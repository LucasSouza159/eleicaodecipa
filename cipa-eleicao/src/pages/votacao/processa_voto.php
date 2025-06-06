<?php
require_once '../../scripts/auth_votacao.php';
require_once '../../scripts/db_connection.php';
require_once '../../scripts/utils.php';

$eleicao_id = $votacao_eleicao_id_logado;     // Definido em auth_votacao.php
$funcionario_id = $votacao_funcionario_id_logado; // Definido em auth_votacao.php

// Defina um SALT global ou por eleição. Por segurança, não deve estar hardcoded diretamente aqui em produção.
// Poderia vir de uma variável de ambiente ou configuração.
define('VOTO_SALT_GLOBAL', 'seu_salt_muito_secreto_e_longo_aqui_@#$!%^');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $candidato_id_post = filter_input(INPUT_POST, 'candidato_id', FILTER_VALIDATE_INT);
    $voto_especial_post = filter_input(INPUT_POST, 'voto_especial', FILTER_SANITIZE_STRING);

    $candidato_id_final = null;
    $tipo_voto_especial_final = null;
    $valor_para_hash = null;

    if ($candidato_id_post) {
        $candidato_id_final = $candidato_id_post;
        $valor_para_hash = (string)$candidato_id_final; // Hash continua baseado no ID do candidato
    } elseif ($voto_especial_post === 'branco') {
        $tipo_voto_especial_final = 'Branco';
        $valor_para_hash = 'branco'; // Para o hash, usamos a string 'branco'
    } elseif ($voto_especial_post === 'nulo') {
        $tipo_voto_especial_final = 'Nulo';
        $valor_para_hash = 'nulo'; // Para o hash, usamos a string 'nulo'
    } else {
        // Tentativa de submissão inválida
        $_SESSION['erro_urna'] = "Opção de voto inválida ao processar."; // Usar uma chave de sessão para urna.php
        header("Location: urna.php");
        exit();
    }

    try {
        // Validação Crítica: Verificar se a eleição ainda está "Em Votação" e dentro do período
        $stmt_check_eleicao = $pdo->prepare("SELECT data_inicio_votacao, data_fim_votacao, status_eleicao FROM eleicoes WHERE id = ?");
        $stmt_check_eleicao->execute([$eleicao_id]);
        $eleicao_db = $stmt_check_eleicao->fetch(PDO::FETCH_ASSOC);

        if (!$eleicao_db || $eleicao_db['status_eleicao'] !== 'Em Votação') {
            registrarLog($pdo, 'WARNING', 'TENTATIVA_VOTO_ELEICAO_NAO_ATIVA', ['eleicao_id' => $eleicao_id, 'funcionario_id' => $funcionario_id, 'status_atual' => $eleicao_db['status_eleicao'] ?? 'N/A'], $funcionario_id, 'Funcionario');
            $_SESSION['erro_login_votacao'] = "Esta eleição não está mais ativa para votação.";
            header("Location: login_votacao.php");
            exit();
        }

        $agora = new DateTime('now', new DateTimeZone('America/Sao_Paulo')); // Usar timezone consistente
        $inicio_votacao = new DateTime($eleicao_db['data_inicio_votacao'], new DateTimeZone('America/Sao_Paulo'));
        $fim_votacao = new DateTime($eleicao_db['data_fim_votacao'], new DateTimeZone('America/Sao_Paulo'));

        if ($agora < $inicio_votacao || $agora > $fim_votacao) {
            registrarLog($pdo, 'WARNING', 'TENTATIVA_VOTO_FORA_PERIODO', ['eleicao_id' => $eleicao_id, 'funcionario_id' => $funcionario_id, 'agora' => $agora->format('Y-m-d H:i:s'), 'inicio' => $inicio_votacao->format('Y-m-d H:i:s'), 'fim' => $fim_votacao->format('Y-m-d H:i:s')], $funcionario_id, 'Funcionario');
            $_SESSION['erro_login_votacao'] = "O período de votação para esta eleição está encerrado ou não começou.";
            header("Location: login_votacao.php");
            exit();
        }

        $stmt_check_voto = $pdo->prepare("SELECT id FROM votos WHERE eleicao_id = ? AND funcionario_id = ?");
        $stmt_check_voto->execute([$eleicao_id, $funcionario_id]);
        if ($stmt_check_voto->fetch()) {
            registrarLog($pdo, 'WARNING', 'TENTATIVA_VOTO_DUPLICADO', ['eleicao_id' => $eleicao_id, 'funcionario_id' => $funcionario_id], $funcionario_id, 'Funcionario');
            $_SESSION['erro_login_votacao'] = "Seu voto já foi registrado anteriormente nesta eleição.";
            unset($_SESSION['votacao_funcionario_id'], $_SESSION['votacao_eleicao_id'], $_SESSION['votacao_nome_funcionario']);
            header("Location: login_votacao.php");
            exit();
        }

        if ($candidato_id_final !== null) {
            $stmt_check_cand = $pdo->prepare("SELECT id FROM candidatos WHERE id = ? AND eleicao_id = ? AND status_candidatura = 'Aprovado'");
            $stmt_check_cand->execute([$candidato_id_final, $eleicao_id]);
            if ($stmt_check_cand->fetch() === false) {
                registrarLog($pdo, 'ERROR', 'TENTATIVA_VOTO_CANDIDATO_INVALIDO', ['eleicao_id' => $eleicao_id, 'funcionario_id' => $funcionario_id, 'candidato_tentado' => $candidato_id_final], $funcionario_id, 'Funcionario');
                $_SESSION['erro_urna'] = "Candidato selecionado é inválido ou não está mais disponível.";
                header("Location: urna.php");
                exit();
            }
        }

        $hash_voto = hash('sha256', $eleicao_id . "_" . $funcionario_id . "_" . $valor_para_hash . "_" . VOTO_SALT_GLOBAL);

        $ip_votante = $_SERVER['REMOTE_ADDR'] ?? null;
        $user_agent_votante = $_SERVER['HTTP_USER_AGENT'] ?? null;

        // Inserir o voto
        $sql_insert_voto = "INSERT INTO votos (eleicao_id, funcionario_id, candidato_id, tipo_voto_especial, hash_voto, ip_votante, user_agent_votante)
                            VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $pdo->prepare($sql_insert_voto);
        $stmt_insert->execute([$eleicao_id, $funcionario_id, $candidato_id_final, $tipo_voto_especial_final, $hash_voto, $ip_votante, $user_agent_votante]);
        $voto_id = $pdo->lastInsertId();

        registrarLog($pdo, 'AUDIT', 'REGISTRO_VOTO_SUCESSO', ['voto_id' => $voto_id, 'eleicao_id' => $eleicao_id, 'funcionario_id' => $funcionario_id, 'voto_em_candidato_id' => $candidato_id_final, 'tipo_especial' => $tipo_voto_especial_final], $funcionario_id, 'Funcionario');

        unset($_SESSION['votacao_funcionario_id']);
        unset($_SESSION['votacao_eleicao_id']);
        unset($_SESSION['votacao_nome_funcionario']);
        unset($_SESSION['votacao_ultima_atividade']);

        $_SESSION['voto_registrado_sucesso'] = true;
        header("Location: agradecimento_voto.php");
        exit();

    } catch (PDOException $e) {
        error_log("Erro PDO ao processar voto (Eleição ID: $eleicao_id, Funcionario ID: $funcionario_id): " . $e->getMessage());
        $log_details = ['eleicao_id' => $eleicao_id, 'funcionario_id' => $funcionario_id, 'erro_pdo' => $e->getMessage()];
        if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) { // UK violation
             $_SESSION['erro_login_votacao'] = "Erro: Seu voto já foi processado (UK).";
             registrarLog($pdo, 'CRITICAL', 'REGISTRO_VOTO_FALHA_DUPLICADO_DB', $log_details, $funcionario_id, 'Funcionario');
        } else {
            $_SESSION['erro_login_votacao'] = "Ocorreu um erro crítico ao registrar seu voto. Contate o suporte.";
            registrarLog($pdo, 'CRITICAL', 'REGISTRO_VOTO_FALHA_DB_GERAL', $log_details, $funcionario_id, 'Funcionario');
        }
        unset($_SESSION['votacao_funcionario_id'], $_SESSION['votacao_eleicao_id'], $_SESSION['votacao_nome_funcionario']);
        header("Location: login_votacao.php");
        exit();
    } catch (Exception $e) {
        error_log("Erro Geral ao processar voto (Eleição ID: $eleicao_id, Funcionario ID: $funcionario_id): " . $e->getMessage());
        registrarLog($pdo, 'CRITICAL', 'REGISTRO_VOTO_FALHA_EXCECAO_GERAL', ['eleicao_id' => $eleicao_id, 'funcionario_id' => $funcionario_id, 'erro' => $e->getMessage()], $funcionario_id, 'Funcionario');
        $_SESSION['erro_login_votacao'] = "Ocorreu um erro inesperado. Contate o suporte.";
        unset($_SESSION['votacao_funcionario_id'], $_SESSION['votacao_eleicao_id'], $_SESSION['votacao_nome_funcionario']);
        header("Location: login_votacao.php");
        exit();
    }

} else {
    registrarLog($pdo ?? null, 'INFO', 'ACESSO_INVALIDO_PROCESSO_VOTO', ['metodo_http' => $_SERVER["REQUEST_METHOD"]], null, 'Sistema');
    $_SESSION['erro_urna'] = "Acesso inválido ao processamento de voto.";
    header("Location: urna.php"); // Ou login_votacao.php se a sessão de urna não existir mais
    exit();
}
?>

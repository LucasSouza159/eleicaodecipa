<?php
require_once '../../scripts/auth_votacao.php'; // Garante autenticação e define variáveis de sessão
require_once '../../scripts/db_connection.php';

$eleicao_id = $votacao_eleicao_id_logado;
$funcionario_id = $votacao_funcionario_id_logado;

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
            $_SESSION['erro_login_votacao'] = "Esta eleição não está mais ativa para votação.";
            header("Location: login_votacao.php"); // Força logout da votação
            exit();
        }
        // Adicionalmente, verificar as datas/horas
        $agora = new DateTime();
        $inicio_votacao = new DateTime($eleicao_db['data_inicio_votacao']);
        $fim_votacao = new DateTime($eleicao_db['data_fim_votacao']);

        if ($agora < $inicio_votacao || $agora > $fim_votacao) {
            $_SESSION['erro_login_votacao'] = "O período de votação para esta eleição está encerrado ou não começou.";
            header("Location: login_votacao.php"); // Força logout da votação
            exit();
        }

        // Validação Crítica: Verificar se o funcionário já não votou (double check)
        $stmt_check_voto = $pdo->prepare("SELECT id FROM votos WHERE eleicao_id = ? AND funcionario_id = ?");
        $stmt_check_voto->execute([$eleicao_id, $funcionario_id]);
        if ($stmt_check_voto->fetch()) {
            $_SESSION['erro_login_votacao'] = "Seu voto já foi registrado anteriormente nesta eleição.";
            // Destruir sessão de votação para evitar confusão
            unset($_SESSION['votacao_funcionario_id'], $_SESSION['votacao_eleicao_id'], $_SESSION['votacao_nome_funcionario']);
            header("Location: login_votacao.php");
            exit();
        }

        // Se for voto em candidato, validar o candidato novamente
        if ($candidato_id_final !== null) {
            $stmt_check_cand = $pdo->prepare("SELECT id FROM candidatos WHERE id = ? AND eleicao_id = ? AND status_candidatura = 'Aprovado'");
            $stmt_check_cand->execute([$candidato_id_final, $eleicao_id]);
            if ($stmt_check_cand->fetch() === false) {
                $_SESSION['erro_urna'] = "Candidato selecionado é inválido ou não está mais disponível.";
                header("Location: urna.php");
                exit();
            }
        }

        // Gerar hash do voto
        $hash_voto = hash('sha256', $eleicao_id . "_" . $funcionario_id . "_" . $valor_para_hash . "_" . VOTO_SALT_GLOBAL);

        $ip_votante = $_SERVER['REMOTE_ADDR'] ?? null;
        $user_agent_votante = $_SERVER['HTTP_USER_AGENT'] ?? null;

        // Inserir o voto
        $sql_insert_voto = "INSERT INTO votos (eleicao_id, funcionario_id, candidato_id, tipo_voto_especial, hash_voto, ip_votante, user_agent_votante)
                            VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $pdo->prepare($sql_insert_voto);
        $stmt_insert->execute([$eleicao_id, $funcionario_id, $candidato_id_final, $tipo_voto_especial_final, $hash_voto, $ip_votante, $user_agent_votante]);

        // Destruir a sessão de votação para finalizar
        unset($_SESSION['votacao_funcionario_id']);
        unset($_SESSION['votacao_eleicao_id']);
        unset($_SESSION['votacao_nome_funcionario']);
        unset($_SESSION['votacao_ultima_atividade']); // Se estiver usando expiração de sessão

        // Guardar uma flag de sucesso para a página de agradecimento
        $_SESSION['voto_registrado_sucesso'] = true;
        header("Location: agradecimento_voto.php");
        exit();

    } catch (PDOException $e) {
        error_log("Erro ao processar voto (Eleição ID: $eleicao_id, Funcionário ID: $funcionario_id): " . $e->getMessage());
        // Verificar se é erro de constraint unique (já votou - embora já verificado acima, é um fallback)
        if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
             $_SESSION['erro_login_votacao'] = "Erro: Seu voto já foi processado anteriormente (código 1062).";
        } else {
            $_SESSION['erro_login_votacao'] = "Ocorreu um erro crítico ao registrar seu voto. Por favor, contate o suporte.";
        }
        // Destruir sessão de votação em caso de erro grave
        unset($_SESSION['votacao_funcionario_id'], $_SESSION['votacao_eleicao_id'], $_SESSION['votacao_nome_funcionario']);
        header("Location: login_votacao.php");
        exit();
    } catch (Exception $e) { // Para exceções gerais
        error_log("Erro geral ao processar voto: " . $e->getMessage());
        $_SESSION['erro_login_votacao'] = "Ocorreu um erro inesperado. Por favor, contate o suporte.";
        unset($_SESSION['votacao_funcionario_id'], $_SESSION['votacao_eleicao_id'], $_SESSION['votacao_nome_funcionario']);
        header("Location: login_votacao.php");
        exit();
    }

} else {
    // Se não for POST, redireciona para a urna ou login
    $_SESSION['erro_urna'] = "Acesso inválido ao processamento de voto.";
    header("Location: urna.php");
    exit();
}
?>

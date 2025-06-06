<?php
session_start();
require_once '../../scripts/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cpf = trim(filter_input(INPUT_POST, 'cpf')); // TODO: Limpar e validar formato CPF
    $data_nascimento_input = trim(filter_input(INPUT_POST, 'data_nascimento'));
    $eleicao_id = filter_input(INPUT_POST, 'eleicao_id', FILTER_VALIDATE_INT);

    // Validar eleicao_id primeiro
    if (!$eleicao_id) {
        // Tentar buscar a primeira eleição "Em Votação" se nenhuma foi explicitamente selecionada
        try {
            $stmt_el = $pdo->query("SELECT id FROM eleicoes WHERE status_eleicao = 'Em Votação' LIMIT 1");
            $eleicao_ativa = $stmt_el->fetch(PDO::FETCH_ASSOC);
            if ($eleicao_ativa) {
                $eleicao_id = $eleicao_ativa['id'];
            } else {
                $_SESSION['erro_login_votacao'] = "Nenhuma eleição disponível para votação no momento ou eleição não especificada.";
                header("Location: login_votacao.php");
                exit();
            }
        } catch (PDOException $e) {
            error_log("Erro ao buscar eleição ativa: " . $e->getMessage());
            $_SESSION['erro_login_votacao'] = "Erro ao determinar a eleição. Tente novamente.";
            header("Location: login_votacao.php");
            exit();
        }
    }

    // Validar dados de entrada
    if (empty($cpf) || empty($data_nascimento_input)) {
        $_SESSION['erro_login_votacao'] = "CPF e Data de Nascimento são obrigatórios.";
        header("Location: login_votacao.php" . ($eleicao_id ? "?eleicao_id=".$eleicao_id : ""));
        exit();
    }

    $dn_obj = DateTime::createFromFormat('Y-m-d', $data_nascimento_input);
    if (!($dn_obj && $dn_obj->format('Y-m-d') === $data_nascimento_input)) {
        $_SESSION['erro_login_votacao'] = "Formato de Data de Nascimento inválido. Use AAAA-MM-DD.";
        header("Location: login_votacao.php" . ($eleicao_id ? "?eleicao_id=".$eleicao_id : ""));
        exit();
    }

    try {
        // Buscar funcionário pelo CPF
        // Precisamos garantir que o funcionário pertença à empresa correta para a eleição
        $stmt_func = $pdo->prepare(
            "SELECT f.id, f.nome_completo, f.data_nascimento, f.status_funcionario, f.permite_votar, f.empresa_id, f.filial_id,
                    e.empresa_id AS eleicao_empresa_id, e.filial_id AS eleicao_filial_id
             FROM funcionarios f
             JOIN eleicoes e ON e.id = :eleicao_id
             WHERE f.cpf = :cpf"
        );
        $stmt_func->bindParam(':cpf', $cpf);
        $stmt_func->bindParam(':eleicao_id', $eleicao_id, PDO::PARAM_INT);
        $stmt_func->execute();
        $funcionario = $stmt_func->fetch(PDO::FETCH_ASSOC);

        if (!$funcionario) {
            $_SESSION['erro_login_votacao'] = "CPF não encontrado.";
            header("Location: login_votacao.php" . ($eleicao_id ? "?eleicao_id=".$eleicao_id : ""));
            exit();
        }

        // Verificar se o funcionário pertence à empresa/filial da eleição
        if ($funcionario['empresa_id'] != $funcionario['eleicao_empresa_id']) {
            $_SESSION['erro_login_votacao'] = "Funcionário não pertence à empresa desta eleição.";
            header("Location: login_votacao.php" . ($eleicao_id ? "?eleicao_id=".$eleicao_id : ""));
            exit();
        }
        // Se a eleição for de uma filial específica, o funcionário deve ser daquela filial ou a eleição não ter filial (geral da empresa)
        if ($funcionario['eleicao_filial_id'] !== null && $funcionario['filial_id'] != $funcionario['eleicao_filial_id']) {
             $_SESSION['erro_login_votacao'] = "Funcionário não pertence à filial desta eleição.";
            header("Location: login_votacao.php" . ($eleicao_id ? "?eleicao_id=".$eleicao_id : ""));
            exit();
        }


        // Verificar Data de Nascimento
        if ($funcionario['data_nascimento'] != $data_nascimento_input) {
            $_SESSION['erro_login_votacao'] = "Data de Nascimento incorreta.";
            header("Location: login_votacao.php" . ($eleicao_id ? "?eleicao_id=".$eleicao_id : ""));
            exit();
        }

        // Verificar status e permissão de voto
        if ($funcionario['status_funcionario'] !== 'Ativo') {
            $_SESSION['erro_login_votacao'] = "Funcionário não está ativo.";
            header("Location: login_votacao.php" . ($eleicao_id ? "?eleicao_id=".$eleicao_id : ""));
            exit();
        }
        if (!$funcionario['permite_votar']) {
            $_SESSION['erro_login_votacao'] = "Este funcionário não tem permissão para votar nesta eleição.";
            header("Location: login_votacao.php" . ($eleicao_id ? "?eleicao_id=".$eleicao_id : ""));
            exit();
        }

        // Verificar se já votou
        $stmt_check_voto = $pdo->prepare("SELECT id FROM votos WHERE eleicao_id = ? AND funcionario_id = ?");
        $stmt_check_voto->execute([$eleicao_id, $funcionario['id']]);
        if ($stmt_check_voto->fetch()) {
            $_SESSION['erro_login_votacao'] = "Este funcionário já votou nesta eleição.";
            header("Location: login_votacao.php" . ($eleicao_id ? "?eleicao_id=".$eleicao_id : ""));
            exit();
        }

        // Login bem-sucedido para votação
        $_SESSION['votacao_funcionario_id'] = $funcionario['id'];
        $_SESSION['votacao_eleicao_id'] = $eleicao_id;
        $_SESSION['votacao_nome_funcionario'] = $funcionario['nome_completo'];
        // Poderia adicionar mais dados da eleição na sessão se necessário para a urna

        session_regenerate_id(true); // Segurança
        header("Location: urna.php"); // Redirecionar para a página da urna
        exit();

    } catch (PDOException $e) {
        error_log("Erro no login para votação: " . $e->getMessage());
        $_SESSION['erro_login_votacao'] = "Erro no sistema durante o login. Tente novamente.";
        header("Location: login_votacao.php" . ($eleicao_id ? "?eleicao_id=".$eleicao_id : ""));
        exit();
    }

} else {
    header("Location: login_votacao.php");
    exit();
}
?>

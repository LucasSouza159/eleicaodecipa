<?php
require_once '../../scripts/auth_comissao.php'; // Garante autenticação e define $comissao_eleicao_id_logado
require_once '../../scripts/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eleicao_id_sessao = $comissao_eleicao_id_logado; // ID da eleição da sessão da comissão
    $eleicao_id_post = filter_input(INPUT_POST, 'eleicao_id', FILTER_VALIDATE_INT);

    if (!$eleicao_id_post || $eleicao_id_post != $eleicao_id_sessao) {
        $_SESSION['mensagem_erro_apuracao'] = "ID da eleição inválido ou não corresponde à sua sessão.";
        header("Location: apuracao_resultados.php"); // Redireciona para a página de apuração principal
        exit();
    }

    try {
        // Buscar eleição para garantir que pertence à comissão e verificar o status
        $stmt_check = $pdo->prepare("SELECT status_eleicao FROM eleicoes WHERE id = ?");
        $stmt_check->execute([$eleicao_id_post]);
        $eleicao = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if (!$eleicao) {
            $_SESSION['mensagem_erro_apuracao'] = "Eleição não encontrada.";
            header("Location: apuracao_resultados.php");
            exit();
        }

        // Permite encerrar se estiver 'Em Votação'
        if ($eleicao['status_eleicao'] === 'Em Votação') {
            // Atualizar o status da eleição para 'Votação Encerrada'
            // e data_fim_votacao para o momento atual, se desejado (ou manter a programada).
            // Para simplificar, apenas mudamos o status. A data_fim_votacao original é mantida.
            // Se quisesse forçar o encerramento antes, poderia atualizar data_fim_votacao = NOW()
            $stmt_update = $pdo->prepare("UPDATE eleicoes SET status_eleicao = 'Votação Encerrada' WHERE id = ?");
            $stmt_update->execute([$eleicao_id_post]);

            if ($stmt_update->rowCount() > 0) {
                $_SESSION['mensagem_sucesso_apuracao'] = "Votação encerrada com sucesso. A apuração pode ser iniciada.";
            } else {
                $_SESSION['mensagem_erro_apuracao'] = "Não foi possível encerrar a votação ou ela já estava encerrada.";
            }
        } else {
            $_SESSION['mensagem_erro_apuracao'] = "A votação não pode ser encerrada pois não está 'Em Votação' (Status atual: " . $eleicao['status_eleicao'] . ").";
        }

    } catch (PDOException $e) {
        error_log("Erro ao encerrar votação (Eleição ID: $eleicao_id_post): " . $e->getMessage());
        $_SESSION['mensagem_erro_apuracao'] = "Ocorreu um erro de banco de dados ao tentar encerrar a votação.";
    }
    header("Location: apuracao_resultados.php");
    exit();

} else {
    // Se não for POST, redireciona
    $_SESSION['mensagem_erro_apuracao'] = "Ação não permitida.";
    header("Location: apuracao_resultados.php");
    exit();
}
?>

<?php
require_once '../../scripts/auth_comissao.php'; // Garante autenticação e define $comissao_eleicao_id_logado
require_once '../../scripts/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eleicao_id_sessao = $comissao_eleicao_id_logado; // ID da eleição da sessão da comissão
    $eleicao_id_post = filter_input(INPUT_POST, 'eleicao_id', FILTER_VALIDATE_INT);

    if (!$eleicao_id_post || $eleicao_id_post != $eleicao_id_sessao) {
        $_SESSION['mensagem_erro_apuracao'] = "ID da eleição inválido ou não corresponde à sua sessão.";
        header("Location: apuracao_resultados.php");
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

        // Permite publicar se estiver 'Votação Encerrada' ou 'Em Apuração'
        // Poderia adicionar lógica para verificar se a apuração foi de fato concluída (ex: um campo 'data_apuracao_concluida')
        if (in_array($eleicao['status_eleicao'], ['Votação Encerrada', 'Em Apuração'])) {
            $stmt_update = $pdo->prepare("UPDATE eleicoes SET status_eleicao = 'Resultados Publicados' WHERE id = ?");
            $stmt_update->execute([$eleicao_id_post]);

            if ($stmt_update->rowCount() > 0) {
                $_SESSION['mensagem_sucesso_apuracao'] = "Resultados publicados com sucesso!";
                // TODO: Adicionar lógica para notificar envolvidos, se necessário.
            } else {
                $_SESSION['mensagem_erro_apuracao'] = "Não foi possível publicar os resultados ou já estavam publicados.";
            }
        } else {
            $_SESSION['mensagem_erro_apuracao'] = "Os resultados não podem ser publicados (Status atual: " . $eleicao['status_eleicao'] . ").";
        }

    } catch (PDOException $e) {
        error_log("Erro ao publicar resultados (Eleição ID: $eleicao_id_post): " . $e->getMessage());
        $_SESSION['mensagem_erro_apuracao'] = "Ocorreu um erro de banco de dados ao tentar publicar os resultados.";
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

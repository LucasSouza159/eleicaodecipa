<?php
require_once '../../scripts/auth_empresa.php';
require_once '../../scripts/db_connection.php';

$empresa_id_logada = $_SESSION['empresa_id'];

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $membro_id = filter_input(INPUT_GET, 'membro_id', FILTER_VALIDATE_INT);
    $eleicao_id = filter_input(INPUT_GET, 'eleicao_id', FILTER_VALIDATE_INT);

    if (!$membro_id || !$eleicao_id) {
        $_SESSION['mensagem_erro'] = "IDs inválidos para remoção.";
        header("Location: gerenciar_eleicoes.php"); // Redireciona para a lista geral
        exit();
    }

    try {
        // Primeiro, verificar se a eleição pertence à empresa logada
        $stmt_check_eleicao = $pdo->prepare(
            "SELECT e.id
             FROM comissao c
             JOIN eleicoes e ON c.eleicao_id = e.id
             WHERE c.id = ? AND e.id = ? AND e.empresa_id = ?"
        );
        $stmt_check_eleicao->execute([$membro_id, $eleicao_id, $empresa_id_logada]);

        if ($stmt_check_eleicao->fetch() === false) {
            $_SESSION['mensagem_erro'] = "Membro não encontrado ou não pertence a uma eleição da sua empresa.";
            header("Location: gerenciar_comissao_eleitoral.php?eleicao_id=" . $eleicao_id);
            exit();
        }

        // Se a verificação passar, remover o membro
        $stmt_delete = $pdo->prepare("DELETE FROM comissao WHERE id = ? AND eleicao_id = ?");
        $stmt_delete->execute([$membro_id, $eleicao_id]);

        if ($stmt_delete->rowCount() > 0) {
            $_SESSION['mensagem_sucesso'] = "Membro da comissão removido com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Não foi possível remover o membro ou o membro já havia sido removido.";
        }
        header("Location: gerenciar_comissao_eleitoral.php?eleicao_id=" . $eleicao_id);
        exit();

    } catch (PDOException $e) {
        error_log("Erro ao remover membro da comissão: " . $e->getMessage());
        $_SESSION['mensagem_erro'] = "Erro de banco de dados ao tentar remover membro. Tente novamente.";
        header("Location: gerenciar_comissao_eleitoral.php?eleicao_id=" . $eleicao_id);
        exit();
    }

} else {
    // Redirecionar se não for GET
    $_SESSION['mensagem_erro'] = "Ação não permitida.";
    header("Location: gerenciar_eleicoes.php");
    exit();
}
?>

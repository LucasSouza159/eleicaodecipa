<?php
require_once '../../scripts/auth_comissao.php'; // Garante autenticação e define $comissao_eleicao_id_logado
require_once '../../scripts/db_connection.php';

$eleicao_id_comissao = $comissao_eleicao_id_logado; // Vem de auth_comissao.php

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $candidato_id = filter_input(INPUT_GET, 'candidato_id', FILTER_VALIDATE_INT);
    $novo_status = trim(filter_input(INPUT_GET, 'novo_status'));

    if (!$candidato_id) {
        $_SESSION['mensagem_erro_candidato'] = "ID da candidatura inválido para alterar status.";
        header("Location: gerenciar_candidatos.php");
        exit();
    }

    // Validar se a candidatura pertence à eleição da comissão logada
    try {
        $stmt_check_owner = $pdo->prepare("SELECT id FROM candidatos WHERE id = ? AND eleicao_id = ?");
        $stmt_check_owner->execute([$candidato_id, $eleicao_id_comissao]);
        if ($stmt_check_owner->fetch() === false) {
            $_SESSION['mensagem_erro_candidato'] = "Candidatura não encontrada ou não pertence à sua eleição.";
            header("Location: gerenciar_candidatos.php");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Erro ao verificar propriedade da candidatura (alterar status): " . $e->getMessage());
        $_SESSION['mensagem_erro_candidato'] = "Erro de banco de dados ao verificar candidatura.";
        header("Location: gerenciar_candidatos.php");
        exit();
    }

    // Validar o novo status
    $status_permitidos = ['Inscrito', 'Aprovado', 'Reprovado', 'Eleito', 'Suplente', 'Não Eleito']; // Adicionar outros se necessário
    if (empty($novo_status) || !in_array($novo_status, $status_permitidos)) {
        $_SESSION['mensagem_erro_candidato'] = "Novo status inválido para a candidatura: " . htmlspecialchars($novo_status);
        header("Location: gerenciar_candidatos.php");
        exit();
    }

    // Lógica de transição de status (exemplo simples)
    // Poderia ser mais complexa, ex: não pode ir de 'Reprovado' para 'Eleito' diretamente.
    // Por agora, permitimos a mudança direta se o status for válido.

    try {
        $sql = "UPDATE candidatos SET status_candidatura = ? WHERE id = ? AND eleicao_id = ?";
        $stmt_update = $pdo->prepare($sql);
        $stmt_update->execute([$novo_status, $candidato_id, $eleicao_id_comissao]);

        if ($stmt_update->rowCount() > 0) {
            $_SESSION['mensagem_sucesso_candidato'] = "Status da candidatura atualizado para '" . htmlspecialchars($novo_status) . "' com sucesso!";
        } else {
            $_SESSION['mensagem_erro_candidato'] = "Não foi possível atualizar o status da candidatura ou o status já era o mesmo.";
        }
        header("Location: gerenciar_candidatos.php");
        exit();

    } catch (PDOException $e) {
        error_log("Erro ao alterar status da candidatura: " . $e->getMessage());
        $_SESSION['mensagem_erro_candidato'] = "Erro ao atualizar status no banco de dados.";
        header("Location: gerenciar_candidatos.php");
        exit();
    }

} else {
    // Se não for GET, redireciona
    $_SESSION['mensagem_erro_candidato'] = "Ação não permitida.";
    header("Location: gerenciar_candidatos.php");
    exit();
}
?>

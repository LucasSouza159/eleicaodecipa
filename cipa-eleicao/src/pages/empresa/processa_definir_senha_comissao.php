<?php
require_once '../../scripts/auth_empresa.php';
require_once '../../scripts/db_connection.php';

$empresa_id_logada = $_SESSION['empresa_id'];
$erros = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $membro_id = filter_input(INPUT_POST, 'membro_id', FILTER_VALIDATE_INT);
    $eleicao_id = filter_input(INPUT_POST, 'eleicao_id', FILTER_VALIDATE_INT);
    $nova_senha = $_POST['nova_senha'] ?? '';
    $confirma_nova_senha = $_POST['confirma_nova_senha'] ?? '';

    if (!$membro_id || !$eleicao_id) {
        $_SESSION['mensagem_erro'] = "IDs inválidos para processar a senha.";
        header("Location: gerenciar_eleicoes.php");
        exit();
    }

    // Validar se a eleição e o membro pertencem à empresa logada
    try {
        $stmt_check = $pdo->prepare(
            "SELECT c.id FROM comissao c
             JOIN eleicoes e ON c.eleicao_id = e.id
             WHERE c.id = :membro_id AND e.id = :eleicao_id AND e.empresa_id = :empresa_id"
        );
        $stmt_check->bindParam(':membro_id', $membro_id, PDO::PARAM_INT);
        $stmt_check->bindParam(':eleicao_id', $eleicao_id, PDO::PARAM_INT);
        $stmt_check->bindParam(':empresa_id', $empresa_id_logada, PDO::PARAM_INT);
        $stmt_check->execute();
        if ($stmt_check->fetch() === false) {
            $_SESSION['mensagem_erro'] = "Membro/Eleição não encontrado ou não pertence à sua empresa.";
            header("Location: gerenciar_comissao_eleitoral.php?eleicao_id=" . $eleicao_id);
            exit();
        }
    } catch (PDOException $e) {
        error_log("Erro ao validar membro/eleição para definir senha: " . $e->getMessage());
        $_SESSION['erros_definir_senha_comissao'] = ["Erro de banco de dados. Tente novamente."];
        header("Location: definir_senha_comissao.php?membro_id=" . $membro_id . "&eleicao_id=" . $eleicao_id);
        exit();
    }

    // Validações da senha
    if (empty($nova_senha)) {
        $erros[] = "O campo Nova Senha é obrigatório.";
    }
    if (empty($confirma_nova_senha)) {
        $erros[] = "O campo Confirmar Nova Senha é obrigatório.";
    }
    if ($nova_senha !== $confirma_nova_senha) {
        $erros[] = "As senhas não coincidem.";
    }
    // TODO: Adicionar requisitos de complexidade de senha (ex: mínimo 6 caracteres)
    if (empty($erros) && strlen($nova_senha) < 6) {
        $erros[] = "A senha deve ter pelo menos 6 caracteres.";
    }


    if (empty($erros)) {
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        try {
            $stmt_update = $pdo->prepare("UPDATE comissao SET senha_hash_comissao = ? WHERE id = ? AND eleicao_id = ?");
            $stmt_update->execute([$senha_hash, $membro_id, $eleicao_id]);

            $_SESSION['mensagem_sucesso'] = "Senha para o membro da comissão definida com sucesso!";
            header("Location: gerenciar_comissao_eleitoral.php?eleicao_id=" . $eleicao_id);
            exit();

        } catch (PDOException $e) {
            error_log("Erro ao definir senha para membro da comissão: " . $e->getMessage());
            $_SESSION['erros_definir_senha_comissao'] = ["Erro ao salvar a senha no banco de dados. Detalhe: " . $e->getMessage()];
            header("Location: definir_senha_comissao.php?membro_id=" . $membro_id . "&eleicao_id=" . $eleicao_id);
            exit();
        }
    } else {
        $_SESSION['erros_definir_senha_comissao'] = $erros;
        header("Location: definir_senha_comissao.php?membro_id=" . $membro_id . "&eleicao_id=" . $eleicao_id);
        exit();
    }

} else {
    header("Location: gerenciar_eleicoes.php"); // Redireciona se não for POST
    exit();
}
?>

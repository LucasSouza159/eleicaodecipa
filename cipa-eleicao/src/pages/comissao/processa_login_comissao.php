<?php
session_start();
require_once '../../scripts/db_connection.php'; // Ajuste o caminho conforme sua estrutura

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $_SESSION['erro_login_comissao'] = "Email e Senha são obrigatórios.";
        $_SESSION['login_comissao_email_tentativa'] = $email; // Para repopular
        header("Location: login_comissao.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare(
            "SELECT c.id, c.eleicao_id, c.nome_completo, c.papel_comissao, c.senha_hash_comissao, e.titulo_eleicao
             FROM comissao c
             JOIN eleicoes e ON c.eleicao_id = e.id
             WHERE c.email = :email AND c.senha_hash_comissao IS NOT NULL"
        );
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $membro = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($membro && password_verify($senha, $membro['senha_hash_comissao'])) {
            // Login bem-sucedido
            $_SESSION['comissao_id'] = $membro['id'];
            $_SESSION['comissao_eleicao_id'] = $membro['eleicao_id'];
            $_SESSION['comissao_nome_membro'] = $membro['nome_completo'];
            $_SESSION['comissao_papel'] = $membro['papel_comissao'];
            $_SESSION['comissao_eleicao_titulo'] = $membro['titulo_eleicao']; // Guardar título para fácil acesso

            // Regenerar ID da sessão para segurança
            session_regenerate_id(true);

            unset($_SESSION['login_comissao_email_tentativa']);
            header("Location: painel_comissao.php");
            exit();
        } else {
            // Credenciais inválidas ou senha não definida
            $_SESSION['erro_login_comissao'] = "Email ou Senha inválidos, ou acesso não configurado.";
            $_SESSION['login_comissao_email_tentativa'] = $email;
            header("Location: login_comissao.php");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Erro de login da comissão: " . $e->getMessage());
        $_SESSION['erro_login_comissao'] = "Erro no sistema ao tentar fazer login. Tente novamente mais tarde.";
        $_SESSION['login_comissao_email_tentativa'] = $email;
        header("Location: login_comissao.php");
        exit();
    }

} else {
    // Redirecionar se não for POST
    header("Location: login_comissao.php");
    exit();
}
?>

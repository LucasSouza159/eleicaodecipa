<?php
session_start();
require_once '../../scripts/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $_SESSION['erro_login_admin'] = "Email e Senha são obrigatórios.";
        header("Location: login_admin.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare(
            "SELECT id, nome_completo, email, senha_hash, nivel_acesso, status_admin
             FROM administradores
             WHERE email = :email"
        );
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && $admin['status_admin'] === 'Ativo' && password_verify($senha, $admin['senha_hash'])) {
            // Login bem-sucedido
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_nome'] = $admin['nome_completo'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_nivel_acesso'] = $admin['nivel_acesso'];

            // Atualizar ultimo_login
            $stmt_update_login = $pdo->prepare("UPDATE administradores SET ultimo_login = NOW() WHERE id = ?");
            $stmt_update_login->execute([$admin['id']]);

            session_regenerate_id(true); // Segurança

            header("Location: painel_admin.php");
            exit();
        } else {
            // Credenciais inválidas ou admin inativo
            if ($admin && $admin['status_admin'] === 'Inativo') {
                $_SESSION['erro_login_admin'] = "Esta conta de administrador está inativa.";
            } else {
                $_SESSION['erro_login_admin'] = "Email ou Senha inválidos.";
            }
            header("Location: login_admin.php");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Erro de login do administrador: " . $e->getMessage());
        $_SESSION['erro_login_admin'] = "Erro no sistema durante o login. Tente novamente mais tarde.";
        header("Location: login_admin.php");
        exit();
    }

} else {
    // Redirecionar se não for POST
    header("Location: login_admin.php");
    exit();
}
?>

<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login da Comissão Eleitoral</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background-color: #f4f4f4; }
        .login-container { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 300px; }
        .login-container h2 { text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .button:hover { background-color: #0056b3; }
        .mensagem { padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align:center; }
        .erro { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .sucesso { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .footer-link { text-align: center; margin-top: 15px; font-size: 0.9em; }
        .footer-link a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Comissão Eleitoral</h2>

        <?php
        if (isset($_SESSION['erro_login_comissao'])) {
            echo "<div class='mensagem erro'>" . htmlspecialchars($_SESSION['erro_login_comissao']) . "</div>";
            unset($_SESSION['erro_login_comissao']);
        }
        if (isset($_SESSION['mensagem_logout_comissao'])) {
            echo "<div class='mensagem sucesso'>" . htmlspecialchars($_SESSION['mensagem_logout_comissao']) . "</div>";
            unset($_SESSION['mensagem_logout_comissao']);
        }
        ?>

        <form action="processa_login_comissao.php" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_SESSION['login_comissao_email_tentativa'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <button type="submit" class="button">Entrar</button>
        </form>
        <div class="footer-link">
            <p><a href="../empresa/login_empresa.php">Acessar como Empresa</a></p>
            <p><a href="../../index.php">Página Inicial</a></p>
        </div>
    </div>
    <?php unset($_SESSION['login_comissao_email_tentativa']); ?>
</body>
</html>

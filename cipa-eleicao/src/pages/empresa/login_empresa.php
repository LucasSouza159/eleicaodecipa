<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login da Empresa</title>
    <style>
        .mensagem { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .sucesso { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .erro { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <h2>Login da Empresa</h2>

    <?php
    // Exibir mensagem de sucesso do cadastro
    if (isset($_SESSION['mensagem_sucesso'])) {
        echo "<div class='mensagem sucesso'>" . htmlspecialchars($_SESSION['mensagem_sucesso']) . "</div>";
        unset($_SESSION['mensagem_sucesso']); // Limpar após exibir
    }

    // Exibir mensagem de erro do login
    if (isset($_SESSION['erro_login'])) {
        echo "<div class='mensagem erro'>" . htmlspecialchars($_SESSION['erro_login']) . "</div>";
        unset($_SESSION['erro_login']); // Limpar após exibir
    }

    // Exibir mensagem de erro de acesso (do auth_empresa.php)
    if (isset($_SESSION['erro_acesso'])) {
        echo "<div class='mensagem erro'>" . htmlspecialchars($_SESSION['erro_acesso']) . "</div>";
        unset($_SESSION['erro_acesso']); // Limpar após exibir
    }
    ?>

    <form action="processa_login_empresa.php" method="POST">
        <div>
            <label for="login">CNPJ ou Email:</label>
            <input type="text" id="login" name="login" value="<?php echo htmlspecialchars($_SESSION['dados_formulario_login']['login'] ?? ''); ?>" required>
        </div>
        <br>
        <div>
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
        </div>
        <br>
        <button type="submit">Entrar</button>
    </form>
    <p>Não tem uma conta? <a href="cadastro_empresa.php">Cadastre-se aqui</a>.</p>

    <?php
    // Limpar dados de formulário de login da sessão, se existirem
    if (isset($_SESSION['dados_formulario_login'])) {
        unset($_SESSION['dados_formulario_login']);
    }
    ?>
</body>
</html>

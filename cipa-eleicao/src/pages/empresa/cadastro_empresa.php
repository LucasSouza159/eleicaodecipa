<?php
session_start();

// Recuperar dados do formulário e erros da sessão, se existirem
$erros = $_SESSION['erros_cadastro'] ?? [];
$dados_formulario = $_SESSION['dados_formulario_cadastro'] ?? [];

// Limpar da sessão para não exibir novamente em recarregamentos
unset($_SESSION['erros_cadastro']);
unset($_SESSION['dados_formulario_cadastro']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Empresa</title>
    <style>
        .erro-lista { list-style-type: none; padding: 0; margin: 0 0 15px 0; }
        .erro-lista li { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 5px; border-radius: 5px; }
    </style>
</head>
<body>
    <h2>Cadastro de Nova Empresa</h2>

    <?php if (!empty($erros)): ?>
        <ul class="erro-lista">
            <?php foreach ($erros as $erro): ?>
                <li><?php echo htmlspecialchars($erro); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form action="processa_cadastro_empresa.php" method="POST">
        <div>
            <label for="nome_fantasia">Nome Fantasia:</label>
            <input type="text" id="nome_fantasia" name="nome_fantasia" value="<?php echo htmlspecialchars($dados_formulario['nome_fantasia'] ?? ''); ?>" required>
        </div>
        <br>
        <div>
            <label for="razao_social">Razão Social:</label>
            <input type="text" id="razao_social" name="razao_social" value="<?php echo htmlspecialchars($dados_formulario['razao_social'] ?? ''); ?>" required>
        </div>
        <br>
        <div>
            <label for="cnpj">CNPJ:</label>
            <input type="text" id="cnpj" name="cnpj" value="<?php echo htmlspecialchars($dados_formulario['cnpj'] ?? ''); ?>" required> <!-- TODO: Adicionar máscara e validação de formato -->
        </div>
        <br>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($dados_formulario['email'] ?? ''); ?>" required>
        </div>
        <br>
        <div>
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
        </div>
        <br>
        <div>
            <label for="confirma_senha">Confirmar Senha:</label>
            <input type="password" id="confirma_senha" name="confirma_senha" required>
        </div>
        <br>
        <button type="submit">Cadastrar</button>
    </form>
    <p>Já tem uma conta? <a href="login_empresa.php">Faça login aqui</a>.</p>
</body>
</html>

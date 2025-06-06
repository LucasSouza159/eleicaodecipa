<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login da Empresa - CIPA Fácil</title>
    <link href="../../src/styles/output.css" rel="stylesheet">
    <style>
        /* Adicionado para garantir que o html e body ocupem toda a altura, permitindo centralização vertical */
        html, body {
            height: 100%;
            margin: 0;
        }
    </style>
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center font-sans">

    <div class="w-full max-w-md p-8 space-y-6 bg-white shadow-xl rounded-lg">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-roxo-principal">CIPA Fácil</h1>
            <h2 class="text-2xl font-semibold text-cinza-chumbo mt-2">Login da Empresa</h2>
        </div>

        <?php
        // Exibir mensagem de sucesso do cadastro
        if (isset($_SESSION['mensagem_sucesso'])) {
            echo "<div class='p-4 mb-4 text-sm text-green-700 bg-green-100 border border-green-400 rounded-lg' role='alert'>" . htmlspecialchars($_SESSION['mensagem_sucesso']) . "</div>";
            unset($_SESSION['mensagem_sucesso']); // Limpar após exibir
        }

        // Exibir mensagem de erro do login
        if (isset($_SESSION['erro_login'])) {
            echo "<div class='p-4 mb-4 text-sm text-red-700 bg-red-100 border border-red-400 rounded-lg' role='alert'>" . htmlspecialchars($_SESSION['erro_login']) . "</div>";
            unset($_SESSION['erro_login']); // Limpar após exibir
        }

        // Exibir mensagem de erro de acesso (do auth_empresa.php)
        if (isset($_SESSION['erro_acesso'])) {
            echo "<div class='p-4 mb-4 text-sm text-yellow-700 bg-yellow-100 border border-yellow-400 rounded-lg' role='alert'>" . htmlspecialchars($_SESSION['erro_acesso']) . "</div>";
            unset($_SESSION['erro_acesso']); // Limpar após exibir
        }
        ?>

        <form action="processa_login_empresa.php" method="POST" class="space-y-6">
            <div>
                <label for="login" class="block text-sm font-medium text-gray-700">CNPJ ou Email:</label>
                <input type="text" id="login" name="login"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm"
                       value="<?php echo htmlspecialchars($_SESSION['dados_formulario_login']['login'] ?? ''); ?>" required
                       placeholder="Seu CNPJ ou Email">
            </div>

            <div>
                <label for="senha" class="block text-sm font-medium text-gray-700">Senha:</label>
                <input type="password" id="senha" name="senha"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm"
                       required placeholder="Sua senha">
            </div>

            <div>
                <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-roxo-principal hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-roxo-principal">
                    Entrar
                </button>
            </div>
        </form>
        <p class="text-sm text-center text-gray-600">
            Não tem uma conta?
            <a href="cadastro_empresa.php" class="font-medium text-roxo-principal hover:text-purple-700">
                Cadastre-se aqui
            </a>
        </p>
        <p class="text-sm text-center">
            <a href="../../index.php" class="font-medium text-gray-600 hover:text-roxo-principal">
                &larr; Voltar à Página Inicial
            </a>
        </p>
    </div>

    <?php
    if (isset($_SESSION['dados_formulario_login'])) {
        unset($_SESSION['dados_formulario_login']);
    }
    ?>
</body>
</html>

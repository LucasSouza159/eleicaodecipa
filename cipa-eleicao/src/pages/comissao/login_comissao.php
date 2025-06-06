<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login da Comissão Eleitoral - CIPA Fácil</title>
    <link href="../../src/styles/output.css" rel="stylesheet">
    <style>
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
            <h2 class="text-2xl font-semibold text-cinza-chumbo mt-2">Login da Comissão Eleitoral</h2>
        </div>

        <?php
        if (isset($_SESSION['erro_login_comissao'])) {
            echo "<div class='p-4 mb-4 text-sm text-red-700 bg-red-100 border border-red-400 rounded-lg' role='alert'>" . htmlspecialchars($_SESSION['erro_login_comissao']) . "</div>";
            unset($_SESSION['erro_login_comissao']);
        }
        if (isset($_SESSION['mensagem_logout_comissao'])) {
            echo "<div class='p-4 mb-4 text-sm text-green-700 bg-green-100 border border-green-400 rounded-lg' role='alert'>" . htmlspecialchars($_SESSION['mensagem_logout_comissao']) . "</div>";
            unset($_SESSION['mensagem_logout_comissao']);
        }
         if (isset($_SESSION['erro_acesso_comissao'])) { // Vindo de auth_comissao.php
            echo "<div class='p-4 mb-4 text-sm text-yellow-700 bg-yellow-100 border border-yellow-400 rounded-lg' role='alert'>" . htmlspecialchars($_SESSION['erro_acesso_comissao']) . "</div>";
            unset($_SESSION['erro_acesso_comissao']);
        }
        ?>

        <form action="processa_login_comissao.php" method="POST" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                <input type="email" id="email" name="email" required
                       value="<?php echo htmlspecialchars($_SESSION['login_comissao_email_tentativa'] ?? ''); ?>"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm"
                       placeholder="seu.email@comissao.com">
            </div>
            <div>
                <label for="senha" class="block text-sm font-medium text-gray-700">Senha:</label>
                <input type="password" id="senha" name="senha" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm"
                       placeholder="Sua senha">
            </div>
            <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-roxo-principal hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-roxo-principal">
                Entrar
            </button>
        </form>
        <div class="text-sm text-center">
            <p><a href="../empresa/login_empresa.php" class="font-medium text-gray-600 hover:text-roxo-principal">Acessar como Empresa</a></p>
            <p class="mt-2"><a href="../../index.php" class="font-medium text-gray-600 hover:text-roxo-principal">&larr; Voltar à Página Inicial</a></p>
        </div>
    </div>
    <?php unset($_SESSION['login_comissao_email_tentativa']); ?>
</body>
</html>

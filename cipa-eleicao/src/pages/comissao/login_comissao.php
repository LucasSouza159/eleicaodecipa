<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login da Comissão | CIPA Fácil</title>
    <link href="../../src/styles/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 sm:p-10 rounded-xl shadow-2xl">
            <div>
                <img class="mx-auto h-16 w-auto" src="../../assets/images/logo_cipa_facil_sm.png" alt="CIPA Fácil Logo">
                <!-- TODO: Adicionar um logo em src/assets/images/ -->
                <h2 class="mt-6 text-center text-3xl font-extrabold text-cinza-chumbo">
                    Acesso da Comissão Eleitoral
                </h2>
                 <p class="mt-2 text-center text-sm text-gray-600">
                    Use suas credenciais para gerenciar a eleição.
                </p>
            </div>

            <?php
            if (isset($_SESSION['erro_login_comissao'])) {
                echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 text-sm' role='alert'>" . htmlspecialchars($_SESSION['erro_login_comissao']) . "</div>";
                unset($_SESSION['erro_login_comissao']);
            }
            if (isset($_SESSION['mensagem_logout_comissao'])) {
                echo "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 text-sm' role='alert'>" . htmlspecialchars($_SESSION['mensagem_logout_comissao']) . "</div>";
                unset($_SESSION['mensagem_logout_comissao']);
            }
             if (isset($_SESSION['erro_acesso_comissao'])) {
                echo "<div class='bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 text-sm' role='alert'>" . htmlspecialchars($_SESSION['erro_acesso_comissao']) . "</div>";
                unset($_SESSION['erro_acesso_comissao']);
            }
            ?>

            <form class="mt-8 space-y-6" action="processa_login_comissao.php" method="POST">
                <div class="rounded-md shadow-sm space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email:</label>
                        <input id="email" name="email" type="email" autocomplete="email" required
                               value="<?php echo htmlspecialchars($_SESSION['login_comissao_email_tentativa'] ?? ''); ?>"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-azul-cipa focus:border-azul-cipa sm:text-sm"
                               placeholder="seu.email@comissao.com">
                    </div>
                    <div>
                        <label for="senha" class="block text-sm font-medium text-gray-700 mb-1">Senha:</label>
                        <input id="senha" name="senha" type="password" autocomplete="current-password" required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-azul-cipa focus:border-azul-cipa sm:text-sm"
                               placeholder="Sua senha">
                    </div>
                </div>
                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-azul-cipa hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-azul-cipa">
                        Entrar
                    </button>
                </div>
            </form>
            <div class="mt-6 text-center text-sm">
                <p class="mb-2">
                    <a href="../empresa/login_empresa.php" class="font-medium text-gray-600 hover:text-azul-cipa">
                        Acessar como Empresa
                    </a>
                </p>
                <p>
                    <a href="../../index.php" class="font-medium text-gray-600 hover:text-azul-cipa">
                        &larr; Voltar para a página inicial
                    </a>
                </p>
            </div>
        </div>
    </div>
    <?php unset($_SESSION['login_comissao_email_tentativa']); ?>
</body>
</html>

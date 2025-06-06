<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login da Empresa | CIPA Fácil</title>
    <link href="../../src/styles/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 sm:p-10 rounded-xl shadow-2xl">
            <div>
                <img class="mx-auto h-16 w-auto" src="../../assets/images/logo_cipa_facil_sm.png" alt="CIPA Fácil Logo">
                <!-- TODO: Adicionar um logo em src/assets/images/ -->
                <h2 class="mt-6 text-center text-3xl font-extrabold text-cinza-chumbo">
                    Acesse sua Conta Empresarial
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Ou
                    <a href="cadastro_empresa.php" class="font-medium text-roxo-principal hover:text-purple-700">
                        crie uma nova conta gratuitamente
                    </a>
                </p>
            </div>

            <?php
            if (isset($_SESSION['mensagem_sucesso'])) {
                echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 text-sm' role='alert'>" . htmlspecialchars($_SESSION['mensagem_sucesso']) . "</div>";
                unset($_SESSION['mensagem_sucesso']);
            }
            if (isset($_SESSION['erro_login'])) {
                echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-sm' role='alert'>" . htmlspecialchars($_SESSION['erro_login']) . "</div>";
                unset($_SESSION['erro_login']);
            }
            if (isset($_SESSION['erro_acesso'])) {
                echo "<div class='bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4 text-sm' role='alert'>" . htmlspecialchars($_SESSION['erro_acesso']) . "</div>";
                unset($_SESSION['erro_acesso']);
            }
            ?>

            <form class="mt-8 space-y-6" action="processa_login_empresa.php" method="POST">
                <div class="rounded-md shadow-sm space-y-4">
                    <div>
                        <label for="login" class="block text-sm font-medium text-gray-700 mb-1">Email ou CNPJ:</label>
                        <input id="login" name="login" type="text" required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm"
                               placeholder="Seu email ou CNPJ"
                               value="<?php echo htmlspecialchars($_SESSION['dados_formulario_login']['login'] ?? ''); ?>">
                    </div>
                    <div>
                        <label for="senha" class="block text-sm font-medium text-gray-700 mb-1">Senha:</label>
                        <input id="senha" name="senha" type="password" autocomplete="current-password" required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm"
                               placeholder="Sua senha">
                    </div>
                </div>

                <!-- <div class="flex items-center justify-between">
                    <div class="text-sm">
                        <a href="#" class="font-medium text-roxo-principal hover:text-purple-700">
                            Esqueceu sua senha?
                        </a>
                    </div>
                </div> -->

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-roxo-principal hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-roxo-principal">
                        Entrar
                    </button>
                </div>
            </form>
            <p class="mt-6 text-center text-sm">
                <a href="../../index.php" class="font-medium text-gray-600 hover:text-roxo-principal">
                    &larr; Voltar para a página inicial
                </a>
            </p>
        </div>
    </div>
    <?php
    if (isset($_SESSION['dados_formulario_login'])) {
        unset($_SESSION['dados_formulario_login']);
    }
    ?>
</body>
</html>

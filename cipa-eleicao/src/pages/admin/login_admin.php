<?php
session_start();
// Se já estiver logado como admin, redireciona para o painel admin
if (isset($_SESSION['admin_id'])) {
    header("Location: painel_admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login do Administrador | CIPA Fácil</title>
    <link href="../../src/styles/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 sm:p-10 rounded-xl shadow-2xl">
            <div>
                <img class="mx-auto h-16 w-auto" src="../../assets/images/logo_cipa_facil_sm.png" alt="CIPA Fácil Logo">
                <h2 class="mt-6 text-center text-3xl font-extrabold text-cinza-chumbo">
                    Painel Administrativo
                </h2>
            </div>

            <?php
            if (isset($_SESSION['erro_login_admin'])) {
                echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 text-sm' role='alert'>" . htmlspecialchars($_SESSION['erro_login_admin']) . "</div>";
                unset($_SESSION['erro_login_admin']);
            }
            if (isset($_SESSION['mensagem_logout_admin'])) {
                echo "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 text-sm' role='alert'>" . htmlspecialchars($_SESSION['mensagem_logout_admin']) . "</div>";
                unset($_SESSION['mensagem_logout_admin']);
            }
            ?>

            <form class="mt-8 space-y-6" action="processa_login_admin.php" method="POST">
                <div class="rounded-md shadow-sm space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email:</label>
                        <input id="email" name="email" type="email" autocomplete="email" required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-cinza-chumbo focus:border-cinza-chumbo sm:text-sm"
                               placeholder="seu.email@admin.com">
                    </div>
                    <div>
                        <label for="senha" class="block text-sm font-medium text-gray-700 mb-1">Senha:</label>
                        <input id="senha" name="senha" type="password" autocomplete="current-password" required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-cinza-chumbo focus:border-cinza-chumbo sm:text-sm"
                               placeholder="Sua senha">
                    </div>
                </div>
                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-cinza-chumbo hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Entrar
                    </button>
                </div>
            </form>
            <p class="mt-6 text-center text-sm">
                <a href="../../index.php" class="font-medium text-gray-600 hover:text-cinza-chumbo">
                    &larr; Voltar para a página inicial
                </a>
            </p>
        </div>
    </div>
</body>
</html>

<?php
session_start();

if (!isset($_SESSION['voto_registrado_sucesso']) || !$_SESSION['voto_registrado_sucesso']) {
    header("Location: login_votacao.php");
    exit();
}
unset($_SESSION['voto_registrado_sucesso']);
// Sessão de votação (votacao_funcionario_id, etc.) já deve ter sido limpa em processa_voto.php
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voto Registrado | CIPA Fácil</title>
    <link href="../../src/styles/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

    <div class="min-h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8 text-center">
        <div class="max-w-lg w-full space-y-8 bg-white p-8 sm:p-12 rounded-xl shadow-2xl">
            <div>
                <svg class="mx-auto h-24 w-24 text-verde-cipa" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h1 class="mt-6 text-3xl md:text-4xl font-extrabold text-verde-cipa">
                    Voto Registrado com Sucesso!
                </h1>
                <p class="mt-4 text-lg text-gray-700">
                    Obrigado por participar da eleição da CIPA.
                </p>
                <p class="mt-2 text-gray-600">
                    Sua participação é fundamental para promover um ambiente de trabalho mais seguro e saudável para todos.
                </p>
            </div>

            <div class="mt-10">
                <a href="../../index.php"
                   class="w-full sm:w-auto inline-block bg-roxo-principal hover:bg-purple-700 text-white font-semibold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 ease-in-out text-lg">
                    Voltar à Página Inicial
                </a>
            </div>
        </div>
        <footer class="mt-8 text-sm text-gray-500">
            &copy; <?php echo date("Y"); ?> CIPA Fácil Online.
        </footer>
    </div>

</body>
</html>

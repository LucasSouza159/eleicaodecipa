<?php
session_start();

// Verificar se o voto foi realmente registrado com sucesso
// Isso previne acesso direto à página sem ter votado.
if (!isset($_SESSION['voto_registrado_sucesso']) || !$_SESSION['voto_registrado_sucesso']) {
    // Se não há confirmação de voto, redireciona para o login de votação
    // ou para uma página de erro/informação.
    header("Location: login_votacao.php");
    exit();
}

// Limpar a flag da sessão para que, se o usuário recarregar a página,
// ele não veja a mensagem de sucesso novamente (ou seja redirecionado).
unset($_SESSION['voto_registrado_sucesso']);

// Qualquer outra limpeza de sessão específica da votação já deve ter sido feita em processa_voto.php
// Ex: unset($_SESSION['votacao_funcionario_id']); etc.
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voto Registrado - CIPA Fácil</title>
    <link href="../../src/styles/output.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
    </style>
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center font-sans p-4 text-center">

    <div class="w-full max-w-lg p-10 space-y-6 bg-white shadow-xl rounded-lg">
        <svg class="mx-auto h-20 w-20 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>

        <h1 class="text-3xl font-bold text-green-600">Voto Registrado com Sucesso!</h1>

        <p class="text-lg text-gray-700">
            Obrigado por participar da eleição da CIPA.
        </p>
        <p class="text-gray-600">
            Sua participação é muito importante para a segurança e bem-estar de todos na empresa.
        </p>

        <div class="mt-8">
            <a href="../../index.php"
               class="w-full sm:w-auto inline-block bg-roxo-principal hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition-colors">
                Voltar à Página Inicial
            </a>
        </div>
    </div>

    <footer class="absolute bottom-0 w-full text-center p-4 text-sm text-gray-500">
        &copy; <?php echo date("Y"); ?> CIPA Fácil Online.
    </footer>

</body>
</html>

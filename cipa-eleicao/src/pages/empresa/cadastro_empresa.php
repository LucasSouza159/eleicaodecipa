<?php
session_start();

$erros = $_SESSION['erros_cadastro'] ?? [];
$dados_formulario = $_SESSION['dados_formulario_cadastro'] ?? [];

unset($_SESSION['erros_cadastro']);
unset($_SESSION['dados_formulario_cadastro']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Empresa - CIPA Fácil</title>
    <link href="../../src/styles/output.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%; /* Garante que o corpo ocupe 100% da altura para centralização */
            margin: 0;
        }
    </style>
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center py-8 font-sans">

    <div class="w-full max-w-lg p-8 space-y-6 bg-white shadow-xl rounded-lg">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-roxo-principal">CIPA Fácil</h1>
            <h2 class="text-2xl font-semibold text-cinza-chumbo mt-2">Cadastro de Nova Empresa</h2>
        </div>

        <?php if (!empty($erros)): ?>
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 border border-red-400 rounded-lg" role="alert">
                <ul class="list-disc pl-5">
                    <?php foreach ($erros as $erro): ?>
                        <li><?php echo htmlspecialchars($erro); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="processa_cadastro_empresa.php" method="POST" class="space-y-5">
            <div>
                <label for="nome_fantasia" class="block text-sm font-medium text-gray-700">Nome Fantasia:</label>
                <input type="text" id="nome_fantasia" name="nome_fantasia"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm"
                       value="<?php echo htmlspecialchars($dados_formulario['nome_fantasia'] ?? ''); ?>" required>
            </div>

            <div>
                <label for="razao_social" class="block text-sm font-medium text-gray-700">Razão Social:</label>
                <input type="text" id="razao_social" name="razao_social"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm"
                       value="<?php echo htmlspecialchars($dados_formulario['razao_social'] ?? ''); ?>" required>
            </div>

            <div>
                <label for="cnpj" class="block text-sm font-medium text-gray-700">CNPJ:</label>
                <input type="text" id="cnpj" name="cnpj"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm"
                       value="<?php echo htmlspecialchars($dados_formulario['cnpj'] ?? ''); ?>" required placeholder="XX.XXX.XXX/XXXX-XX">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                <input type="email" id="email" name="email"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm"
                       value="<?php echo htmlspecialchars($dados_formulario['email'] ?? ''); ?>" required placeholder="seu@email.com">
            </div>

            <div>
                <label for="senha" class="block text-sm font-medium text-gray-700">Senha:</label>
                <input type="password" id="senha" name="senha"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm"
                       required placeholder="Mínimo 6 caracteres">
            </div>

            <div>
                <label for="confirma_senha" class="block text-sm font-medium text-gray-700">Confirmar Senha:</label>
                <input type="password" id="confirma_senha" name="confirma_senha"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm"
                       required>
            </div>

            <div>
                <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-roxo-principal hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-roxo-principal">
                    Cadastrar Empresa
                </button>
            </div>
        </form>
        <p class="text-sm text-center text-gray-600">
            Já tem uma conta?
            <a href="login_empresa.php" class="font-medium text-roxo-principal hover:text-purple-700">
                Faça login aqui
            </a>
        </p>
         <p class="text-sm text-center">
            <a href="../../index.php" class="font-medium text-gray-600 hover:text-roxo-principal">
                &larr; Voltar à Página Inicial
            </a>
        </p>
    </div>
</body>
</html>

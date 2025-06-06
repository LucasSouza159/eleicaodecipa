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
    <title>Cadastro de Empresa | CIPA Fácil</title>
    <link href="../../src/styles/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg w-full space-y-8 bg-white p-8 sm:p-10 rounded-xl shadow-2xl">
            <div>
                <img class="mx-auto h-16 w-auto" src="../../assets/images/logo_cipa_facil_sm.png" alt="CIPA Fácil Logo">
                 <!-- TODO: Adicionar um logo em src/assets/images/ -->
                <h2 class="mt-6 text-center text-3xl font-extrabold text-cinza-chumbo">
                    Crie sua Conta Empresarial
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    E comece a gerenciar suas eleições CIPA facilmente. Já possui uma conta?
                    <a href="login_empresa.php" class="font-medium text-roxo-principal hover:text-purple-700">
                        Faça login
                    </a>
                </p>
            </div>

            <?php if (!empty($erros)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Erros Encontrados:</p>
                    <ul class="list-disc pl-5 mt-2 text-sm">
                        <?php foreach ($erros as $erro): ?>
                            <li><?php echo htmlspecialchars($erro); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form class="mt-8 space-y-6" action="processa_cadastro_empresa.php" method="POST">
                <div class="space-y-4">
                    <div>
                        <label for="nome_fantasia" class="block text-sm font-medium text-gray-700 mb-1">Nome Fantasia:</label>
                        <input type="text" id="nome_fantasia" name="nome_fantasia"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm"
                               value="<?php echo htmlspecialchars($dados_formulario['nome_fantasia'] ?? ''); ?>" required>
                    </div>

                    <div>
                        <label for="razao_social" class="block text-sm font-medium text-gray-700 mb-1">Razão Social:</label>
                        <input type="text" id="razao_social" name="razao_social"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm"
                               value="<?php echo htmlspecialchars($dados_formulario['razao_social'] ?? ''); ?>" required>
                    </div>

                    <div>
                        <label for="cnpj" class="block text-sm font-medium text-gray-700 mb-1">CNPJ:</label>
                        <input type="text" id="cnpj" name="cnpj"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm"
                               value="<?php echo htmlspecialchars($dados_formulario['cnpj'] ?? ''); ?>" required placeholder="XX.XXX.XXX/XXXX-XX">
                               <!-- TODO: Adicionar máscara de CNPJ -->
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email:</label>
                        <input type="email" id="email" name="email"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm"
                               value="<?php echo htmlspecialchars($dados_formulario['email'] ?? ''); ?>" required placeholder="seu@email.com">
                    </div>

                    <div>
                        <label for="senha" class="block text-sm font-medium text-gray-700 mb-1">Senha:</label>
                        <input type="password" id="senha" name="senha"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm"
                               required placeholder="Mínimo 6 caracteres">
                    </div>

                    <div>
                        <label for="confirma_senha" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Senha:</label>
                        <input type="password" id="confirma_senha" name="confirma_senha"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm"
                               required>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-roxo-principal hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-roxo-principal">
                        Cadastrar Empresa
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
</body>
</html>

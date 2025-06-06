<?php
require_once '../../scripts/auth_empresa.php';
// $empresa_nome_logada é definida em auth_empresa.php
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel da Empresa - CIPA Fácil</title>
    <link href="../../src/styles/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

    <!-- Navbar do Painel -->
    <nav class="bg-roxo-principal text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="text-xl font-bold">
                    Painel da Empresa: <?php echo htmlspecialchars($empresa_nome_logada); ?>
                </div>
                <div>
                    <a href="logout_empresa.php"
                       class="px-4 py-2 bg-red-500 hover:bg-red-600 rounded-md text-sm font-medium transition-colors">
                        Sair
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal do Painel -->
    <main class="container mx-auto p-6 mt-8">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-semibold text-cinza-chumbo">Bem-vindo(a) ao seu Painel de Controle!</h2>
            <p class="text-gray-600">Aqui você pode gerenciar todas as etapas das eleições da CIPA da sua empresa.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Card para Gerenciar Filiais -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                <h3 class="text-xl font-semibold text-roxo-principal mb-3">Gerenciar Filiais</h3>
                <p class="text-gray-600 mb-4">Adicione, edite ou visualize as filiais da sua empresa.</p>
                <a href="#"
                   class="inline-block w-full text-center px-4 py-2 bg-gray-300 text-gray-700 hover:bg-gray-400 rounded-md font-medium transition-colors">
                    Acessar (Em breve)
                </a>
            </div>

            <!-- Card para Gerenciar Funcionários -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                <h3 class="text-xl font-semibold text-roxo-principal mb-3">Gerenciar Funcionários</h3>
                <p class="text-gray-600 mb-4">Cadastre, importe ou edite os dados dos seus funcionários.</p>
                <a href="gerenciar_funcionarios.php"
                   class="inline-block w-full text-center px-4 py-2 bg-azul-cipa text-white hover:bg-blue-700 rounded-md font-medium transition-colors">
                    Acessar Funcionários
                </a>
            </div>

            <!-- Card para Gerenciar Eleições -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                <h3 class="text-xl font-semibold text-roxo-principal mb-3">Gerenciar Eleições</h3>
                <p class="text-gray-600 mb-4">Crie novas eleições, acompanhe o status e gerencie comissões.</p>
                <a href="gerenciar_eleicoes.php"
                   class="inline-block w-full text-center px-4 py-2 bg-verde-cipa text-white hover:bg-green-700 rounded-md font-medium transition-colors">
                    Acessar Eleições
                </a>
            </div>

            <!-- Card para Configurações da Conta -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                <h3 class="text-xl font-semibold text-roxo-principal mb-3">Configurações da Conta</h3>
                <p class="text-gray-600 mb-4">Altere dados da sua empresa, senha de acesso e outras preferências.</p>
                <a href="#"
                   class="inline-block w-full text-center px-4 py-2 bg-gray-300 text-gray-700 hover:bg-gray-400 rounded-md font-medium transition-colors">
                    Acessar (Em breve)
                </a>
            </div>

            <!-- Adicionar mais cards conforme necessário -->

        </div>
    </main>

    <footer class="text-center p-4 mt-12 text-sm text-gray-500">
        &copy; <?php echo date("Y"); ?> CIPA Fácil Online. Todos os direitos reservados.
    </footer>

</body>
</html>

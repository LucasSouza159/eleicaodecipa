<?php
$titulo_pagina = "Painel Principal"; // Definir título antes de incluir o header
require_once 'includes/header_painel.php';
// auth_empresa.php já foi chamado em header_painel.php
// $empresa_nome_logada é definida em auth_empresa.php
?>

<div class="text-center mb-10">
    <h1 class="text-3xl md:text-4xl font-bold text-cinza-chumbo">
        Bem-vindo(a) ao seu Painel de Controle, <?php echo htmlspecialchars($empresa_nome_logada); ?>!
    </h1>
    <p class="text-lg text-gray-600 mt-2">Aqui você pode gerenciar todas as etapas das eleições da CIPA da sua empresa.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 xl:gap-8">

    <a href="gerenciar_funcionarios.php"
       class="block bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out group">
        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-azul-cipa text-white mx-auto mb-4 group-hover:bg-blue-700 transition-colors">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
        </div>
        <h3 class="text-xl font-semibold text-cinza-chumbo mb-2 text-center group-hover:text-roxo-principal transition-colors">Gerenciar Funcionários</h3>
        <p class="text-gray-600 text-sm text-center">Cadastre, importe ou edite os dados dos seus funcionários.</p>
    </a>

    <a href="gerenciar_eleicoes.php"
       class="block bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out group">
        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-verde-cipa text-white mx-auto mb-4 group-hover:bg-green-700 transition-colors">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <h3 class="text-xl font-semibold text-cinza-chumbo mb-2 text-center group-hover:text-roxo-principal transition-colors">Gerenciar Eleições</h3>
        <p class="text-gray-600 text-sm text-center">Crie novas eleições, acompanhe o status e gerencie comissões.</p>
    </a>

    <a href="#"
       class="block bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out group opacity-50 cursor-not-allowed"
       title="Em breve">
        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-gray-300 text-gray-700 mx-auto mb-4">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
        </div>
        <h3 class="text-xl font-semibold text-cinza-chumbo mb-2 text-center">Gerenciar Filiais</h3>
        <p class="text-gray-600 text-sm text-center">Adicione, edite ou visualize as filiais da sua empresa. (Em breve)</p>
    </a>

    <a href="#"
       class="block bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out group opacity-50 cursor-not-allowed"
       title="Em breve">
        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-gray-300 text-gray-700 mx-auto mb-4">
             <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
        </div>
        <h3 class="text-xl font-semibold text-cinza-chumbo mb-2 text-center">Configurações da Conta</h3>
        <p class="text-gray-600 text-sm text-center">Altere dados da sua empresa e senha de acesso. (Em breve)</p>
    </a>

    <!-- Adicionar mais cards conforme necessário -->
</div>

<?php
require_once 'includes/footer_painel.php';
?>

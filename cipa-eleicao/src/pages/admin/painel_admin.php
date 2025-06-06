<?php
$titulo_pagina_admin = "Painel Administrativo"; // Definir título antes de incluir o header
require_once 'includes/header_painel_admin.php';
// auth_admin.php já foi chamado em header_painel_admin.php
// Variáveis como $admin_nome_logado, $admin_nivel_acesso_logado são definidas lá.
?>

<div class="text-center mb-10">
    <h1 class="text-3xl md:text-4xl font-bold text-cinza-chumbo">
        Bem-vindo(a) ao Painel Administrativo, <?php echo htmlspecialchars($admin_nome_logado); ?>!
    </h1>
    <p class="text-lg text-gray-600 mt-2">Gerencie o sistema CIPA Fácil e todos os seus aspectos.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 xl:gap-8">

    <a href="gerenciar_empresas_admin.php"
       class="block bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out group opacity-50 cursor-not-allowed"
       title="Em breve">
        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-gray-300 text-gray-700 mx-auto mb-4">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
        </div>
        <h3 class="text-xl font-semibold text-cinza-chumbo mb-2 text-center group-hover:text-roxo-principal transition-colors">Gerenciar Empresas</h3>
        <p class="text-gray-600 text-sm text-center">Visualize, edite ou adicione novas empresas ao sistema. (Em breve)</p>
    </a>

    <a href="gerenciar_eleicoes_admin.php"
       class="block bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out group opacity-50 cursor-not-allowed"
       title="Em breve">
        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-gray-300 text-gray-700 mx-auto mb-4">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <h3 class="text-xl font-semibold text-cinza-chumbo mb-2 text-center group-hover:text-roxo-principal transition-colors">Gerenciar Todas Eleições</h3>
        <p class="text-gray-600 text-sm text-center">Monitore e gerencie todas as eleições de todas as empresas. (Em breve)</p>
    </a>

    <a href="gerenciar_administradores_admin.php"
       class="block bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out group opacity-50 cursor-not-allowed"
       title="Em breve">
        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-gray-300 text-gray-700 mx-auto mb-4">
             <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
        </div>
        <h3 class="text-xl font-semibold text-cinza-chumbo mb-2 text-center group-hover:text-roxo-principal transition-colors">Gerenciar Administradores</h3>
        <p class="text-gray-600 text-sm text-center">Adicione ou edite contas de administradores do sistema. (Em breve)</p>
    </a>

    <a href="logs_sistema_admin.php"
       class="block bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out group opacity-50 cursor-not-allowed"
       title="Em breve">
        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-gray-300 text-gray-700 mx-auto mb-4">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </div>
        <h3 class="text-xl font-semibold text-cinza-chumbo mb-2 text-center group-hover:text-roxo-principal transition-colors">Logs do Sistema</h3>
        <p class="text-gray-600 text-sm text-center">Visualize logs de atividades e erros do sistema. (Em breve)</p>
    </a>

     <a href="#"
       class="block bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out group opacity-50 cursor-not-allowed"
       title="Em breve">
        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-gray-300 text-gray-700 mx-auto mb-4">
             <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
        </div>
        <h3 class="text-xl font-semibold text-cinza-chumbo mb-2 text-center group-hover:text-roxo-principal transition-colors">Configurações Gerais</h3>
        <p class="text-gray-600 text-sm text-center">Ajustes globais do sistema. (Em breve)</p>
    </a>

</div>

<?php
require_once 'includes/footer_painel_admin.php';
?>

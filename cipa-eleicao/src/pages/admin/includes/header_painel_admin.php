<?php
// auth_admin.php já inicia a sessão se necessário
require_once __DIR__ . '/../../../scripts/auth_admin.php';
// Variáveis como $admin_nome_logado, $admin_nivel_acesso_logado são definidas em auth_admin.php

$pagina_atual_admin = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo_pagina_admin) ? htmlspecialchars($titulo_pagina_admin) . ' | ' : ''; ?>Painel Admin - CIPA Fácil</title>
    <link href="../../../src/styles/output.css" rel="stylesheet">
    <link rel="icon" href="../../../assets/images/favicon.ico" type="image/x-icon">
</head>
<body class="bg-gray-200 font-sans flex flex-col min-h-screen">

    <nav class="bg-cinza-chumbo text-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="painel_admin.php" class="flex-shrink-0">
                        <span class="font-bold text-xl">CIPA Fácil <span class="text-sm font-normal">- Admin</span></span>
                    </a>
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <a href="painel_admin.php"
                               class="<?php echo ($pagina_atual_admin === 'painel_admin.php') ? 'bg-gray-700' : ''; ?> hover:bg-gray-700 px-3 py-2 rounded-md text-sm font-medium transition-colors">Início</a>
                            <a href="gerenciar_empresas_admin.php"
                               class="<?php echo ($pagina_atual_admin === 'gerenciar_empresas_admin.php') ? 'bg-gray-700' : ''; ?> hover:bg-gray-700 px-3 py-2 rounded-md text-sm font-medium transition-colors">Empresas</a>
                            <a href="gerenciar_eleicoes_admin.php"
                               class="<?php echo ($pagina_atual_admin === 'gerenciar_eleicoes_admin.php') ? 'bg-gray-700' : ''; ?> hover:bg-gray-700 px-3 py-2 rounded-md text-sm font-medium transition-colors">Eleições</a>
                             <a href="logs_sistema_admin.php"
                               class="<?php echo ($pagina_atual_admin === 'logs_sistema_admin.php') ? 'bg-gray-700' : ''; ?> hover:bg-gray-700 px-3 py-2 rounded-md text-sm font-medium transition-colors opacity-50 cursor-not-allowed" title="Em breve">Logs (Breve)</a>
                        </div>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-4 flex items-center md:ml-6 text-sm">
                        <span class="mr-3">Admin: <?php echo htmlspecialchars($admin_nome_logado); ?> <span class="text-xs">(<?php echo htmlspecialchars($admin_nivel_acesso_logado); ?>)</span></span>
                        <a href="logout_admin.php"
                           class="bg-red-600 hover:bg-red-700 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Logout
                        </a>
                    </div>
                </div>
                <div class="-mr-2 flex md:hidden">
                    <button type="button" id="mobile-menu-button-admin"
                            class="bg-gray-700 inline-flex items-center justify-center p-2 rounded-md text-gray-300 hover:text-white hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                            aria-controls="mobile-menu-admin" aria-expanded="false">
                        <span class="sr-only">Abrir menu</span>
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="md:hidden hidden" id="mobile-menu-admin">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="painel_admin.php" class="hover:bg-gray-700 text-white block px-3 py-2 rounded-md text-base font-medium <?php echo ($pagina_atual_admin === 'painel_admin.php') ? 'bg-gray-700' : ''; ?>">Início</a>
                <a href="gerenciar_empresas_admin.php" class="hover:bg-gray-700 text-white block px-3 py-2 rounded-md text-base font-medium <?php echo ($pagina_atual_admin === 'gerenciar_empresas_admin.php') ? 'bg-gray-700' : ''; ?>">Empresas</a>
                <a href="gerenciar_eleicoes_admin.php" class="hover:bg-gray-700 text-white block px-3 py-2 rounded-md text-base font-medium <?php echo ($pagina_atual_admin === 'gerenciar_eleicoes_admin.php') ? 'bg-gray-700' : ''; ?>">Eleições</a>
                <a href="#" class="hover:bg-gray-700 text-white block px-3 py-2 rounded-md text-base font-medium opacity-50 cursor-not-allowed">Logs (Breve)</a>
            </div>
            <div class="pt-4 pb-3 border-t border-gray-700">
                <div class="flex items-center px-5">
                    <div>
                        <div class="text-base font-medium leading-none text-white"><?php echo htmlspecialchars($admin_nome_logado); ?></div>
                        <div class="text-sm font-medium leading-none text-gray-400"><?php echo htmlspecialchars($admin_nivel_acesso_logado); ?></div>
                    </div>
                </div>
                <div class="mt-3 px-2 space-y-1">
                    <a href="logout_admin.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <?php
            if (isset($_SESSION['mensagem_sucesso_admin_global'])) {
                echo "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6' role='alert'><p>" . htmlspecialchars($_SESSION['mensagem_sucesso_admin_global']) . "</p></div>";
                unset($_SESSION['mensagem_sucesso_admin_global']);
            }
            if (isset($_SESSION['mensagem_erro_admin_global'])) {
                echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6' role='alert'><p>" . htmlspecialchars($_SESSION['mensagem_erro_admin_global']) . "</p></div>";
                unset($_SESSION['mensagem_erro_admin_global']);
            }
            ?>
            <!-- Conteúdo específico da página do admin -->

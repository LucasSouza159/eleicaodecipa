<?php
// session_start(); // auth_empresa.php já inicia a sessão
// Incluir o script de autenticação para proteger todas as páginas do painel
require_once __DIR__ . '/../../../scripts/auth_empresa.php';
// $empresa_nome_logada é definida em auth_empresa.php
// $empresa_id é também definida em auth_empresa.php (anteriormente $_SESSION['empresa_id'])

$pagina_atual = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo_pagina) ? htmlspecialchars($titulo_pagina) . ' | ' : ''; ?>Painel Empresa - CIPA Fácil</title>
    <link href="../../../src/styles/output.css" rel="stylesheet">
    <!-- Favicon (exemplo, substitua pelo seu) -->
    <link rel="icon" href="../../../assets/images/favicon.ico" type="image/x-icon">
</head>
<body class="bg-gray-100 font-sans flex flex-col min-h-screen">

    <nav class="bg-roxo-principal text-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="../empresa/painel_empresa.php" class="flex-shrink-0">
                        <!-- <img class="h-10 w-auto" src="../../../assets/images/logo_cipa_facil_branca_sm.png" alt="CIPA Fácil"> -->
                        <span class="font-bold text-xl">CIPA Fácil</span>
                    </a>
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <a href="painel_empresa.php"
                               class="<?php echo ($pagina_atual === 'painel_empresa.php') ? 'bg-purple-700' : ''; ?> hover:bg-purple-700 px-3 py-2 rounded-md text-sm font-medium transition-colors">Início</a>
                            <a href="gerenciar_funcionarios.php"
                               class="<?php echo ($pagina_atual === 'gerenciar_funcionarios.php' || $pagina_atual === 'cadastrar_funcionario.php' || $pagina_atual === 'editar_funcionario.php') ? 'bg-purple-700' : ''; ?> hover:bg-purple-700 px-3 py-2 rounded-md text-sm font-medium transition-colors">Funcionários</a>
                            <a href="gerenciar_eleicoes.php"
                               class="<?php echo ($pagina_atual === 'gerenciar_eleicoes.php' || $pagina_atual === 'criar_eleicao.php' || $pagina_atual === 'editar_eleicao.php' || $pagina_atual === 'gerenciar_comissao_eleitoral.php' || $pagina_atual === 'definir_senha_comissao.php') ? 'bg-purple-700' : ''; ?> hover:bg-purple-700 px-3 py-2 rounded-md text-sm font-medium transition-colors">Eleições</a>
                            <!-- Adicionar outros links principais aqui -->
                        </div>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-4 flex items-center md:ml-6">
                        <span class="mr-3 text-sm">Olá, <?php echo htmlspecialchars($empresa_nome_logada); ?></span>
                        <a href="logout_empresa.php"
                           class="bg-red-500 hover:bg-red-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Logout
                        </a>
                    </div>
                </div>
                <div class="-mr-2 flex md:hidden">
                    <!-- Botão do menu mobile -->
                    <button type="button" id="mobile-menu-button"
                            class="bg-purple-700 inline-flex items-center justify-center p-2 rounded-md text-purple-200 hover:text-white hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                            aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Abrir menu principal</span>
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Menu Mobile -->
        <div class="md:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="painel_empresa.php" class="hover:bg-purple-700 text-white block px-3 py-2 rounded-md text-base font-medium <?php echo ($pagina_atual === 'painel_empresa.php') ? 'bg-purple-700' : ''; ?>">Início</a>
                <a href="gerenciar_funcionarios.php" class="hover:bg-purple-700 text-white block px-3 py-2 rounded-md text-base font-medium <?php echo ($pagina_atual === 'gerenciar_funcionarios.php' || $pagina_atual === 'cadastrar_funcionario.php' || $pagina_atual === 'editar_funcionario.php') ? 'bg-purple-700' : ''; ?>">Funcionários</a>
                <a href="gerenciar_eleicoes.php" class="hover:bg-purple-700 text-white block px-3 py-2 rounded-md text-base font-medium <?php echo ($pagina_atual === 'gerenciar_eleicoes.php' || $pagina_atual === 'criar_eleicao.php' || $pagina_atual === 'editar_eleicao.php' || $pagina_atual === 'gerenciar_comissao_eleitoral.php' || $pagina_atual === 'definir_senha_comissao.php') ? 'bg-purple-700' : ''; ?>">Eleições</a>
            </div>
            <div class="pt-4 pb-3 border-t border-purple-500">
                <div class="flex items-center px-5">
                    <div class="ml-3">
                        <div class="text-base font-medium leading-none text-white"><?php echo htmlspecialchars($empresa_nome_logada); ?></div>
                        <!-- <div class="text-sm font-medium leading-none text-gray-400"><?php // echo htmlspecialchars($_SESSION['empresa_email'] ?? ''); ?></div> -->
                    </div>
                </div>
                <div class="mt-3 px-2 space-y-1">
                    <a href="logout_empresa.php" class="block px-3 py-2 rounded-md text-base font-medium text-purple-200 hover:text-white hover:bg-purple-700">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow"> <!-- main agora é flex-grow para empurrar o footer -->
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- O conteúdo específico da página será inserido aqui -->
            <?php
            // Exibir mensagens de sessão globais (se houver)
            if (isset($_SESSION['mensagem_sucesso_global'])) {
                echo "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6' role='alert'><p>" . htmlspecialchars($_SESSION['mensagem_sucesso_global']) . "</p></div>";
                unset($_SESSION['mensagem_sucesso_global']);
            }
            if (isset($_SESSION['mensagem_erro_global'])) {
                echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6' role='alert'><p>" . htmlspecialchars($_SESSION['mensagem_erro_global']) . "</p></div>";
                unset($_SESSION['mensagem_erro_global']);
            }
            ?>

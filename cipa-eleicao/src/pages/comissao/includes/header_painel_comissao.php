<?php
// auth_comissao.php já inicia a sessão
require_once __DIR__ . '/../../../scripts/auth_comissao.php';
// Variáveis como $comissao_nome_membro_logado, $comissao_eleicao_titulo_logado,
// $comissao_papel_logado, $comissao_eleicao_id_logado são definidas em auth_comissao.php

$pagina_atual_comissao = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo_pagina_comissao) ? htmlspecialchars($titulo_pagina_comissao) . ' | ' : ''; ?>Painel Comissão - CIPA Fácil</title>
    <link href="../../../src/styles/output.css" rel="stylesheet">
    <link rel="icon" href="../../../assets/images/favicon.ico" type="image/x-icon">
</head>
<body class="bg-gray-100 font-sans flex flex-col min-h-screen">

    <nav class="bg-azul-cipa text-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="painel_comissao.php" class="flex-shrink-0">
                        <span class="font-bold text-xl">CIPA Fácil <span class="text-sm font-normal">- Comissão</span></span>
                    </a>
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <a href="painel_comissao.php"
                               class="<?php echo ($pagina_atual_comissao === 'painel_comissao.php') ? 'bg-blue-700' : ''; ?> hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium transition-colors">Início</a>
                            <a href="gerenciar_candidatos.php"
                               class="<?php echo ($pagina_atual_comissao === 'gerenciar_candidatos.php' || $pagina_atual_comissao === 'editar_candidatura.php') ? 'bg-blue-700' : ''; ?> hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium transition-colors">Candidaturas</a>
                            <a href="apuracao_resultados.php"
                               class="<?php echo ($pagina_atual_comissao === 'apuracao_resultados.php') ? 'bg-blue-700' : ''; ?> hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium transition-colors">Apuração</a>
                            <!-- Adicionar outros links principais aqui -->
                        </div>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-4 flex items-center md:ml-6 text-sm">
                        <div class="mr-3 text-right">
                            <div><?php echo htmlspecialchars($comissao_nome_membro_logado); ?> <span class="text-xs">(<?php echo htmlspecialchars($comissao_papel_logado); ?>)</span></div>
                            <div class="text-xs text-blue-200"><?php echo htmlspecialchars($comissao_eleicao_titulo_logado); ?></div>
                        </div>
                        <a href="logout_comissao.php"
                           class="bg-red-500 hover:bg-red-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Logout
                        </a>
                    </div>
                </div>
                <div class="-mr-2 flex md:hidden">
                    <!-- Botão do menu mobile -->
                    <button type="button" id="mobile-menu-button-comissao"
                            class="bg-blue-700 inline-flex items-center justify-center p-2 rounded-md text-blue-200 hover:text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                            aria-controls="mobile-menu-comissao" aria-expanded="false">
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
        <div class="md:hidden hidden" id="mobile-menu-comissao">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="painel_comissao.php" class="hover:bg-blue-700 text-white block px-3 py-2 rounded-md text-base font-medium <?php echo ($pagina_atual_comissao === 'painel_comissao.php') ? 'bg-blue-700' : ''; ?>">Início</a>
                <a href="gerenciar_candidatos.php" class="hover:bg-blue-700 text-white block px-3 py-2 rounded-md text-base font-medium <?php echo ($pagina_atual_comissao === 'gerenciar_candidatos.php' || $pagina_atual_comissao === 'editar_candidatura.php') ? 'bg-blue-700' : ''; ?>">Candidaturas</a>
                <a href="apuracao_resultados.php" class="hover:bg-blue-700 text-white block px-3 py-2 rounded-md text-base font-medium <?php echo ($pagina_atual_comissao === 'apuracao_resultados.php') ? 'bg-blue-700' : ''; ?>">Apuração</a>
            </div>
            <div class="pt-4 pb-3 border-t border-blue-500">
                <div class="flex items-center px-5">
                    <div>
                        <div class="text-base font-medium leading-none text-white"><?php echo htmlspecialchars($comissao_nome_membro_logado); ?></div>
                        <div class="text-sm font-medium leading-none text-blue-200"><?php echo htmlspecialchars($comissao_papel_logado); ?></div>
                        <div class="text-sm font-medium leading-none text-blue-300 mt-1"><?php echo htmlspecialchars($comissao_eleicao_titulo_logado); ?></div>
                    </div>
                </div>
                <div class="mt-3 px-2 space-y-1">
                    <a href="logout_comissao.php" class="block px-3 py-2 rounded-md text-base font-medium text-blue-200 hover:text-white hover:bg-blue-700">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- O conteúdo específico da página será inserido aqui -->
            <?php
            // Exibir mensagens de sessão globais (se houver)
            if (isset($_SESSION['mensagem_sucesso_comissao_global'])) {
                echo "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6' role='alert'><p>" . htmlspecialchars($_SESSION['mensagem_sucesso_comissao_global']) . "</p></div>";
                unset($_SESSION['mensagem_sucesso_comissao_global']);
            }
            if (isset($_SESSION['mensagem_erro_comissao_global'])) {
                echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6' role='alert'><p>" . htmlspecialchars($_SESSION['mensagem_erro_comissao_global']) . "</p></div>";
                unset($_SESSION['mensagem_erro_comissao_global']);
            }
            ?>

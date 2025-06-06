<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Eleição CIPA Online | CIPA Fácil</title>
    <link href="src/styles/output.css" rel="stylesheet">
    <!-- A fonte Inter é aplicada globalmente via main.css -> body @apply font-sans;
         onde font-sans é definido em tailwind.config.js como ['Inter', 'Arial', 'sans-serif'] -->
</head>
<body class="bg-gray-50 text-cinza-chumbo">

    <header class="bg-roxo-principal text-white shadow-md">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-5 flex justify-between items-center">
            <h1 class="text-2xl md:text-3xl font-bold">CIPA Fácil Online</h1>
            <!-- Navegação do cabeçalho (se houver no futuro)
            <nav class="hidden md:flex space-x-4">
                <a href="#" class="hover:text-gray-300">Início</a>
                <a href="#" class="hover:text-gray-300">Sobre</a>
                <a href="#" class="hover:text-gray-300">Contato</a>
            </nav>
            -->
        </div>
    </header>

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <section class="text-center">
            <h2 class="text-4xl sm:text-5xl md:text-6xl font-bold text-cinza-chumbo mb-6">
                Gerencie as Eleições da CIPA de Forma Simples e Segura
            </h2>
            <p class="text-lg sm:text-xl text-gray-700 mb-10 max-w-3xl mx-auto">
                Nossa plataforma facilita todo o processo eleitoral da CIPA, desde a convocação até a apuração, em total conformidade com a NR-05. Ideal para empresas com múltiplos CNPJs e filiais.
            </p>
            <div class="flex flex-col sm:flex-row justify-center items-center sm:space-x-4 space-y-4 sm:space-y-0">
                <a href="src/pages/empresa/login_empresa.php"
                   class="w-full sm:w-auto inline-block bg-roxo-principal hover:bg-purple-700 text-white font-semibold py-3 px-8 rounded-lg shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1">
                    Sou Empresa / Acessar Painel
                </a>
                <a href="src/pages/votacao/login_votacao.php"
                   class="w-full sm:w-auto inline-block bg-verde-cipa hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-lg shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1">
                    Sou Funcionário / Votar
                </a>
                <a href="src/pages/comissao/login_comissao.php"
                   class="w-full sm:w-auto inline-block bg-gray-700 hover:bg-gray-800 text-white font-semibold py-3 px-8 rounded-lg shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1">
                    Acesso Restrito (Comissão/Admin)
                </a>
            </div>
        </section>

        <section class="mt-16 md:mt-24 py-12 bg-white rounded-lg shadow-xl">
            <div class="container mx-auto px-6 text-center">
                <h3 class="text-3xl md:text-4xl font-bold text-cinza-chumbo mb-6">Conformidade com a NR-05</h3>
                <p class="text-md sm:text-lg text-gray-600 max-w-2xl mx-auto">
                    O sistema CIPA Fácil Online foi desenvolvido seguindo as diretrizes da Norma Regulamentadora nº 05, garantindo a autenticidade do processo, o sigilo do voto e a geração de todos os documentos necessários para auditoria e conformidade legal.
                </p>
            </div>
        </section>

        <!-- Seção de Funcionalidades (Exemplo) -->
        <section class="mt-16 md:mt-24 py-12">
            <div class="container mx-auto px-6 text-center">
                <h3 class="text-3xl md:text-4xl font-bold text-cinza-chumbo mb-10">Principais Funcionalidades</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <svg class="w-12 h-12 text-roxo-principal mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h4 class="text-xl font-semibold mb-2">Gestão Completa</h4>
                        <p class="text-gray-600">Desde o cadastro da empresa até a apuração dos votos e geração de atas.</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <svg class="w-12 h-12 text-roxo-principal mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <h4 class="text-xl font-semibold mb-2">Votação Segura</h4>
                        <p class="text-gray-600">Processo de votação online com garantia de sigilo e autenticidade.</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <svg class="w-12 h-12 text-roxo-principal mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <h4 class="text-xl font-semibold mb-2">Relatórios e Atas</h4>
                        <p class="text-gray-600">Geração automática dos documentos exigidos pela NR-05.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-gray-800 text-gray-300 p-8 text-center mt-12 md:mt-16">
        <p>&copy; <?php echo date("Y"); ?> CIPA Fácil Online. Todos os direitos reservados.</p>
        <p class="text-sm mt-2">
            <a href="#" class="hover:text-roxo-principal transition-colors">Política de Privacidade</a> |
            <a href="#" class="hover:text-roxo-principal transition-colors">Termos de Uso</a>
        </p>
    </footer>

</body>
</html>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Eleição CIPA Online | CIPA Fácil</title>
    <link href="src/styles/output.css" rel="stylesheet">
    <!-- Para desenvolvimento, pode-se usar o CDN do Tailwind para ver os estilos aplicados:
    <script src="https://cdn.tailwindcss.com"></script>
    -->
    <style>
        /* Estilos personalizados podem ser adicionados aqui ou em um arquivo CSS separado se necessário */
        /* Exemplo: body { font-family: 'Inter', sans-serif; } // Supondo que Inter foi importada via Tailwind config ou CSS */
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

    <header class="bg-purple-700 text-white shadow-md">
        <div class="container mx-auto p-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">CIPA Fácil Online</h1>
            <nav>
                <!-- Links de navegação no cabeçalho, se necessário no futuro -->
            </nav>
        </div>
    </header>

    <main class="container mx-auto p-6 md:p-8">
        <section class="text-center py-12">
            <h1 class="text-4xl md:text-5xl font-bold text-purple-700 mb-6">
                Gerencie as Eleições da CIPA de Forma Simples e Segura
            </h1>
            <p class="text-lg md:text-xl text-gray-600 mb-10 max-w-3xl mx-auto">
                Nossa plataforma facilita todo o processo eleitoral da CIPA, desde a convocação até a apuração, em total conformidade com a NR-05. Ideal para empresas com múltiplos CNPJs e filiais.
            </p>
            <div class="space-y-4 md:space-y-0 md:space-x-4">
                <a href="src/pages/empresa/login_empresa.php"
                   class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1">
                    Sou Empresa / Acessar Painel
                </a>
                <a href="src/pages/votacao/login_votacao.php"
                   class="inline-block bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1">
                    Sou Funcionário / Votar
                </a>
                <a href="src/pages/comissao/login_comissao.php"
                   class="inline-block bg-gray-700 hover:bg-gray-800 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1">
                    Acesso Restrito (Comissão/Admin)
                </a>
            </div>
        </section>

        <section class="py-12 bg-white shadow-lg rounded-lg mt-12 p-8">
            <h2 class="text-3xl font-semibold text-purple-700 mb-4 text-center">Conformidade com a NR-05</h2>
            <p class="text-gray-700 leading-relaxed max-w-2xl mx-auto text-center">
                O sistema CIPA Fácil Online foi desenvolvido seguindo as diretrizes da Norma Regulamentadora nº 05, garantindo a autenticidade do processo, o sigilo do voto e a geração de todos os documentos necessários para auditoria e conformidade legal.
            </p>
        </section>

        <!-- Outras seções podem ser adicionadas aqui: Funcionalidades, Depoimentos, FAQ, etc. -->

    </main>

    <footer class="bg-gray-800 text-gray-300 text-center p-6 mt-12">
        <p>&copy; <?php echo date("Y"); ?> CIPA Fácil Online. Todos os direitos reservados.</p>
        <p><a href="#" class="hover:text-purple-400">Política de Privacidade</a> | <a href="#" class="hover:text-purple-400">Termos de Uso</a></p>
    </footer>

</body>
</html>

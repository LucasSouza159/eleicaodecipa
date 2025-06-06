<?php
require_once '../../scripts/auth_comissao.php';
require_once '../../scripts/db_connection.php';

$eleicao_detalhes_adicionais = null;
try {
    $stmt_eleicao = $pdo->prepare("SELECT ano_referencia, status_eleicao FROM eleicoes WHERE id = ?");
    $stmt_eleicao->execute([$comissao_eleicao_id_logado]); // $comissao_eleicao_id_logado vem de auth_comissao.php
    $eleicao_detalhes_adicionais = $stmt_eleicao->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ao buscar detalhes da eleição no painel da comissão: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel da Comissão - CIPA Fácil</title>
    <link href="../../src/styles/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

    <!-- Navbar do Painel da Comissão -->
    <nav class="bg-azul-cipa text-white shadow-lg"> <!-- Cor diferente para diferenciar do painel da empresa -->
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="text-xl font-bold">
                    Comissão: <?php echo htmlspecialchars($comissao_eleicao_titulo_logado); // Vem de auth_comissao.php ?>
                </div>
                <div>
                    <span class="text-sm mr-4"><?php echo htmlspecialchars($comissao_nome_membro_logado . " - " . $comissao_papel_logado); ?></span>
                    <a href="logout_comissao.php"
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
            <h2 class="text-3xl font-semibold text-cinza-chumbo">Painel de Controle da Comissão Eleitoral</h2>
            <?php if ($eleicao_detalhes_adicionais): ?>
                <p class="text-gray-600">
                    Eleição: <strong><?php echo htmlspecialchars($comissao_eleicao_titulo_logado); ?></strong> |
                    Ano Referência: <strong><?php echo htmlspecialchars($eleicao_detalhes_adicionais['ano_referencia']); ?></strong> |
                    Status Atual: <strong class="text-blue-600"><?php echo htmlspecialchars($eleicao_detalhes_adicionais['status_eleicao']); ?></strong>
                </p>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                <h3 class="text-xl font-semibold text-azul-cipa mb-3">Gerenciar Candidaturas</h3>
                <p class="text-gray-600 mb-4">Inscreva, aprove, reprove ou edite informações dos candidatos.</p>
                <a href="gerenciar_candidatos.php"
                   class="inline-block w-full text-center px-4 py-2 bg-azul-cipa text-white hover:bg-blue-700 rounded-md font-medium transition-colors">
                    Acessar Candidaturas
                </a>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                <h3 class="text-xl font-semibold text-azul-cipa mb-3">Configurar Votação</h3>
                <p class="text-gray-600 mb-4">Defina períodos, abra ou encerre a votação online.</p>
                <a href="configurar_votacao_comissao.php?eleicao_id=<?php echo $comissao_eleicao_id_logado; ?>"
                   class="inline-block w-full text-center px-4 py-2 bg-gray-300 text-gray-700 hover:bg-gray-400 rounded-md font-medium transition-colors">
                    Acessar (Em breve)
                </a>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                <h3 class="text-xl font-semibold text-azul-cipa mb-3">Apuração e Resultados</h3>
                <p class="text-gray-600 mb-4">Acompanhe a apuração, encerre a votação e publique os resultados.</p>
                <a href="apuracao_resultados.php"
                   class="inline-block w-full text-center px-4 py-2 bg-azul-cipa text-white hover:bg-blue-700 rounded-md font-medium transition-colors">
                    Acessar Apuração
                </a>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                <h3 class="text-xl font-semibold text-azul-cipa mb-3">Emitir Comunicados</h3>
                <p class="text-gray-600 mb-4">Envie comunicados para os envolvidos na eleição.</p>
                <a href="comunicados_comissao.php?eleicao_id=<?php echo $comissao_eleicao_id_logado; ?>"
                   class="inline-block w-full text-center px-4 py-2 bg-gray-300 text-gray-700 hover:bg-gray-400 rounded-md font-medium transition-colors">
                    Acessar (Em breve)
                </a>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                <h3 class="text-xl font-semibold text-azul-cipa mb-3">Gerar Documentos</h3>
                <p class="text-gray-600 mb-4">Gere atas e outros documentos importantes da eleição.</p>
                <a href="../../reports/gerar_ata_convocacao.php?eleicao_id=<?php echo $comissao_eleicao_id_logado; ?>"
                   target="_blank"
                   class="block w-full text-center mb-2 px-4 py-2 bg-teal-500 text-white hover:bg-teal-600 rounded-md font-medium transition-colors">
                    Ata de Convocação (Preview)
                </a>
                <a href="../../reports/gerar_lista_votantes.php?eleicao_id=<?php echo $comissao_eleicao_id_logado; ?>"
                   target="_blank"
                   class="block w-full text-center mb-2 px-4 py-2 bg-teal-500 text-white hover:bg-teal-600 rounded-md font-medium transition-colors">
                    Lista de Votantes (Preview)
                </a>
                <a href="../../reports/gerar_ata_resultado_posse.php?eleicao_id=<?php echo $comissao_eleicao_id_logado; ?>"
                   target="_blank"
                   class="block w-full text-center px-4 py-2 bg-teal-500 text-white hover:bg-teal-600 rounded-md font-medium transition-colors">
                    Ata de Resultado e Posse (Preview)
                </a>
            </div>

            <!-- Adicionar mais cards conforme necessário -->
        </div>
    </main>

    <footer class="text-center p-4 mt-12 text-sm text-gray-500">
        &copy; <?php echo date("Y"); ?> CIPA Fácil Online.
    </footer>

</body>
</html>

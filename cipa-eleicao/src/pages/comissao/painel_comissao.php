<?php
$titulo_pagina_comissao = "Painel Principal"; // Definir título antes de incluir o header
require_once 'includes/header_painel_comissao.php';
// auth_comissao.php já foi chamado em header_painel_comissao.php
// Variáveis como $comissao_nome_membro_logado, $comissao_eleicao_titulo_logado, $comissao_papel_logado, $comissao_eleicao_id_logado são definidas lá.
// db_connection.php também já foi incluído se auth_comissao.php o requer.
// Se não, e for necessário aqui, descomente:
// require_once '../../scripts/db_connection.php';

$eleicao_detalhes_adicionais = null;
if (isset($pdo)) { // Verifica se $pdo foi inicializado (por db_connection.php)
    try {
        $stmt_eleicao = $pdo->prepare("SELECT ano_referencia, status_eleicao FROM eleicoes WHERE id = ?");
        $stmt_eleicao->execute([$comissao_eleicao_id_logado]);
        $eleicao_detalhes_adicionais = $stmt_eleicao->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erro ao buscar detalhes da eleição no painel da comissão: " . $e->getMessage());
        // Não crítico, a página pode continuar sem esses detalhes extras.
    }
}
?>

<div class="text-center mb-10">
    <h1 class="text-3xl md:text-4xl font-bold text-cinza-chumbo">
        Painel de Controle da Comissão Eleitoral
    </h1>
    <?php if ($eleicao_detalhes_adicionais): ?>
        <p class="text-lg text-gray-600 mt-2">
            Eleição: <strong class="text-azul-cipa"><?php echo htmlspecialchars($comissao_eleicao_titulo_logado); ?></strong> |
            Ano: <strong class="text-azul-cipa"><?php echo htmlspecialchars($eleicao_detalhes_adicionais['ano_referencia']); ?></strong> |
            Status: <strong class="text-azul-cipa"><?php echo htmlspecialchars($eleicao_detalhes_adicionais['status_eleicao']); ?></strong>
        </p>
    <?php else: ?>
         <p class="text-lg text-gray-600 mt-2">
            Eleição: <strong class="text-azul-cipa"><?php echo htmlspecialchars($comissao_eleicao_titulo_logado); ?></strong>
        </p>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 xl:gap-8">

    <a href="gerenciar_candidatos.php"
       class="block bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out group">
        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-azul-cipa text-white mx-auto mb-4 group-hover:bg-blue-700 transition-colors">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
        </div>
        <h3 class="text-xl font-semibold text-cinza-chumbo mb-2 text-center group-hover:text-azul-cipa transition-colors">Gerenciar Candidaturas</h3>
        <p class="text-gray-600 text-sm text-center">Inscreva, aprove, reprove ou edite informações dos candidatos.</p>
    </a>

    <a href="apuracao_resultados.php"
       class="block bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out group">
        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-azul-cipa text-white mx-auto mb-4 group-hover:bg-blue-700 transition-colors">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
        </div>
        <h3 class="text-xl font-semibold text-cinza-chumbo mb-2 text-center group-hover:text-azul-cipa transition-colors">Apuração e Resultados</h3>
        <p class="text-gray-600 text-sm text-center">Acompanhe a apuração, encerre a votação e publique os resultados.</p>
    </a>

    <div class="block bg-white p-6 rounded-xl shadow-lg group">
        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-teal-500 text-white mx-auto mb-4">
             <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </div>
        <h3 class="text-xl font-semibold text-cinza-chumbo mb-2 text-center">Gerar Documentos</h3>
        <div class="space-y-2 mt-3">
            <a href="../../reports/gerar_ata_convocacao.php?eleicao_id=<?php echo $comissao_eleicao_id_logado; ?>" target="_blank" class="block text-sm text-center py-2 px-3 bg-teal-100 hover:bg-teal-200 text-teal-700 rounded-md transition-colors">Ata de Convocação (Preview)</a>
            <a href="../../reports/gerar_lista_votantes.php?eleicao_id=<?php echo $comissao_eleicao_id_logado; ?>" target="_blank" class="block text-sm text-center py-2 px-3 bg-teal-100 hover:bg-teal-200 text-teal-700 rounded-md transition-colors">Lista de Votantes (Preview)</a>
            <a href="../../reports/gerar_ata_resultado_posse.php?eleicao_id=<?php echo $comissao_eleicao_id_logado; ?>" target="_blank" class="block text-sm text-center py-2 px-3 bg-teal-100 hover:bg-teal-200 text-teal-700 rounded-md transition-colors">Ata de Resultado e Posse (Preview)</a>
        </div>
    </div>

    <a href="#"
       class="block bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out group opacity-50 cursor-not-allowed"
       title="Em breve">
        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-gray-300 text-gray-700 mx-auto mb-4">
           <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <h3 class="text-xl font-semibold text-cinza-chumbo mb-2 text-center">Configurar Votação</h3>
        <p class="text-gray-600 text-sm text-center">Defina períodos, abra ou encerre a votação online. (Em breve)</p>
    </a>

    <a href="#"
       class="block bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out group opacity-50 cursor-not-allowed"
       title="Em breve">
        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-gray-300 text-gray-700 mx-auto mb-4">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
        </div>
        <h3 class="text-xl font-semibold text-cinza-chumbo mb-2 text-center">Emitir Comunicados</h3>
        <p class="text-gray-600 text-sm text-center">Envie comunicados para os envolvidos na eleição. (Em breve)</p>
    </a>

</div>

<?php
require_once 'includes/footer_painel_comissao.php';
?>

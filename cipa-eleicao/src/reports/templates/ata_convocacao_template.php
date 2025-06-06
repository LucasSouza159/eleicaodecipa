<?php
if (!isset($dados_eleicao) || !is_array($dados_eleicao)) {
    echo "<p class='text-red-500 p-4'>Erro: Dados da eleição não fornecidos para o template.</p>";
    return;
}
extract($dados_eleicao);

$fmt_data_convocacao = isset($data_convocacao) ? date('d/m/Y', strtotime($data_convocacao)) : '[Data não definida]';
$fmt_data_inicio_inscricao = isset($data_inicio_inscricao_candidatos) ? date('d/m/Y \à\s H:i', strtotime($data_inicio_inscricao_candidatos)) : '[Data não definida]';
$fmt_data_fim_inscricao = isset($data_fim_inscricao_candidatos) ? date('d/m/Y \à\s H:i', strtotime($data_fim_inscricao_candidatos)) : '[Data não definida]';
$fmt_data_inicio_votacao = isset($data_inicio_votacao) ? date('d/m/Y \à\s H:i', strtotime($data_inicio_votacao)) : '[Data não definida]';
$fmt_data_fim_votacao = isset($data_fim_votacao) ? date('d/m/Y \à\s H:i', strtotime($data_fim_votacao)) : '[Data não definida]';
$fmt_data_apuracao_prevista = isset($data_apuracao) ? date('d/m/Y \à\s H:i', strtotime($data_apuracao)) : (isset($data_fim_votacao) ? date('d/m/Y \à\s H:i', strtotime($data_fim_votacao)) : '[Data não definida]');
$fmt_data_posse_eleitos = isset($data_posse_eleitos) ? date('d/m/Y', strtotime($data_posse_eleitos)) : '[Data não definida]';

$fmt_ano_referencia = isset($ano_referencia) ? $ano_referencia : '[Ano não definido]';
$proximo_ano = (int)$fmt_ano_referencia + 1;

$nome_empresa_doc = htmlspecialchars($nome_empresa ?? '[Nome da Empresa não definido]');
$cnpj_empresa_doc = htmlspecialchars($cnpj_empresa ?? '[CNPJ da Empresa não definido]');
$endereco_completo_local_doc = htmlspecialchars($endereco_completo_local ?? '[Endereço Completo da Empresa/Filial não definido]');
$titulo_eleicao_doc = htmlspecialchars($titulo_eleicao ?? 'Eleição CIPA');
$local_empresa_doc = htmlspecialchars($cidade_local ?? '[Cidade]') . "/" . htmlspecialchars($estado_local ?? '[UF]');
$membros_comissao_eleitoral_doc = htmlspecialchars($membros_comissao_eleitoral ?? 'Não informada');

// Estilos CSS inline são mantidos para o caso de bibliotecas PDF que não interpretam classes Tailwind.
// Classes Tailwind são adicionadas para melhorar o preview HTML.
$styles_inline = [
    'body' => 'font-family: Arial, sans-serif; font-size: 12px; line-height: 1.6;',
    'h1_pdf' => 'font-size: 16px; font-weight: bold; text-align: center; margin-bottom: 20px;',
    'h2_pdf' => 'font-size: 14px; font-weight: bold; margin-top: 15px; margin-bottom: 10px;',
    'p_pdf' => 'margin-bottom: 10px; text-align: justify;',
    'table_pdf' => 'width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 15px; font-size: 11px;',
    'th_pdf' => 'border: 1px solid #000; padding: 6px; background-color: #f2f2f2; text-align: left;',
    'td_pdf' => 'border: 1px solid #000; padding: 6px;',
    'assinatura_pdf' => 'margin-top: 40px; border-top: 1px solid #000; width: 280px; text-align: center; padding-top: 5px; display:inline-block;',
    'cidade_data_pdf' => 'text-align: right; margin-top: 30px; margin-bottom: 20px;',
];
?>
<div class="text-sm text-gray-800 leading-relaxed" style="<?php echo $styles_inline['body']; ?>">
    <h1 class="text-2xl font-bold text-center mb-6 text-cinza-chumbo" style="<?php echo $styles_inline['h1_pdf']; ?>">EDITAL DE CONVOCAÇÃO PARA ELEIÇÃO DA CIPA</h1>
    <h1 class="text-xl font-bold text-center mb-8 text-cinza-chumbo" style="<?php echo $styles_inline['h1_pdf']; ?>">GESTÃO <?php echo $fmt_ano_referencia; ?>/<?php echo $proximo_ano; ?></h1>

    <p class="mb-4 text-justify" style="<?php echo $styles_inline['p_pdf']; ?>">
        A empresa <strong><?php echo $nome_empresa_doc; ?></strong>, inscrita no CNPJ sob o nº
        <strong><?php echo $cnpj_empresa_doc; ?></strong>, estabelecida em <?php echo $endereco_completo_local_doc; ?>,
        em conformidade com o disposto na Norma Regulamentadora nº 05 (NR-05), aprovada pela Portaria MTb nº 3.214/78 e suas atualizações,
        CONVOCA todos os seus empregados para a eleição dos membros da Comissão Interna de Prevenção de Acidentes e de Assédio - CIPA,
        para a gestão <?php echo $fmt_ano_referencia; ?>/<?php echo $proximo_ano; ?>.
    </p>

    <h2 class="text-lg font-bold mt-6 mb-3 text-cinza-chumbo" style="<?php echo $styles_inline['h2_pdf']; ?>">1. OBJETIVO</h2>
    <p class="mb-4 text-justify" style="<?php echo $styles_inline['p_pdf']; ?>">
        A presente eleição tem como objetivo escolher os representantes dos empregados na CIPA, que terão como atribuição
        principal a prevenção de acidentes e doenças decorrentes do trabalho, e o combate ao assédio sexual e a outras formas de violência no âmbito do trabalho, de modo a tornar compatível permanentemente
        o trabalho com a preservação da vida e a promoção da saúde do trabalhador.
    </p>

    <h2 class="text-lg font-bold mt-6 mb-3 text-cinza-chumbo" style="<?php echo $styles_inline['h2_pdf']; ?>">2. COMISSÃO ELEITORAL</h2>
    <p class="mb-4 text-justify" style="<?php echo $styles_inline['p_pdf']; ?>">
        A eleição será organizada e conduzida pela Comissão Eleitoral designada para este fim, composta pelos seguintes membros: <?php echo $membros_comissao_eleitoral_doc; ?>.
        A Comissão Eleitoral é responsável por garantir a lisura e transparência de todo o processo eleitoral.
    </p>

    <h2 class="text-lg font-bold mt-6 mb-3 text-cinza-chumbo" style="<?php echo $styles_inline['h2_pdf']; ?>">3. CRONOGRAMA ELEITORAL</h2>
    <p class="mb-2 text-justify" style="<?php echo $styles_inline['p_pdf']; ?>">O processo eleitoral seguirá o seguinte cronograma:</p>
    <div class="overflow-x-auto mb-4">
        <table class="min-w-full text-sm text-left text-gray-700" style="<?php echo $styles_inline['table_pdf']; ?>">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    <th scope="col" class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['th_pdf']; ?>">Evento</th>
                    <th scope="col" class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['th_pdf']; ?>">Data e Hora</th>
                </tr>
            </thead>
            <tbody>
                <tr class="bg-white border-b hover:bg-gray-50"><td class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['td_pdf']; ?>">Convocação da Eleição</td><td class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['td_pdf']; ?>"><?php echo $fmt_data_convocacao; ?></td></tr>
                <tr class="bg-white border-b hover:bg-gray-50"><td class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['td_pdf']; ?>">Início das Inscrições de Candidatos</td><td class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['td_pdf']; ?>"><?php echo $fmt_data_inicio_inscricao; ?></td></tr>
                <tr class="bg-white border-b hover:bg-gray-50"><td class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['td_pdf']; ?>">Término das Inscrições de Candidatos</td><td class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['td_pdf']; ?>"><?php echo $fmt_data_fim_inscricao; ?></td></tr>
                <tr class="bg-white border-b hover:bg-gray-50"><td class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['td_pdf']; ?>">Início da Votação</td><td class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['td_pdf']; ?>"><?php echo $fmt_data_inicio_votacao; ?></td></tr>
                <tr class="bg-white border-b hover:bg-gray-50"><td class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['td_pdf']; ?>">Término da Votação</td><td class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['td_pdf']; ?>"><?php echo $fmt_data_fim_votacao; ?></td></tr>
                <tr class="bg-white border-b hover:bg-gray-50"><td class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['td_pdf']; ?>">Apuração dos Votos</td><td class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['td_pdf']; ?>">A partir de <?php echo $fmt_data_apuracao_prevista; ?></td></tr>
                <tr class="bg-white hover:bg-gray-50"><td class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['td_pdf']; ?>">Posse dos Eleitos</td><td class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['td_pdf']; ?>"><?php echo $fmt_data_posse_eleitos; ?></td></tr>
            </tbody>
        </table>
    </div>

    <h2 class="text-lg font-bold mt-6 mb-3 text-cinza-chumbo" style="<?php echo $styles_inline['h2_pdf']; ?>">4. INSCRIÇÃO DE CANDIDATOS</h2>
    <p class="mb-4 text-justify" style="<?php echo $styles_inline['p_pdf']; ?>">
        Os empregados interessados em se candidatar deverão realizar suas inscrições no período de
        <strong><?php echo $fmt_data_inicio_inscricao; ?></strong> até <strong><?php echo $fmt_data_fim_inscricao; ?></strong>.
        As inscrições serão realizadas através do sistema CIPA Fácil Online, acessível em [Link do Sistema ou Intranet], ou diretamente junto à Comissão Eleitoral.
        É vedada a candidatura de empregados que não se enquadrem nos critérios estabelecidos pela NR-05.
    </p>

    <h2 class="text-lg font-bold mt-6 mb-3 text-cinza-chumbo" style="<?php echo $styles_inline['h2_pdf']; ?>">5. VOTAÇÃO</h2>
    <p class="mb-4 text-justify" style="<?php echo $styles_inline['p_pdf']; ?>">
        A votação será realizada eletronicamente através do sistema CIPA Fácil Online, no período de
        <strong><?php echo $fmt_data_inicio_votacao; ?></strong> até <strong><?php echo $fmt_data_fim_votacao; ?></strong>.
        O voto é secreto e individual. Todos os empregados da empresa têm direito a voto, conforme item 5.5.4 da NR-05.
    </p>

    <h2 class="text-lg font-bold mt-6 mb-3 text-cinza-chumbo" style="<?php echo $styles_inline['h2_pdf']; ?>">6. DISPOSIÇÕES GERAIS</h2>
    <p class="mb-4 text-justify" style="<?php echo $styles_inline['p_pdf']; ?>">
        Casos omissos neste edital serão resolvidos pela Comissão Eleitoral, com base na NR-05 e demais legislações pertinentes.
        Este edital será afixado em local de fácil acesso e visualização pelos empregados, bem como disponibilizado nos meios eletrônicos da empresa.
    </p>

    <div class="mt-10 mb-6 text-right" style="<?php echo $styles_inline['cidade_data_pdf']; ?>">
        <?php echo $local_empresa_doc; ?>, <?php echo $fmt_data_convocacao; ?>.
    </div>

    <div class="mt-16 text-center space-y-12">
        <div class="inline-block" style="<?php echo $styles_inline['assinatura_pdf']; ?>">
            <span class="block border-b border-gray-700 pb-1"></span>
            Presidente da Comissão Eleitoral
        </div>
        <div class="inline-block ml-12" style="<?php echo $styles_inline['assinatura_pdf']; ?>">
            <span class="block border-b border-gray-700 pb-1"></span>
            Representante da Empresa (se aplicável)
        </div>
    </div>
</div>

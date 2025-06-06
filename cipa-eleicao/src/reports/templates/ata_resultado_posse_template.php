<?php
if (!isset($dados_resultado) || !is_array($dados_resultado)) {
    echo "<p class='text-red-500 p-4'>Erro: Dados do resultado não fornecidos.</p>";
    return;
}

extract($dados_resultado['dados_eleicao']);
$resultados_votacao = $dados_resultado['resultados_votacao'];
$candidatos_eleitos_titulares = $dados_resultado['candidatos_eleitos_titulares'];
$candidatos_eleitos_suplentes = $dados_resultado['candidatos_eleitos_suplentes'];
$membros_comissao_eleitoral = $dados_resultado['dados_comissao_eleitoral'] ?? [];

$nome_empresa_doc = htmlspecialchars($nome_empresa ?? '[Nome da Empresa]');
$cnpj_empresa_doc = htmlspecialchars($cnpj_empresa ?? '[CNPJ]');
$endereco_empresa_doc = htmlspecialchars($endereco_empresa_completo ?? '[Endereço da Empresa]');
$titulo_eleicao_doc = htmlspecialchars($titulo_eleicao ?? 'Eleição CIPA');
$ano_ref_doc = htmlspecialchars($ano_referencia ?? '[Ano]');
$proximo_ano_doc = (int)$ano_ref_doc + 1;
$gestao_doc = $ano_ref_doc . "/" . $proximo_ano_doc;

$data_convocacao_fmt = isset($data_convocacao) ? date('d/m/Y', strtotime($data_convocacao)) : '[Data Convocação]';
$data_inicio_votacao_fmt = isset($data_inicio_votacao) ? date('d/m/Y \à\s H:i', strtotime($data_inicio_votacao)) : '[Data Início Votação]';
$data_fim_votacao_fmt = isset($data_fim_votacao) ? date('d/m/Y \à\s H:i', strtotime($data_fim_votacao)) : '[Data Fim Votação]';
$data_apuracao_fmt = isset($data_apuracao) ? date('d/m/Y \à\s H:i', strtotime($data_apuracao)) : '[Data Apuração]';
$data_posse_fmt = isset($data_posse_eleitos) ? date('d/m/Y', strtotime($data_posse_eleitos)) : '[Data Posse]';
$local_geracao_doc = htmlspecialchars($cidade_local ?? '[Cidade]') . "/" . htmlspecialchars($estado_local ?? '[UF]');

$total_votantes_doc = $resultados_votacao['total_votantes'] ?? 0;
// $total_empregados_aptos_doc = $dados_resultado['total_empregados_aptos'] ?? 'N/D'; // Implementar busca
// $total_ausentes_doc = $total_empregados_aptos_doc !== 'N/D' ? ($total_empregados_aptos_doc - $total_votantes_doc) : 'N/D';

// Estilos inline para PDF, classes Tailwind para preview HTML
$styles_inline = [
    'body' => 'font-family: Arial, sans-serif; font-size: 12px; line-height: 1.5;',
    'h1_pdf' => 'font-size: 15px; font-weight: bold; text-align: center; margin-bottom: 8px;',
    'h2_pdf' => 'font-size: 13px; font-weight: bold; margin-top: 18px; margin-bottom: 8px;',
    'p_pdf' => 'margin-bottom: 10px; text-align: justify; text-indent: 2em;',
    'table_pdf' => 'width: 100%; border-collapse: collapse; margin-top: 12px; margin-bottom: 12px; font-size:10px;',
    'th_pdf' => 'border: 1px solid #000; padding: 5px; background-color: #e0e0e0; text-align: center; font-weight:bold;',
    'td_pdf' => 'border: 1px solid #000; padding: 5px; text-align: left;',
    'td_votos_pdf' => 'border: 1px solid #000; padding: 5px; text-align: center;',
    'assinatura_area_pdf' => 'margin-top: 40px;',
    'assinatura_linha_pdf' => 'margin-top: 35px; border-top: 1px solid #000; width: 250px; text-align: center; padding-top: 4px; display:inline-block; page-break-inside: avoid;',
    'assinatura_nome_pdf' => 'text-align: center; font-size:11px;',
    'assinatura_cargo_pdf' => 'text-align: center; font-size: 10px;',
    'cidade_data_pdf' => 'text-align: right; margin-top: 25px; margin-bottom: 15px;',
    'lista_ul_pdf' => 'list-style-type: disc; padding-left: 30px; margin-bottom:10px;',
];
?>
<div class="text-sm text-gray-800 leading-relaxed" style="<?php echo $styles_inline['body']; ?>">
    <h1 class="text-xl font-bold text-center mb-2 text-cinza-chumbo" style="<?php echo $styles_inline['h1_pdf']; ?>">ATA DE APURAÇÃO, ELEIÇÃO E POSSE DA COMISSÃO INTERNA DE PREVENÇÃO DE ACIDENTES E DE ASSÉDIO - CIPA</h1>
    <h1 class="text-lg font-bold text-center mb-6 text-cinza-chumbo" style="<?php echo $styles_inline['h1_pdf']; ?>">EMPRESA: <?php echo $nome_empresa_doc; ?> | GESTÃO <?php echo $gestao_doc; ?></h1>

    <p class="mb-4 text-justify indent-8" style="<?php echo $styles_inline['p_pdf']; ?>">
        Aos <?php echo date('d', strtotime($data_apuracao)); ?> dias do mês de <?php echo mb_strtolower(strftime('%B', strtotime($data_apuracao))); ?>
        do ano de <?php echo date('Y', strtotime($data_apuracao)); ?>, às <?php echo date('H:i', strtotime($data_apuracao)); ?>,
        nas instalações da empresa <?php echo $nome_empresa_doc; ?>, CNPJ nº <?php echo $cnpj_empresa_doc; ?>,
        localizada em <?php echo $endereco_empresa_doc; ?>, reuniram-se os membros da Comissão Eleitoral,
        designados por [Edital de Convocação ou documento pertinente], Srs.(as)
        <?php
            $nomes_comissao_fmt = [];
            foreach($membros_comissao_eleitoral as $membro_ce) { $nomes_comissao_fmt[] = htmlspecialchars($membro_ce['nome_completo']) . " (" . htmlspecialchars($membro_ce['papel_comissao']) . ")"; }
            echo implode(', ', $nomes_comissao_fmt);
        ?>,
        para proceder à apuração dos votos da eleição da CIPA, gestão <?php echo $gestao_doc; ?>,
        convocada por edital em <?php echo $data_convocacao_fmt; ?>.
    </p>

    <h2 class="text-base font-bold mt-5 mb-2 text-cinza-chumbo" style="<?php echo $styles_inline['h2_pdf']; ?>">DO PROCESSO ELEITORAL E APURAÇÃO DOS VOTOS</h2>
    <p class="mb-4 text-justify indent-8" style="<?php echo $styles_inline['p_pdf']; ?>">
        O processo de votação ocorreu no período de <?php echo $data_inicio_votacao_fmt; ?>
        até <?php echo $data_fim_votacao_fmt; ?>, utilizando o sistema de votação eletrônica CIPA Fácil Online.
        Compareceram para votar um total de <strong><?php echo $total_votantes_doc; ?></strong> empregados.
        <!-- de um total de [<?php // echo $total_empregados_aptos_doc; ?>] empregados aptos a votar,
        registrando-se [<?php // echo $total_ausentes_doc; ?>] ausências. -->
    </p>
    <p class="mb-4 text-justify indent-8" style="<?php echo $styles_inline['p_pdf']; ?>">
        Após o encerramento da votação, procedeu-se à apuração dos votos, constatando-se os seguintes resultados:
    </p>
    <div class="overflow-x-auto my-4">
        <table class="w-full text-xs text-left text-gray-700" style="<?php echo $styles_inline['table_pdf']; ?>">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    <th scope="col" class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['th_pdf']; ?>">Candidato (Nome na Urna)</th>
                    <th scope="col" class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['th_pdf']; ?>">Nome Completo</th>
                    <th scope="col" class="px-4 py-2 border border-gray-400 text-center" style="<?php echo $styles_inline['th_pdf']; ?>">Votos Recebidos</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($resultados_votacao['candidatos'])): ?>
                    <?php foreach ($resultados_votacao['candidatos'] as $candidato_res): ?>
                    <tr class="bg-white border-b hover:bg-gray-50"><td class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['td_pdf']; ?>"><?php echo htmlspecialchars($candidato_res['nome_urna'] ?? $candidato_res['nome_completo']); ?></td><td class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['td_pdf']; ?>"><?php echo htmlspecialchars($candidato_res['nome_completo']); ?></td><td class="px-4 py-2 border border-gray-400 text-center" style="<?php echo $styles_inline['td_votos_pdf']; ?>"><?php echo $candidato_res['total_votos']; ?></td></tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr class="bg-white border-b"><td colspan="3" class="px-4 py-2 border border-gray-400 text-center" style="<?php echo $styles_inline['td_pdf']; ?>">Nenhum voto computado para candidatos.</td></tr>
                <?php endif; ?>
                <tr class="bg-gray-50"><td colspan="2" class="px-4 py-2 border border-gray-400 text-right font-bold" style="<?php echo $styles_inline['td_pdf']; ?>">Votos em Branco:</td><td class="px-4 py-2 border border-gray-400 text-center" style="<?php echo $styles_inline['td_votos_pdf']; ?>"><?php echo $resultados_votacao['branco'] ?? 0; ?></td></tr>
                <tr class="bg-gray-50"><td colspan="2" class="px-4 py-2 border border-gray-400 text-right font-bold" style="<?php echo $styles_inline['td_pdf']; ?>">Votos Nulos:</td><td class="px-4 py-2 border border-gray-400 text-center" style="<?php echo $styles_inline['td_votos_pdf']; ?>"><?php echo $resultados_votacao['nulo'] ?? 0; ?></td></tr>
                <tr class="bg-gray-100"><td colspan="2" class="px-4 py-2 border border-gray-400 text-right font-bold" style="<?php echo $styles_inline['td_pdf']; ?>">Total de Votos Apurados:</td><td class="px-4 py-2 border border-gray-400 text-center font-bold" style="<?php echo $styles_inline['td_votos_pdf']; ?>"><?php echo $total_votantes_doc; ?></td></tr>
            </tbody>
        </table>
    </div>

    <h2 class="text-base font-bold mt-5 mb-2 text-cinza-chumbo" style="<?php echo $styles_inline['h2_pdf']; ?>">DOS ELEITOS E SUPLENTES (REPRESENTANTES DOS EMPREGADOS)</h2>
    <p class="mb-2 text-justify indent-8" style="<?php echo $styles_inline['p_pdf']; ?>">
        De acordo com os resultados apurados e o dimensionamento previsto na NR-05 para esta empresa (<?php echo htmlspecialchars($numero_titulares_previstos ?? 'N/D'); ?> titulares e <?php echo htmlspecialchars($numero_suplentes_previstos ?? 'N/D'); ?> suplentes), foram eleitos os seguintes representantes dos empregados:
    </p>

    <h3 class="text-sm font-semibold mt-3 mb-1 text-cinza-chumbo" style="font-size: 12.5px; font-weight: bold; margin-top: 12px;">Titulares Eleitos:</h3>
    <?php if (!empty($candidatos_eleitos_titulares)): ?>
        <ul class="list-decimal pl-10 mb-3 text-sm" style="<?php echo $styles_inline['lista_ul_pdf']; ?>">
            <?php foreach ($candidatos_eleitos_titulares as $titular): ?>
                <li><?php echo htmlspecialchars($titular['nome_completo']); ?> (CPF: <?php echo htmlspecialchars($titular['cpf']); ?>) - <?php echo $titular['total_votos']; ?> votos.</li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="ml-8 mb-3 text-sm italic" style="margin-left: 2em; margin-bottom: 10px; text-align: justify; font-style: italic;">Nenhum titular eleito (ou número de titulares não definido/atingido).</p>
    <?php endif; ?>

    <h3 class="text-sm font-semibold mt-3 mb-1 text-cinza-chumbo" style="font-size: 12.5px; font-weight: bold; margin-top: 10px;">Suplentes Eleitos:</h3>
    <?php if (!empty($candidatos_eleitos_suplentes)): ?>
        <ul class="list-decimal pl-10 mb-3 text-sm" style="<?php echo $styles_inline['lista_ul_pdf']; ?>">
            <?php foreach ($candidatos_eleitos_suplentes as $suplente): ?>
                <li><?php echo htmlspecialchars($suplente['nome_completo']); ?> (CPF: <?php echo htmlspecialchars($suplente['cpf']); ?>) - <?php echo $suplente['total_votos']; ?> votos.</li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="ml-8 mb-3 text-sm italic" style="margin-left: 2em; margin-bottom: 10px; text-align: justify; font-style: italic;">Nenhum suplente eleito (ou número de suplentes não definido/atingido).</p>
    <?php endif; ?>

    <p class="mb-4 text-justify indent-8" style="<?php echo $styles_inline['p_pdf']; ?>">
        Os representantes do empregador, designados conforme [documento de designação da empresa], são: [Listar nomes dos representantes do empregador - Titulares e Suplentes, se houver campos para isso no sistema ou adicionar manualmente].
    </p>

    <h2 class="text-base font-bold mt-5 mb-2 text-cinza-chumbo" style="<?php echo $styles_inline['h2_pdf']; ?>">DA POSSE DOS MEMBROS DA CIPA</h2>
    <p class="mb-4 text-justify indent-8" style="<?php echo $styles_inline['p_pdf']; ?>">
        Aos <?php echo date('d', strtotime($data_posse_fmt)); ?> dias do mês de <?php echo mb_strtolower(strftime('%B', strtotime($data_posse_fmt))); ?>
        do ano de <?php echo date('Y', strtotime($data_posse_fmt)); ?>, nesta mesma localidade (ou conforme local designado), tomaram posse os membros eleitos e designados da CIPA,
        para a gestão <?php echo $gestao_doc; ?>, comprometendo-se a cumprir as atribuições que lhes são conferidas pela NR-05.
        A presente ata, após lida e aprovada, vai assinada pelos membros da Comissão Eleitoral e pelos empossados.
    </p>

    <div class="mt-12" style="<?php echo $styles_inline['assinatura_area_pdf']; ?>">
        <p class="text-center font-semibold mb-8 text-gray-700">Assinaturas dos Membros Eleitos (Representantes dos Empregados):</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-10">
            <?php foreach (array_merge($candidatos_eleitos_titulares, $candidatos_eleitos_suplentes) as $eleito): ?>
                <div class="text-center page-break-inside-avoid" style="page-break-inside: avoid;">
                     <div class="border-b border-gray-700 h-8 mb-1" style="border-bottom: 1px solid #000; height: 20px; margin-bottom: 4px;">&nbsp;</div>
                     <div class="text-xs" style="<?php echo $styles_inline['assinatura_nome_pdf']; ?>"><?php echo htmlspecialchars($eleito['nome_completo']); ?></div>
                     <div class="text-xs italic" style="<?php echo $styles_inline['assinatura_cargo_pdf']; ?>">
                        (<?php echo in_array($eleito, $candidatos_eleitos_titulares, true) ? 'Titular Eleito' : 'Suplente Eleito'; ?>)
                     </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="mt-12" style="<?php echo $styles_inline['assinatura_area_pdf']; ?>">
        <p class="text-center font-semibold mb-8 text-gray-700">Assinaturas dos Membros Designados (Representantes do Empregador):</p>
        <p class="text-center text-sm italic text-gray-500">(Espaço para assinaturas dos membros designados pelo empregador - a serem coletadas fisicamente ou via sistema de assinatura digital externo)</p>
        <!-- Exemplo de placeholders para designados -->
        <?php
            $num_designados_total = ($numero_titulares_previstos ?? 0) + ($numero_suplentes_previstos ?? 0);
            if ($num_designados_total == 0 && count($candidatos_eleitos_titulares) == 0) $num_designados_total = 2; // Mínimo para exemplo
            for ($i=0; $i < $num_designados_total; $i++):
        ?>
             <div class="inline-block text-center mr-4 mb-10 page-break-inside-avoid" style="width: 250px; page-break-inside: avoid;">
                 <div class="border-b border-gray-700 h-8 mb-1" style="border-bottom: 1px solid #000; height: 20px; margin-bottom: 4px;">&nbsp;</div>
                 <div class="text-xs italic" style="<?php echo $styles_inline['assinatura_cargo_pdf']; ?>">(Membro Designado <?php echo ($i < ($numero_titulares_previstos ?? 0) ? 'Titular' : 'Suplente'); ?>)</div>
            </div>
        <?php endfor; ?>
    </div>

    <div class="mt-12" style="<?php echo $styles_inline['assinatura_area_pdf']; ?>">
        <p class="text-center font-semibold mb-8 text-gray-700">Assinaturas da Comissão Eleitoral:</p>
         <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-10">
            <?php foreach($membros_comissao_eleitoral as $membro_ce): ?>
                 <div class="text-center page-break-inside-avoid" style="page-break-inside: avoid;">
                     <div class="border-b border-gray-700 h-8 mb-1" style="border-bottom: 1px solid #000; height: 20px; margin-bottom: 4px;">&nbsp;</div>
                     <div class="text-xs" style="<?php echo $styles_inline['assinatura_nome_pdf']; ?>"><?php echo htmlspecialchars($membro_ce['nome_completo']); ?></div>
                     <div class="text-xs italic" style="<?php echo $styles_inline['assinatura_cargo_pdf']; ?>">(<?php echo htmlspecialchars($membro_ce['papel_comissao']); ?>)</div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="mt-10 text-right" style="<?php echo $styles_inline['cidade_data_pdf']; ?>">
        <?php echo $local_geracao_doc; ?>, <?php echo date('d \d\e F \d\e Y', strtotime($data_posse_fmt)); ?>.
    </div>
</div>

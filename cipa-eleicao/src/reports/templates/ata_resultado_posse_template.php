<?php
// Template para Ata de Resultado da Eleição e Posse da CIPA
// Espera um array $dados_resultado com todas as informações necessárias.

if (!isset($dados_resultado) || !is_array($dados_resultado)) {
    echo "<p>Erro: Dados do resultado não fornecidos para o template.</p>";
    return;
}

extract($dados_resultado['dados_eleicao']);
$resultados_votacao = $dados_resultado['resultados_votacao'];
$candidatos_eleitos_titulares = $dados_resultado['candidatos_eleitos_titulares'];
$candidatos_eleitos_suplentes = $dados_resultado['candidatos_eleitos_suplentes'];
$membros_comissao_eleitoral = $dados_resultado['dados_comissao_eleitoral'] ?? []; // Array de nomes ou objetos

// Formatação de dados
$nome_empresa = htmlspecialchars($nome_empresa ?? '[Nome da Empresa]');
$cnpj_empresa = htmlspecialchars($cnpj_empresa ?? '[CNPJ]');
$endereco_empresa = htmlspecialchars($endereco_empresa ?? '[Endereço da Empresa]'); // Adicionar aos dados buscados
$titulo_eleicao_doc = htmlspecialchars($titulo_eleicao ?? 'Eleição CIPA');
$ano_ref = htmlspecialchars($ano_referencia ?? '[Ano]');
$proximo_ano = (int)$ano_ref + 1;
$gestao = $ano_ref . "/" . $proximo_ano;

$data_apuracao_fmt = isset($data_apuracao) ? date('d/m/Y \à\s H:i', strtotime($data_apuracao)) : '[Data Apuração]';
$data_posse_fmt = isset($data_posse_eleitos) ? date('d/m/Y', strtotime($data_posse_eleitos)) : '[Data Posse]';
$local_geracao = htmlspecialchars($cidade_empresa ?? '[Cidade]') . "/" . htmlspecialchars($estado_empresa ?? '[UF]'); // Adicionar aos dados buscados

$total_votantes = $resultados_votacao['total_votantes'] ?? 0;
// $total_empregados_aptos = $dados_resultado['total_empregados_aptos'] ?? 0; // Adicionar aos dados buscados
// $total_ausentes = $total_empregados_aptos - $total_votantes;

$styles = [
    'body' => 'font-family: Arial, sans-serif; font-size: 12px; line-height: 1.6;',
    'h1' => 'font-size: 16px; font-weight: bold; text-align: center; margin-bottom: 10px;',
    'h2' => 'font-size: 14px; font-weight: bold; margin-top: 20px; margin-bottom: 10px;',
    'p' => 'margin-bottom: 10px; text-align: justify; text-indent: 2em;', // Adiciona justificado e indentação
    'table' => 'width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 15px;',
    'th_resultado' => 'border: 1px solid #000; padding: 6px; background-color: #e0e0e0; text-align: center;',
    'td_resultado' => 'border: 1px solid #000; padding: 6px; text-align: left;',
    'td_votos' => 'border: 1px solid #000; padding: 6px; text-align: center;',
    'assinatura_area' => 'margin-top: 50px;',
    'assinatura_linha' => 'margin-top: 40px; border-top: 1px solid #000; width: 280px; text-align: center; padding-top: 5px;',
    'assinatura_nome' => 'text-align: center;',
    'assinatura_cargo' => 'text-align: center; font-size: 10px;',
    'cidade_data' => 'text-align: right; margin-top: 30px; margin-bottom: 20px;',
    'lista_ul' => 'list-style-type: disc; padding-left: 20px;',
    'paragrafo_recuado' => 'margin-left: 2em; margin-bottom: 10px; text-align: justify;',
];

?>
<div style="<?php echo $styles['body']; ?>">
    <h1 style="<?php echo $styles['h1']; ?>">ATA DE RESULTADO DA ELEIÇÃO E POSSE DA COMISSÃO INTERNA DE PREVENÇÃO DE ACIDENTES - CIPA</h1>
    <h1 style="<?php echo $styles['h1']; ?>">GESTÃO <?php echo $gestao; ?></h1>

    <p style="<?php echo $styles['p']; ?>">
        Aos <?php echo date('d', strtotime($data_apuracao)); ?> dias do mês de <?php echo strftime('%B', strtotime($data_apuracao)); ?>
        do ano de <?php echo date('Y', strtotime($data_apuracao)); ?>, às <?php echo date('H:i', strtotime($data_apuracao)); ?>,
        nas instalações da empresa <strong><?php echo $nome_empresa; ?></strong>, CNPJ nº <?php echo $cnpj_empresa; ?>,
        localizada em <?php echo $endereco_empresa; ?>, reuniram-se os membros da Comissão Eleitoral,
        designados por [Forma de designação da Comissão Eleitoral], Srs.(as)
        <?php
            $nomes_comissao = [];
            foreach($membros_comissao_eleitoral as $membro_ce) { $nomes_comissao[] = htmlspecialchars($membro_ce['nome_completo']) . " (" . htmlspecialchars($membro_ce['papel_comissao']) . ")"; }
            echo implode(', ', $nomes_comissao);
        ?>,
        para proceder à apuração dos votos da eleição da CIPA, gestão <?php echo $gestao; ?>,
        convocada por edital em <?php echo date('d/m/Y', strtotime($data_convocacao)); ?>.
    </p>

    <h2 style="<?php echo $styles['h2']; ?>">DO PROCESSO ELEITORAL E APURAÇÃO</h2>
    <p style="<?php echo $styles['p']; ?>">
        O processo de votação ocorreu no período de <?php echo date('d/m/Y H:i', strtotime($data_inicio_votacao)); ?>
        até <?php echo date('d/m/Y H:i', strtotime($data_fim_votacao)); ?>, utilizando o sistema de votação eletrônica CIPA Fácil Online.
        Compareceram para votar um total de <strong><?php echo $total_votantes; ?></strong> empregados, de um total de
        [<?php echo $dados_resultado['total_empregados_aptos'] ?? 'N/D'; ?>] empregados aptos a votar,
        registrando-se [<?php echo ($dados_resultado['total_empregados_aptos'] ?? 0) - $total_votantes; ?>] ausências.
    </p>
    <p style="<?php echo $styles['p']; ?>">
        Após o encerramento da votação, procedeu-se à apuração dos votos, constatando-se os seguintes resultados:
    </p>
    <table style="<?php echo $styles['table']; ?>">
        <thead>
            <tr>
                <th style="<?php echo $styles['th_resultado']; ?>">Candidato (Nome na Urna)</th>
                <th style="<?php echo $styles['th_resultado']; ?>">Nome Completo</th>
                <th style="<?php echo $styles['th_resultado']; ?>">Nº de Votos Recebidos</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($resultados_votacao['candidatos'])): ?>
                <?php foreach ($resultados_votacao['candidatos'] as $candidato_res): ?>
                <tr>
                    <td style="<?php echo $styles['td_resultado']; ?>"><?php echo htmlspecialchars($candidato_res['nome_urna'] ?? $candidato_res['funcionario_nome']); ?></td>
                    <td style="<?php echo $styles['td_resultado']; ?>"><?php echo htmlspecialchars($candidato_res['funcionario_nome']); ?></td>
                    <td style="<?php echo $styles['td_votos']; ?>"><?php echo $candidato_res['total_votos']; ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="<?php echo $styles['td_resultado']; ?> text-align:center;">Nenhum voto computado para candidatos.</td>
                </tr>
            <?php endif; ?>
            <tr>
                <td colspan="2" style="<?php echo $styles['td_resultado']; ?> text-align:right; font-weight:bold;">Votos em Branco:</td>
                <td style="<?php echo $styles['td_votos']; ?>"><?php echo $resultados_votacao['branco'] ?? 0; ?></td>
            </tr>
            <tr>
                <td colspan="2" style="<?php echo $styles['td_resultado']; ?> text-align:right; font-weight:bold;">Votos Nulos:</td>
                <td style="<?php echo $styles['td_votos']; ?>"><?php echo $resultados_votacao['nulo'] ?? 0; ?></td>
            </tr>
            <tr>
                <td colspan="2" style="<?php echo $styles['td_resultado']; ?> text-align:right; font-weight:bold;">Total de Votos Apurados (incluindo brancos/nulos):</td>
                <td style="<?php echo $styles['td_votos']; ?>"><?php echo $total_votantes; ?></td>
            </tr>
        </tbody>
    </table>

    <h2 style="<?php echo $styles['h2']; ?>">DOS ELEITOS E SUPLENTES (REPRESENTANTES DOS EMPREGADOS)</h2>
    <p style="<?php echo $styles['p']; ?>">
        De acordo com os resultados apurados e o dimensionamento previsto na NR-05 para esta empresa, foram eleitos os seguintes representantes dos empregados:
    </p>

    <h3 style="font-size: 13px; font-weight: bold; margin-top: 15px;">Titulares:</h3>
    <?php if (!empty($candidatos_eleitos_titulares)): ?>
        <ul style="<?php echo $styles['lista_ul']; ?>">
            <?php foreach ($candidatos_eleitos_titulares as $titular): ?>
                <li><?php echo htmlspecialchars($titular['nome_completo']); ?> (CPF: <?php echo htmlspecialchars($titular['cpf']); ?>) - <?php echo $titular['total_votos']; ?> votos.</li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p style="<?php echo $styles['paragrafo_recuado']; ?>">Nenhum titular eleito (ou número de titulares não definido).</p>
    <?php endif; ?>

    <h3 style="font-size: 13px; font-weight: bold; margin-top: 10px;">Suplentes:</h3>
    <?php if (!empty($candidatos_eleitos_suplentes)): ?>
        <ul style="<?php echo $styles['lista_ul']; ?>">
            <?php foreach ($candidatos_eleitos_suplentes as $suplente): ?>
                <li><?php echo htmlspecialchars($suplente['nome_completo']); ?> (CPF: <?php echo htmlspecialchars($suplente['cpf']); ?>) - <?php echo $suplente['total_votos']; ?> votos.</li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p style="<?php echo $styles['paragrafo_recuado']; ?>">Nenhum suplente eleito (ou número de suplentes não definido).</p>
    <?php endif; ?>

    <p style="<?php echo $styles['p']; ?>">
        Os representantes do empregador, designados conforme [Forma de designação dos representantes do empregador], são: [Listar nomes dos representantes do empregador - Titulares e Suplentes].
    </p>

    <h2 style="<?php echo $styles['h2']; ?>">DA POSSE DOS MEMBROS DA CIPA</h2>
    <p style="<?php echo $styles['p']; ?>">
        Aos <?php echo date('d', strtotime($data_posse_fmt)); ?> dias do mês de <?php echo strftime('%B', strtotime($data_posse_fmt)); ?>
        do ano de <?php echo date('Y', strtotime($data_posse_fmt)); ?>, nesta mesma localidade, tomaram posse os membros eleitos e designados da CIPA,
        para a gestão <?php echo $gestao; ?>, comprometendo-se a cumprir as atribuições que lhes são conferidas pela NR-05.
        A presente ata, após lida e aprovada, vai assinada pelos membros da Comissão Eleitoral e pelos empossados.
    </p>

    <div style="<?php echo $styles['assinatura_area']; ?>">
        <p style="text-align:center; font-weight:bold; margin-bottom:30px;">Assinaturas dos Membros Eleitos (Representantes dos Empregados):</p>
        <?php foreach ($candidatos_eleitos_titulares as $eleito): ?>
            <div style="display:inline-block; margin-right: 20px; margin-bottom:30px;">
                 <div style="<?php echo $styles['assinatura_linha']; ?>">&nbsp;</div>
                 <div style="<?php echo $styles['assinatura_nome']; ?>"><?php echo htmlspecialchars($eleito['nome_completo']); ?></div>
                 <div style="<?php echo $styles['assinatura_cargo']; ?>">(Titular)</div>
            </div>
        <?php endforeach; ?>
        <?php foreach ($candidatos_eleitos_suplentes as $eleito): ?>
             <div style="display:inline-block; margin-right: 20px; margin-bottom:30px;">
                 <div style="<?php echo $styles['assinatura_linha']; ?>">&nbsp;</div>
                 <div style="<?php echo $styles['assinatura_nome']; ?>"><?php echo htmlspecialchars($eleito['nome_completo']); ?></div>
                 <div style="<?php echo $styles['assinatura_cargo']; ?>">(Suplente)</div>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="<?php echo $styles['assinatura_area']; ?>">
        <p style="text-align:center; font-weight:bold; margin-bottom:30px;">Assinaturas dos Membros Designados (Representantes do Empregador):</p>
        <p style="text-align:center; font-style:italic;">(Espaço para assinaturas dos membros designados pelo empregador)</p>
        <?php for ($i=0; $i < ($numero_titulares_previstos ?? 0); $i++): // Exemplo de placeholders ?>
             <div style="display:inline-block; margin-right: 20px; margin-bottom:30px;">
                 <div style="<?php echo $styles['assinatura_linha']; ?>">&nbsp;</div>
                 <div style="<?php echo $styles['assinatura_cargo']; ?>">(Titular Designado)</div>
            </div>
        <?php endfor; ?>
         <?php for ($i=0; $i < ($numero_suplentes_previstos ?? 0); $i++): // Exemplo de placeholders ?>
             <div style="display:inline-block; margin-right: 20px; margin-bottom:30px;">
                 <div style="<?php echo $styles['assinatura_linha']; ?>">&nbsp;</div>
                 <div style="<?php echo $styles['assinatura_cargo']; ?>">(Suplente Designado)</div>
            </div>
        <?php endfor; ?>
    </div>


    <div style="<?php echo $styles['assinatura_area']; ?>">
        <p style="text-align:center; font-weight:bold; margin-bottom:30px;">Assinaturas da Comissão Eleitoral:</p>
        <?php foreach($membros_comissao_eleitoral as $membro_ce): ?>
             <div style="display:inline-block; margin-right: 20px; margin-bottom:30px;">
                 <div style="<?php echo $styles['assinatura_linha']; ?>">&nbsp;</div>
                 <div style="<?php echo $styles['assinatura_nome']; ?>"><?php echo htmlspecialchars($membro_ce['nome_completo']); ?></div>
                 <div style="<?php echo $styles['assinatura_cargo']; ?>">(<?php echo htmlspecialchars($membro_ce['papel_comissao']); ?>)</div>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="<?php echo $styles['cidade_data']; ?>">
        <?php echo $local_geracao; ?>, <?php echo date('d \d\e F \d\e Y', strtotime($data_posse_fmt)); ?>.
    </div>
</div>

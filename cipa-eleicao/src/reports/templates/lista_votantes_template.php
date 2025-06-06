<?php
if (!isset($dados_votacao) || !is_array($dados_votacao) ||
    !isset($dados_votacao['dados_eleicao']) || !isset($dados_votacao['lista_votantes'])) {
    echo "<p class='text-red-500 p-4'>Erro: Dados da votação ou da eleição não fornecidos para o template.</p>";
    return;
}

extract($dados_votacao['dados_eleicao']);
$lista_votantes = $dados_votacao['lista_votantes'];
$total_votantes = count($lista_votantes);

$titulo_eleicao_doc = htmlspecialchars($titulo_eleicao ?? 'Eleição CIPA');
$nome_empresa_doc = htmlspecialchars($nome_empresa ?? '[Nome da Empresa]');
$ano_referencia_doc = htmlspecialchars($ano_referencia ?? '[Ano]');
$proximo_ano_doc = (int)$ano_referencia_doc + 1;

// Estilos inline para PDF, classes Tailwind para preview HTML
$styles_inline = [
    'body' => 'font-family: Arial, sans-serif; font-size: 11px; line-height: 1.5;',
    'h1_pdf' => 'font-size: 15px; font-weight: bold; text-align: center; margin-bottom: 5px;',
    'h2_pdf' => 'font-size: 13px; font-weight: bold; text-align: center; margin-bottom: 15px;',
    'table_pdf' => 'width: 100%; border-collapse: collapse; margin-top: 10px; font-size:10px;',
    'th_pdf' => 'border: 1px solid #333; padding: 5px; background-color: #f0f0f0; text-align: left; font-weight: bold;',
    'td_pdf' => 'border: 1px solid #333; padding: 5px;',
    'footer_info_pdf' => 'margin-top: 20px; font-size: 9px; text-align: right;',
    'total_pdf' => 'font-weight: bold; font-size: 11px; margin-top: 10px;'
];
?>
<div class="text-sm text-gray-800 leading-relaxed" style="<?php echo $styles_inline['body']; ?>">
    <h1 class="text-xl font-bold text-center mb-2 text-cinza-chumbo" style="<?php echo $styles_inline['h1_pdf']; ?>"><?php echo $nome_empresa_doc; ?></h1>
    <h2 class="text-lg font-semibold text-center mb-6 text-cinza-chumbo" style="<?php echo $styles_inline['h2_pdf']; ?>">
        LISTA DE VOTANTES - <?php echo $titulo_eleicao_doc; ?> (GESTÃO <?php echo $ano_referencia_doc; ?>/<?php echo $proximo_ano_doc; ?>)
    </h2>

    <?php if (empty($lista_votantes)): ?>
        <p class="text-center text-gray-600 py-4">Nenhum votante registrado para esta eleição até o momento.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-gray-700" style="<?php echo $styles_inline['table_pdf']; ?>">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th scope="col" class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['th_pdf']; ?>">#</th>
                        <th scope="col" class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['th_pdf']; ?>">Nome Completo do Votante</th>
                        <th scope="col" class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['th_pdf']; ?>">CPF</th>
                        <th scope="col" class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['th_pdf']; ?>">Data e Hora do Voto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $count = 1; foreach ($lista_votantes as $votante): ?>
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-4 py-2 border border-gray-400 text-center" style="<?php echo $styles_inline['td_pdf']; ?>"><?php echo $count++; ?></td>
                            <td class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['td_pdf']; ?>"><?php echo htmlspecialchars($votante['nome_completo']); ?></td>
                            <td class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['td_pdf']; ?>"><?php echo htmlspecialchars($votante['cpf']); // TODO: Considerar mascarar o CPF ?></td>
                            <td class="px-4 py-2 border border-gray-400" style="<?php echo $styles_inline['td_pdf']; ?>"><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($votante['data_hora_voto']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="mt-4 font-bold text-gray-700" style="<?php echo $styles_inline['total_pdf']; ?>">Total de Votantes: <?php echo $total_votantes; ?></p>
    <?php endif; ?>

    <div class="mt-6 text-xs text-gray-600 text-right" style="<?php echo $styles_inline['footer_info_pdf']; ?>">
        Relatório gerado em: <?php echo date('d/m/Y H:i:s'); ?>
    </div>
</div>

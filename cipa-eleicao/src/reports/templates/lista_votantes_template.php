<?php
// Template para Lista de Votantes da Eleição CIPA
// Espera um array $dados_votacao contendo $dados_eleicao e $lista_votantes.

if (!isset($dados_votacao) || !is_array($dados_votacao) ||
    !isset($dados_votacao['dados_eleicao']) || !isset($dados_votacao['lista_votantes'])) {
    echo "<p>Erro: Dados da votação ou da eleição não fornecidos para o template.</p>";
    return;
}

extract($dados_votacao['dados_eleicao']); // Título da eleição, ano, etc.
$lista_votantes = $dados_votacao['lista_votantes'];
$total_votantes = count($lista_votantes);

$titulo_eleicao_doc = htmlspecialchars($titulo_eleicao ?? 'Eleição CIPA');
$nome_empresa = htmlspecialchars($nome_empresa ?? '[Nome da Empresa]'); // Supondo que nome_empresa venha em dados_eleicao
$ano_referencia_doc = htmlspecialchars($ano_referencia ?? '[Ano]');
$proximo_ano_doc = (int)$ano_referencia_doc + 1;

$styles = [
    'body' => 'font-family: Arial, sans-serif; font-size: 11px; line-height: 1.5;',
    'h1' => 'font-size: 15px; font-weight: bold; text-align: center; margin-bottom: 5px;',
    'h2' => 'font-size: 13px; font-weight: bold; text-align: center; margin-bottom: 15px;',
    'table' => 'width: 100%; border-collapse: collapse; margin-top: 10px;',
    'th' => 'border: 1px solid #333; padding: 6px; background-color: #f0f0f0; text-align: left; font-weight: bold;',
    'td' => 'border: 1px solid #333; padding: 6px;',
    'footer_info' => 'margin-top: 20px; font-size: 10px; text-align: right;',
    'total' => 'font-weight: bold; font-size: 12px; margin-top: 10px;'
];
?>
<div style="<?php echo $styles['body']; ?>">
    <h1 style="<?php echo $styles['h1']; ?>"><?php echo $nome_empresa; ?></h1>
    <h2 style="<?php echo $styles['h2']; ?>">LISTA DE VOTANTES - <?php echo $titulo_eleicao_doc; ?> (GESTÃO <?php echo $ano_referencia_doc; ?>/<?php echo $proximo_ano_doc; ?>)</h2>

    <?php if (empty($lista_votantes)): ?>
        <p>Nenhum votante registrado para esta eleição até o momento.</p>
    <?php else: ?>
        <table style="<?php echo $styles['table']; ?>">
            <thead>
                <tr>
                    <th style="<?php echo $styles['th']; ?>">#</th>
                    <th style="<?php echo $styles['th']; ?>">Nome Completo do Votante</th>
                    <th style="<?php echo $styles['th']; ?>">CPF</th>
                    <th style="<?php echo $styles['th']; ?>">Data e Hora do Voto</th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 1; foreach ($lista_votantes as $votante): ?>
                    <tr>
                        <td style="<?php echo $styles['td']; ?>"><?php echo $count++; ?></td>
                        <td style="<?php echo $styles['td']; ?>"><?php echo htmlspecialchars($votante['nome_completo']); ?></td>
                        <td style="<?php echo $styles['td']; ?>"><?php echo htmlspecialchars($votante['cpf']); // TODO: Considerar mascarar o CPF ?></td>
                        <td style="<?php echo $styles['td']; ?>"><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($votante['data_hora_voto']))); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p style="<?php echo $styles['total']; ?>">Total de Votantes: <?php echo $total_votantes; ?></p>
    <?php endif; ?>

    <div style="<?php echo $styles['footer_info']; ?>">
        Relatório gerado em: <?php echo date('d/m/Y H:i:s'); ?>
    </div>
</div>

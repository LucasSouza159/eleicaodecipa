<?php
// Template para Ata de Convocação da Eleição CIPA
// Este template espera um array $dados_eleicao com as informações necessárias.

if (!isset($dados_eleicao) || !is_array($dados_eleicao)) {
    echo "<p>Erro: Dados da eleição não fornecidos para o template.</p>";
    return;
}

// Extrair variáveis para facilitar o uso no template
extract($dados_eleicao);

// Formatar datas (exemplo)
$fmt_data_convocacao = isset($data_convocacao) ? date('d/m/Y', strtotime($data_convocacao)) : '[Data não definida]';
$fmt_data_inicio_inscricao = isset($data_inicio_inscricao_candidatos) ? date('d/m/Y \à\s H:i', strtotime($data_inicio_inscricao_candidatos)) : '[Data não definida]';
$fmt_data_fim_inscricao = isset($data_fim_inscricao_candidatos) ? date('d/m/Y \à\s H:i', strtotime($data_fim_inscricao_candidatos)) : '[Data não definida]';
$fmt_data_inicio_votacao = isset($data_inicio_votacao) ? date('d/m/Y \à\s H:i', strtotime($data_inicio_votacao)) : '[Data não definida]';
$fmt_data_fim_votacao = isset($data_fim_votacao) ? date('d/m/Y \à\s H:i', strtotime($data_fim_votacao)) : '[Data não definida]';
$fmt_ano_referencia = isset($ano_referencia) ? $ano_referencia : '[Ano não definido]';
$proximo_ano = (int)$fmt_ano_referencia + 1;


$nome_empresa = htmlspecialchars($nome_empresa ?? '[Nome da Empresa não definido]');
$cnpj_empresa = htmlspecialchars($cnpj_empresa ?? '[CNPJ da Empresa não definido]');
$titulo_eleicao_doc = htmlspecialchars($titulo_eleicao ?? 'Eleição CIPA');
$local_empresa = htmlspecialchars($cidade_empresa ?? '[Cidade da Empresa]') . "/" . htmlspecialchars($estado_empresa ?? '[UF]');


// CSS Inline básico para melhor compatibilidade com bibliotecas PDF como TCPDF/FPDF
// Classes Tailwind não serão processadas aqui diretamente ao gerar PDF com essas bibliotecas.
$styles = [
    'body' => 'font-family: Arial, sans-serif; font-size: 12px; line-height: 1.6;',
    'h1' => 'font-size: 16px; font-weight: bold; text-align: center; margin-bottom: 20px;',
    'h2' => 'font-size: 14px; font-weight: bold; margin-top: 15px; margin-bottom: 10px;',
    'p' => 'margin-bottom: 10px; text-align: justify;',
    'table' => 'width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 15px;',
    'th' => 'border: 1px solid #000; padding: 8px; background-color: #f2f2f2; text-align: left;',
    'td' => 'border: 1px solid #000; padding: 8px;',
    'assinatura' => 'margin-top: 40px; border-top: 1px solid #000; width: 250px; text-align: center; padding-top: 5px;',
    'cidade_data' => 'text-align: right; margin-top: 30px; margin-bottom: 20px;',
];

?>
<div style="<?php echo $styles['body']; ?>">
    <h1 style="<?php echo $styles['h1']; ?>">EDITAL DE CONVOCAÇÃO PARA ELEIÇÃO DA CIPA</h1>
    <h1 style="<?php echo $styles['h1']; ?>">GESTÃO <?php echo $fmt_ano_referencia; ?>/<?php echo $proximo_ano; ?></h1>

    <p style="<?php echo $styles['p']; ?>">
        A empresa <strong><?php echo $nome_empresa; ?></strong>, inscrita no CNPJ sob o nº
        <strong><?php echo $cnpj_empresa; ?></strong>, estabelecida em [Endereço Completo da Empresa/Filial, se aplicável],
        em conformidade com o disposto na Norma Regulamentadora nº 05 (NR-05), aprovada pela Portaria MTb nº 3.214/78,
        CONVOCA todos os seus empregados para a eleição dos membros da Comissão Interna de Prevenção de Acidentes - CIPA,
        para a gestão <?php echo $fmt_ano_referencia; ?>/<?php echo $proximo_ano; ?>.
    </p>

    <h2 style="<?php echo $styles['h2']; ?>">1. OBJETIVO</h2>
    <p style="<?php echo $styles['p']; ?>">
        A presente eleição tem como objetivo escolher os representantes dos empregados na CIPA, que terão como atribuição
        principal a prevenção de acidentes e doenças decorrentes do trabalho, de modo a tornar compatível permanentemente
        o trabalho com a preservação da vida e a promoção da saúde do trabalhador.
    </p>

    <h2 style="<?php echo $styles['h2']; ?>">2. COMISSÃO ELEITORAL</h2>
    <p style="<?php echo $styles['p']; ?>">
        A eleição será organizada e conduzida pela Comissão Eleitoral designada para este fim, conforme [Mencionar como a comissão foi designada, ex: Portaria Interna, Ordem de Serviço, etc.],
        responsável por garantir a lisura e transparência de todo o processo eleitoral.
    </p>
    <!-- TODO: Listar membros da comissão eleitoral se necessário e se os dados estiverem disponíveis em $dados_eleicao -->


    <h2 style="<?php echo $styles['h2']; ?>">3. CRONOGRAMA ELEITORAL</h2>
    <p style="<?php echo $styles['p']; ?>">O processo eleitoral seguirá o seguinte cronograma:</p>
    <table style="<?php echo $styles['table']; ?>">
        <thead>
            <tr>
                <th style="<?php echo $styles['th']; ?>">Evento</th>
                <th style="<?php echo $styles['th']; ?>">Data e Hora</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="<?php echo $styles['td']; ?>">Convocação da Eleição</td>
                <td style="<?php echo $styles['td']; ?>"><?php echo $fmt_data_convocacao; ?></td>
            </tr>
            <tr>
                <td style="<?php echo $styles['td']; ?>">Início das Inscrições de Candidatos</td>
                <td style="<?php echo $styles['td']; ?>"><?php echo $fmt_data_inicio_inscricao; ?></td>
            </tr>
            <tr>
                <td style="<?php echo $styles['td']; ?>">Término das Inscrições de Candidatos</td>
                <td style="<?php echo $styles['td']; ?>"><?php echo $fmt_data_fim_inscricao; ?></td>
            </tr>
            <tr>
                <td style="<?php echo $styles['td']; ?>">Início da Votação</td>
                <td style="<?php echo $styles['td']; ?>"><?php echo $fmt_data_inicio_votacao; ?></td>
            </tr>
            <tr>
                <td style="<?php echo $styles['td']; ?>">Término da Votação</td>
                <td style="<?php echo $styles['td']; ?>"><?php echo $fmt_data_fim_votacao; ?></td>
            </tr>
             <tr>
                <td style="<?php echo $styles['td']; ?>">Apuração dos Votos</td>
                <td style="<?php echo $styles['td']; ?>">Imediatamente após o término da votação, em <?php echo isset($data_fim_votacao) ? date('d/m/Y', strtotime($data_fim_votacao)) : '[Data não definida]'; ?></td>
            </tr>
             <tr>
                <td style="<?php echo $styles['td']; ?>">Posse dos Eleitos</td>
                <td style="<?php echo $styles['td']; ?>"><?php echo isset($data_posse_eleitos) ? date('d/m/Y', strtotime($data_posse_eleitos)) : '[Data não definida]'; ?></td>
            </tr>
        </tbody>
    </table>

    <h2 style="<?php echo $styles['h2']; ?>">4. INSCRIÇÃO DE CANDIDATOS</h2>
    <p style="<?php echo $styles['p']; ?>">
        Os empregados interessados em se candidatar deverão realizar suas inscrições no período de
        <strong><?php echo $fmt_data_inicio_inscricao; ?></strong> até <strong><?php echo $fmt_data_fim_inscricao; ?></strong>.
        As inscrições serão realizadas [Descrever como serão feitas as inscrições, ex: através do sistema online, junto à Comissão Eleitoral, etc.].
        É vedada a candidatura de empregados que não se enquadrem nos critérios estabelecidos pela NR-05.
    </p>

    <h2 style="<?php echo $styles['h2']; ?>">5. VOTAÇÃO</h2>
    <p style="<?php echo $styles['p']; ?>">
        A votação será realizada eletronicamente através do sistema CIPA Fácil Online, no período de
        <strong><?php echo $fmt_data_inicio_votacao; ?></strong> até <strong><?php echo $fmt_data_fim_votacao; ?></strong>.
        O voto é secreto e individual. Todos os empregados da empresa, independentemente de filiação sindical ou tempo de serviço,
        têm direito a voto.
    </p>

    <h2 style="<?php echo $styles['h2']; ?>">6. DISPOSIÇÕES GERAIS</h2>
    <p style="<?php echo $styles['p']; ?>">
        Casos omissos neste edital serão resolvidos pela Comissão Eleitoral, com base na NR-05 e demais legislações pertinentes.
        Este edital será afixado em local de fácil acesso e visualização pelos empregados, bem como disponibilizado nos meios eletrônicos da empresa.
    </p>

    <div style="<?php echo $styles['cidade_data']; ?>">
        <?php echo $local_empresa; ?>, <?php echo $fmt_data_convocacao; ?>.
    </div>

    <div style="margin-top: 60px; text-align: center;">
        <div style="display: inline-block; <?php echo $styles['assinatura']; ?>">
            Presidente da Comissão Eleitoral
        </div>
    </div>
    <div style="margin-top: 60px; text-align: center;">
         <div style="display: inline-block; <?php echo $styles['assinatura']; ?>">
            Representante da Empresa (se aplicável na convocação)
        </div>
    </div>

</div>

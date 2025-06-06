<?php
session_start();
$usuario_logado = false;
$empresa_id_contexto = null;
$usuario_id_gerador = null;

if (isset($_SESSION['empresa_id'])) {
    require_once '../../scripts/auth_empresa.php';
    $empresa_id_contexto = $empresa_id;
    $usuario_logado = true;
} elseif (isset($_SESSION['comissao_id'])) {
    require_once '../../scripts/auth_comissao.php';
    require_once '../../scripts/db_connection.php';
    $stmt_emp_check = $pdo->prepare("SELECT empresa_id FROM eleicoes WHERE id = ?");
    $stmt_emp_check->execute([$comissao_eleicao_id_logado]);
    $emp_assoc_check = $stmt_emp_check->fetch(PDO::FETCH_ASSOC);
    if ($emp_assoc_check) {
        $empresa_id_contexto = $emp_assoc_check['empresa_id'];
    }
    $usuario_logado = true;
}

if (!$usuario_logado) {
    die("Acesso não autorizado.");
}

require_once '../../scripts/db_connection.php';

$eleicao_id_get = filter_input(INPUT_GET, 'eleicao_id', FILTER_VALIDATE_INT);

if (!$eleicao_id_get) {
    die("ID da eleição não fornecido ou inválido.");
}

if (isset($_SESSION['comissao_id']) && $eleicao_id_get != $_SESSION['comissao_eleicao_id']) {
    die("Membro da comissao não tem permissão para esta eleição.");
}

$dados_eleicao_report = null;
$resultados_votacao_report = ['candidatos' => [], 'branco' => 0, 'nulo' => 0, 'total_votantes' => 0];
$candidatos_eleitos_titulares_report = [];
$candidatos_eleitos_suplentes_report = [];
$comissao_eleitoral_report = [];
// $total_empregados_aptos_report = 0; // TODO: Implementar busca de total de empregados aptos

try {
    // Buscar dados da eleição e da empresa
    $stmt_eleicao_data = $pdo->prepare(
        "SELECT e.*, emp.nome_fantasia AS nome_empresa, emp.cnpj AS cnpj_empresa, emp.razao_social,
                f.endereco AS endereco_filial, f.cidade AS cidade_filial, f.estado AS estado_filial
         FROM eleicoes e
         JOIN empresas emp ON e.empresa_id = emp.id
         LEFT JOIN filiais f ON e.filial_id = f.id
         WHERE e.id = :eleicao_id AND e.empresa_id = :empresa_id_contexto"
    );
    $stmt_eleicao_data->bindParam(':eleicao_id', $eleicao_id_get, PDO::PARAM_INT);
    $stmt_eleicao_data->bindParam(':empresa_id_contexto', $empresa_id_contexto, PDO::PARAM_INT);
    $stmt_eleicao_data->execute();
    $dados_eleicao_report = $stmt_eleicao_data->fetch(PDO::FETCH_ASSOC);

    if (!$dados_eleicao_report) {
        die("Eleição não encontrada ou não pertence à sua empresa.");
    }
    // Usar endereço da filial se existir, senão placeholder para endereço da empresa
    $dados_eleicao_report['endereco_empresa'] = $dados_eleicao_report['endereco_filial'] ?? ($dados_eleicao_report['razao_social'] . ' - Matriz (Endereço não especificado)');
    $dados_eleicao_report['cidade_empresa'] = $dados_eleicao_report['cidade_filial'] ?? '[Cidade]';
    $dados_eleicao_report['estado_empresa'] = $dados_eleicao_report['estado_filial'] ?? '[UF]';


    // Validação de status
    $status_permitidos = ['Resultados Publicados', 'Finalizada', 'Em Apuração', 'Votação Encerrada']; // Permitir gerar mesmo que ainda em apuração
    if (!in_array($dados_eleicao_report['status_eleicao'], $status_permitidos)) {
        die("A Ata de Resultado e Posse só pode ser gerada após a apuração ou publicação dos resultados. Status: " . $dados_eleicao_report['status_eleicao']);
    }

    // Coletar resultados da votação
    $stmt_total_votantes = $pdo->prepare("SELECT COUNT(id) AS total FROM votos WHERE eleicao_id = ?");
    $stmt_total_votantes->execute([$eleicao_id_get]);
    $resultados_votacao_report['total_votantes'] = $stmt_total_votantes->fetchColumn();

    $stmt_brancos = $pdo->prepare("SELECT COUNT(id) AS total FROM votos WHERE eleicao_id = ? AND tipo_voto_especial = 'Branco'");
    $stmt_brancos->execute([$eleicao_id_get]);
    $resultados_votacao_report['branco'] = $stmt_brancos->fetchColumn();

    $stmt_nulos = $pdo->prepare("SELECT COUNT(id) AS total FROM votos WHERE eleicao_id = ? AND tipo_voto_especial = 'Nulo'");
    $stmt_nulos->execute([$eleicao_id_get]);
    $resultados_votacao_report['nulo'] = $stmt_nulos->fetchColumn();

    $stmt_votos_cand = $pdo->prepare(
        "SELECT c.id AS candidato_id, c.funcionario_id, c.nome_urna, f.nome_completo, f.cpf, COUNT(v.id) AS total_votos
         FROM candidatos c
         JOIN funcionarios f ON c.funcionario_id = f.id
         LEFT JOIN votos v ON c.id = v.candidato_id AND v.eleicao_id = c.eleicao_id
         WHERE c.eleicao_id = ? AND c.status_candidatura = 'Aprovado' -- Considerar apenas aprovados para resultado
         GROUP BY c.id, c.funcionario_id, c.nome_urna, f.nome_completo, f.cpf
         ORDER BY total_votos DESC, f.nome_completo ASC" // Critério de desempate pode ser mais complexo
    );
    $stmt_votos_cand->execute([$eleicao_id_get]);
    $resultados_votacao_report['candidatos'] = $stmt_votos_cand->fetchAll(PDO::FETCH_ASSOC);

    // Identificar eleitos
    $num_titulares = $dados_eleicao_report['numero_titulares_previstos'] ?? 0;
    $num_suplentes = $dados_eleicao_report['numero_suplentes_previstos'] ?? 0;

    $candidatos_ordenados = $resultados_votacao_report['candidatos']; // Já está ordenado por votos
    $candidatos_eleitos_titulares_report = array_slice($candidatos_ordenados, 0, $num_titulares);
    $candidatos_eleitos_suplentes_report = array_slice($candidatos_ordenados, $num_titulares, $num_suplentes);

    // Buscar membros da comissão eleitoral
    $stmt_comissao = $pdo->prepare("SELECT nome_completo, papel_comissao, cpf FROM comissao WHERE eleicao_id = ? ORDER BY nome_completo");
    $stmt_comissao->execute([$eleicao_id_get]);
    $comissao_eleitoral_report = $stmt_comissao->fetchAll(PDO::FETCH_ASSOC);


} catch (PDOException $e) {
    error_log("Erro ao buscar dados para Ata de Resultado (Eleição ID: $eleicao_id_get): " . $e->getMessage());
    die("Erro ao buscar dados para o relatório. Verifique os logs.");
}

$dados_para_template_final = [
    'dados_eleicao' => $dados_eleicao_report,
    'resultados_votacao' => $resultados_votacao_report,
    'candidatos_eleitos_titulares' => $candidatos_eleitos_titulares_report,
    'candidatos_eleitos_suplentes' => $candidatos_eleitos_suplentes_report,
    'dados_comissao_eleitoral' => $comissao_eleitoral_report,
    // 'total_empregados_aptos' => $total_empregados_aptos_report, // Adicionar se implementado
];

ob_start();
$dados_resultado = $dados_para_template_final;
include 'templates/ata_resultado_posse_template.php';
$html_content = ob_get_clean();

$gerar_pdf_real = false;

if ($gerar_pdf_real) {
    /*
    // Exemplo com TCPDF
    // require_once('path/to/tcpdf/tcpdf.php');
    if (!class_exists('TCPDF')) {
        echo "Biblioteca TCPDF não encontrada.<br><br>";
        echo $html_content; exit;
    }
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    // ... Configurações do PDF ...
    $pdf->AddPage();
    $pdf->writeHTML($html_content, true, false, true, false, '');
    $pdf_filename = "ata_resultado_posse_eleicao_" . $eleicao_id_get . ".pdf";

    // Opcional: Salvar na tabela 'atas'
    // try {
    //     $stmt_ata = $pdo->prepare(
    //         "INSERT INTO atas (eleicao_id, tipo_ata, titulo_documento, nome_arquivo_fisico)
    //          VALUES (:eleicao_id, 'ResultadoEleicao', :titulo, :nome_arquivo)"
    //     );
    //     $stmt_ata->execute([
    //         ':eleicao_id' => $eleicao_id_get,
    //         ':titulo' => 'Ata de Resultado e Posse - ' . $dados_eleicao_report['titulo_eleicao'],
    //         ':nome_arquivo' => $pdf_filename
    //     ]);
    // } catch (PDOException $e) { error_log("Erro ao salvar registro da ata (resultado): " . $e->getMessage()); }

    $pdf->Output($pdf_filename, 'D');
    exit;
    */
    echo "Geração de PDF está habilitada, mas o código da biblioteca PDF precisa ser configurado.";
    exit;
} else {
    echo "<html><head><title>Preview Ata de Resultado e Posse</title>";
    echo "<link href='../../src/styles/output.css' rel='stylesheet'>"; // Para Tailwind no preview HTML
    echo "<style> body { padding: 20px; } </style>";
    echo "</head><body>";
    echo "<div class='container mx-auto bg-white p-8 shadow-lg'>";
    echo "<h1 class='text-xl font-bold mb-4 text-center text-red-600'>PREVIEW - ESTE NÃO É O PDF FINAL</h1>";
    echo "<p class='text-center mb-6 text-red-500'>A biblioteca PDF precisa ser configurada para gerar o documento final.</p>";
    echo $html_content;
    echo "</div>";
    echo "</body></html>";
}
?>

<?php
session_start();
$usuario_logado = false;
$empresa_id_contexto = null; // Empresa à qual a eleição pertence
$usuario_id_gerador = null;

if (isset($_SESSION['empresa_id'])) {
    require_once '../../scripts/auth_empresa.php';
    $empresa_id_contexto = $empresa_id; // Definido em auth_empresa.php
    $usuario_logado = true;
} elseif (isset($_SESSION['comissao_id'])) {
    require_once '../../scripts/auth_comissao.php';
    // $comissao_eleicao_id_logado é definido em auth_comissao.php
    // Precisamos buscar o empresa_id a partir da eleicao_id da comissão
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
    die("Acesso não autorizado. Faça login como empresa ou membro da comissão.");
}

require_once '../../scripts/db_connection.php';

$eleicao_id_get = filter_input(INPUT_GET, 'eleicao_id', FILTER_VALIDATE_INT);

if (!$eleicao_id_get) {
    die("ID da eleição não fornecido ou inválido.");
}

// Se logado como comissão, o eleicao_id DEVE ser o da sessão.
if (isset($_SESSION['comissao_id']) && $eleicao_id_get != $_SESSION['comissao_eleicao_id']) {
    die("Membro da comissão não tem permissão para acessar dados desta eleição.");
}


$dados_eleicao_report = null;
$lista_votantes_report = [];

try {
    // Buscar dados da eleição e da empresa
    $stmt = $pdo->prepare(
        "SELECT e.id AS eleicao_id, e.titulo_eleicao, e.ano_referencia, e.status_eleicao,
                emp.nome_fantasia AS nome_empresa, emp.cnpj AS cnpj_empresa
         FROM eleicoes e
         JOIN empresas emp ON e.empresa_id = emp.id
         WHERE e.id = :eleicao_id AND e.empresa_id = :empresa_id_contexto"
    );
    $stmt->bindParam(':eleicao_id', $eleicao_id_get, PDO::PARAM_INT);
    $stmt->bindParam(':empresa_id_contexto', $empresa_id_contexto, PDO::PARAM_INT);
    $stmt->execute();
    $dados_eleicao_report = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dados_eleicao_report) {
        die("Eleição não encontrada ou não pertence à sua empresa.");
    }

    // Validar se a eleição já foi encerrada ou está em um status que permite ver a lista de votantes
    $status_permitidos = ['Votação Encerrada', 'Em Apuração', 'Resultados Publicados', 'Finalizada'];
    if (!in_array($dados_eleicao_report['status_eleicao'], $status_permitidos)) {
        die("A lista de votantes só pode ser gerada após o encerramento da votação. Status atual: " . $dados_eleicao_report['status_eleicao']);
    }

    // Buscar lista de votantes
    $stmt_votantes = $pdo->prepare(
        "SELECT f.nome_completo, f.cpf, v.data_hora_voto
         FROM votos v
         JOIN funcionarios f ON v.funcionario_id = f.id
         WHERE v.eleicao_id = :eleicao_id
         ORDER BY v.data_hora_voto ASC"
         // Poderia ordenar por f.nome_completo também
    );
    $stmt_votantes->bindParam(':eleicao_id', $eleicao_id_get, PDO::PARAM_INT);
    $stmt_votantes->execute();
    $lista_votantes_report = $stmt_votantes->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erro ao buscar dados para Lista de Votantes (Eleição ID: $eleicao_id_get): " . $e->getMessage());
    die("Erro ao buscar dados para o relatório. Verifique os logs.");
}

$dados_votacao_template = [
    'dados_eleicao' => $dados_eleicao_report,
    'lista_votantes' => $lista_votantes_report
];

// Capturar o HTML do template
ob_start();
$dados_votacao = $dados_votacao_template; // Passa os dados para o template
include 'templates/lista_votantes_template.php';
$html_content = ob_get_clean();

$gerar_pdf_real = false; // Mude para true para tentar gerar PDF

if ($gerar_pdf_real) {
    /*
    // Exemplo com TCPDF
    // require_once('path/to/tcpdf/tcpdf.php'); // Ajuste o caminho
    if (!class_exists('TCPDF')) {
        echo "Biblioteca TCPDF não encontrada.<br><br>";
        echo $html_content; exit;
    }
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor($dados_eleicao_report['nome_empresa'] ?? 'Sistema CIPA Fácil');
    $pdf->SetTitle('Lista de Votantes - ' . $dados_eleicao_report['titulo_eleicao']);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(true); // Pode querer um rodapé com número de página
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(TRUE, 15);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 9);
    $pdf->writeHTML($html_content, true, false, true, false, '');
    $pdf_filename = "lista_votantes_eleicao_" . $eleicao_id_get . ".pdf";

    // Opcional: Salvar na tabela 'atas'
    // try {
    //     $stmt_ata = $pdo->prepare(
    //         "INSERT INTO atas (eleicao_id, tipo_ata, titulo_documento, nome_arquivo_fisico)
    //          VALUES (:eleicao_id, 'ListaVotantes', :titulo, :nome_arquivo)"
    //     );
    //     $stmt_ata->execute([
    //         ':eleicao_id' => $eleicao_id_get,
    //         ':titulo' => 'Lista de Votantes - ' . $dados_eleicao_report['titulo_eleicao'],
    //         ':nome_arquivo' => $pdf_filename
    //     ]);
    // } catch (PDOException $e) { error_log("Erro ao salvar registro da ata (lista votantes): " . $e->getMessage()); }

    $pdf->Output($pdf_filename, 'D'); // 'D' para forçar download
    exit;
    */
    echo "Geração de PDF está habilitada, mas o código da biblioteca PDF precisa ser configurado.";
    exit;
} else {
    // Preview HTML
    echo "<html><head><title>Preview Lista de Votantes</title>";
    echo "<link href='../../src/styles/output.css' rel='stylesheet'>";
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

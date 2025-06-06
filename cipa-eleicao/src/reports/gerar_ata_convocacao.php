<?php
// Este script pode ser acessado tanto pela empresa quanto pela comissão,
// então vamos verificar se um dos dois está logado.
session_start();
$usuario_logado = false;
$empresa_id_contexto = null;
$usuario_id_gerador = null; // Futuramente, para registrar quem gerou

if (isset($_SESSION['empresa_id'])) { // Empresa logada
    require_once '../../scripts/auth_empresa.php'; // Define $empresa_id
    $empresa_id_contexto = $empresa_id;
    // $usuario_id_gerador = $_SESSION['empresa_usuario_id']; // Se houver um ID específico do usuário da empresa
    $usuario_logado = true;
} elseif (isset($_SESSION['comissao_id'])) { // Membro da comissão logado
    require_once '../../scripts/auth_comissao.php'; // Define $comissao_eleicao_id_logado
    // Precisamos buscar o empresa_id a partir da eleicao_id da comissão
    require_once '../../scripts/db_connection.php'; // Conexão já deve estar inclusa por auth_comissao, mas para garantir
    $stmt_emp = $pdo->prepare("SELECT empresa_id FROM eleicoes WHERE id = ?");
    $stmt_emp->execute([$comissao_eleicao_id_logado]);
    $emp_assoc = $stmt_emp->fetch(PDO::FETCH_ASSOC);
    if ($emp_assoc) {
        $empresa_id_contexto = $emp_assoc['empresa_id'];
    }
    // $usuario_id_gerador = $_SESSION['comissao_id']; // ID do membro da comissão
    $usuario_logado = true;
}

if (!$usuario_logado) {
    die("Acesso não autorizado. Faça login como empresa ou membro da comissão.");
}

require_once '../../scripts/db_connection.php'; // Se não incluído ainda

$eleicao_id = filter_input(INPUT_GET, 'eleicao_id', FILTER_VALIDATE_INT);

if (!$eleicao_id) {
    die("ID da eleição não fornecido ou inválido.");
}

$dados_eleicao_completo = null;

try {
    $stmt = $pdo->prepare(
        "SELECT e.*, emp.nome_fantasia AS nome_empresa, emp.cnpj AS cnpj_empresa,
                emp.email AS email_empresa, emp.razao_social,
                f.nome_fantasia AS nome_filial, f.cidade AS cidade_filial, f.estado AS estado_filial,
                (SELECT GROUP_CONCAT(CONCAT(c.nome_completo, ' (', c.papel_comissao, ')') SEPARATOR '; ')
                 FROM comissao c WHERE c.eleicao_id = e.id) AS membros_comissao_eleitoral
         FROM eleicoes e
         JOIN empresas emp ON e.empresa_id = emp.id
         LEFT JOIN filiais f ON e.filial_id = f.id
         WHERE e.id = :eleicao_id AND e.empresa_id = :empresa_id_contexto"
    );
    $stmt->bindParam(':eleicao_id', $eleicao_id, PDO::PARAM_INT);
    $stmt->bindParam(':empresa_id_contexto', $empresa_id_contexto, PDO::PARAM_INT);
    $stmt->execute();
    $dados_eleicao_completo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dados_eleicao_completo) {
        die("Eleição não encontrada, não pertence à sua empresa ou dados insuficientes.");
    }

    // Adicionar cidade/estado da empresa principal se não for eleição de filial
    if (empty($dados_eleicao_completo['cidade_filial'])) {
        // Buscar cidade/estado da empresa principal (simplificado, idealmente teria campos de endereço na tabela empresas)
        // Por agora, vamos deixar um placeholder se não for de filial
        $dados_eleicao_completo['cidade_empresa'] = $dados_eleicao_completo['cidade_filial'] ?? '[Cidade da Empresa]';
        $dados_eleicao_completo['estado_empresa'] = $dados_eleicao_completo['estado_filial'] ?? '[UF]';
    } else {
        $dados_eleicao_completo['cidade_empresa'] = $dados_eleicao_completo['cidade_filial'];
        $dados_eleicao_completo['estado_empresa'] = $dados_eleicao_completo['estado_filial'];
    }


} catch (PDOException $e) {
    error_log("Erro ao buscar dados da eleição para ata: " . $e->getMessage());
    die("Erro ao buscar dados da eleição. Verifique os logs.");
}


// Capturar o HTML do template
ob_start();
$dados_eleicao = $dados_eleicao_completo; // Passa os dados para o template
include 'templates/ata_convocacao_template.php';
$html_content = ob_get_clean();

// --- Ponto de integração para a biblioteca PDF ---
$gerar_pdf_real = false; // Mude para true para tentar gerar PDF se a lib estiver configurada

if ($gerar_pdf_real) {
    /*
    // Exemplo com TCPDF (requer instalação e configuração do autoload)
    // Verifique se o autoload do Composer está incluído, ou inclua o tcpdf.php manualmente
    // require_once __DIR__ . '/../../vendor/autoload.php'; // Se usando Composer e TCPDF está no vendor
    // Ou:
    // require_once('path/to/tcpdf/tcpdf.php'); // Ajuste este caminho

    if (!class_exists('TCPDF')) {
        echo "Biblioteca TCPDF não encontrada. Por favor, configure-a para gerar o PDF.<br><br>";
        echo $html_content; // Mostra o HTML como fallback
        exit;
    }

    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor($dados_eleicao['nome_empresa'] ?? 'Sistema CIPA Fácil');
    $pdf->SetTitle('Ata de Convocação - ' . $dados_eleicao['titulo_eleicao']);
    $pdf->SetSubject('Ata de Convocação para Eleição da CIPA');

    // Remover header e footer padrão do TCPDF, se desejar
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    $pdf->SetMargins(15, 15, 15); // Esquerda, Topo, Direita
    $pdf->SetAutoPageBreak(TRUE, 15); // Margem inferior

    $pdf->AddPage();

    $pdf->SetFont('helvetica', '', 10); // TCPDF usa fontes core se não especificadas de outra forma

    // writeHTMLCell é mais flexível que writeHTML para controlar o fluxo
    // $pdf->writeHTMLCell(0, 0, '', '', $html_content, 0, 1, 0, true, '', true);
    // Ou simplesmente:
    $pdf->writeHTML($html_content, true, false, true, false, '');


    $pdf_filename = "ata_convocacao_eleicao_" . $eleicao_id . ".pdf";

    // Tentar salvar no servidor (opcional) e registrar na tabela 'atas'
    $caminho_salvar_pdf = __DIR__ . '/../arquivos_gerados/'; // Exemplo
    if (!is_dir($caminho_salvar_pdf)) {
        mkdir($caminho_salvar_pdf, 0777, true);
    }
    // $pdf->Output($caminho_salvar_pdf . $pdf_filename, 'F'); // 'F' para salvar no servidor

    // Forçar download
    $pdf->Output($pdf_filename, 'D');


    // Inserir registro na tabela 'atas' (exemplo)
    try {
        $stmt_ata = $pdo->prepare(
            "INSERT INTO atas (eleicao_id, tipo_ata, titulo_documento, gerada_por_usuario_id, nome_arquivo_fisico, conteudo_ata)
             VALUES (:eleicao_id, 'Convocacao', :titulo, :usuario_id, :nome_arquivo, :conteudo_html)"
        );
        $stmt_ata->execute([
            ':eleicao_id' => $eleicao_id,
            ':titulo' => 'Ata de Convocação - ' . $dados_eleicao['titulo_eleicao'],
            ':usuario_id' => $usuario_id_gerador, // Implementar captura do ID do usuário logado
            ':nome_arquivo' => $pdf_filename, // Se salvou no servidor
            ':conteudo_html' => $html_content // Opcional, se quiser guardar o HTML
        ]);
    } catch (PDOException $e) {
        error_log("Erro ao salvar registro da ata: " . $e->getMessage());
        // Não interromper o download do PDF por causa disso
    }
    exit;
    */
    echo "Geração de PDF está habilitada, mas o código da biblioteca PDF precisa ser configurado.";
    exit;
} else {
    // Se não for para gerar PDF real, apenas exibe o HTML
    echo "<html><head><title>Preview Ata de Convocação</title>";
    echo "<link href='../../src/styles/output.css' rel='stylesheet'>"; // Para aplicar Tailwind no preview HTML
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

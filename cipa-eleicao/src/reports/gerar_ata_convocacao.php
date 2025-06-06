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
    $stmt_emp = $pdo->prepare("SELECT empresa_id FROM eleicoes WHERE id = ?");
    $stmt_emp->execute([$comissao_eleicao_id_logado]);
    $emp_assoc = $stmt_emp->fetch(PDO::FETCH_ASSOC);
    if ($emp_assoc) {
        $empresa_id_contexto = $emp_assoc['empresa_id'];
    }
    $usuario_logado = true;
}

if (!$usuario_logado) {
    die("Acesso não autorizado. Faça login como empresa ou membro da comissão.");
}

require_once '../../scripts/db_connection.php';

$eleicao_id = filter_input(INPUT_GET, 'eleicao_id', FILTER_VALIDATE_INT);

if (!$eleicao_id) {
    die("ID da eleição não fornecido ou inválido.");
}

// Se logado como comissão, o eleicao_id DEVE ser o da sessão.
if (isset($_SESSION['comissao_id']) && $eleicao_id != $_SESSION['comissao_eleicao_id']) {
    die("Membro da comissão não tem permissão para acessar dados desta eleição.");
}

$dados_eleicao_completo = null;
$titulo_relatorio = "Preview - Ata de Convocação"; // Título padrão

try {
    $stmt = $pdo->prepare(
        "SELECT e.*, emp.nome_fantasia AS nome_empresa, emp.cnpj AS cnpj_empresa,
                emp.email AS email_empresa, emp.razao_social,
                f.nome_fantasia AS nome_filial, f.cidade AS cidade_filial, f.estado AS estado_filial, f.endereco AS endereco_filial_completo,
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
    $titulo_relatorio = "Preview - Ata de Convocação: " . htmlspecialchars($dados_eleicao_completo['titulo_eleicao']);

    // Melhorar lógica de endereço e local
    $dados_eleicao_completo['endereco_completo_local'] = $dados_eleicao_completo['endereco_filial_completo'] ?? ($dados_eleicao_completo['razao_social'] . ' - Endereço da Matriz não especificado');
    $dados_eleicao_completo['cidade_local'] = $dados_eleicao_completo['cidade_filial'] ?? '[Cidade não especificada]';
    $dados_eleicao_completo['estado_local'] = $dados_eleicao_completo['estado_filial'] ?? '[UF]';


} catch (PDOException $e) {
    error_log("Erro ao buscar dados da eleição para ata: " . $e->getMessage());
    die("Erro ao buscar dados da eleição. Verifique os logs.");
}

// Início do HTML da página de preview
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_relatorio; ?></title>
    <link href="../../src/styles/output.css" rel="stylesheet">
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .print-document-container { box-shadow: none !important; margin: 0 !important; padding: 0 !important; border: none !important; }
        }
    </style>
</head>
<body class="bg-gray-100 p-4 sm:p-8 font-sans">
    <div class="no-print mb-6 text-center space-x-2">
        <button onclick="window.print()" class="bg-roxo-principal hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors">
            Imprimir / Salvar como PDF
        </button>
        <a href="javascript:window.close()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors">
            Fechar Preview
        </a>
    </div>
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 shadow-2xl rounded-lg print-document-container">
        <?php
        // Capturar o HTML do template
        ob_start();
        $dados_eleicao = $dados_eleicao_completo; // Passa os dados para o template
        include 'templates/ata_convocacao_template.php';
        $html_content = ob_get_clean();
        echo $html_content; // Exibe o conteúdo do template

        // --- Ponto de integração para a biblioteca PDF ---
        $gerar_pdf_real = false; // Mude para true para tentar gerar PDF se a lib estiver configurada

        if ($gerar_pdf_real) {
            // Código da biblioteca PDF (comentado como antes)
            echo "<div class='no-print mt-8 text-center text-red-500'>Geração de PDF real precisa ser configurada.</div>";
        }

        // Opcional: Registrar na tabela 'atas' se ainda não foi feito ao gerar PDF
        if (!$gerar_pdf_real && isset($pdo)) { // Exemplo de registro mesmo para preview HTML
            try {
                // Verificar se já existe um registro para não duplicar em simples refresh
                $stmt_check_ata = $pdo->prepare("SELECT id FROM atas WHERE eleicao_id = :eleicao_id AND tipo_ata = 'Convocacao'");
                $stmt_check_ata->execute([':eleicao_id' => $eleicao_id]);
                if (!$stmt_check_ata->fetch()) {
                    $stmt_ata = $pdo->prepare(
                        "INSERT INTO atas (eleicao_id, tipo_ata, titulo_documento, conteudo_ata, gerada_por_usuario_id)
                         VALUES (:eleicao_id, 'Convocacao', :titulo, :conteudo_html, :usuario_id)"
                    );
                    $stmt_ata->execute([
                        ':eleicao_id' => $eleicao_id,
                        ':titulo' => 'Ata de Convocação - ' . ($dados_eleicao_completo['titulo_eleicao'] ?? 'Eleição ' . $eleicao_id),
                        ':conteudo_html' => $html_content,
                        ':usuario_id' => null // $usuario_id_gerador - Implementar captura do ID do usuário logado
                    ]);
                }
            } catch (PDOException $e) {
                error_log("Erro ao salvar registro da ata (preview HTML): " . $e->getMessage());
            }
        }
        ?>
    </div> <!-- Fechamento do .max-w-4xl -->
    <div class="no-print mt-6 text-center">
         <button onclick="window.print()" class="bg-roxo-principal hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors">
            Imprimir / Salvar como PDF
        </button>
    </div>
</body>
</html>

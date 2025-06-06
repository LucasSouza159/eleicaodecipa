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
    die("Membro da comissão não tem permissão para acessar dados desta eleição.");
}

$dados_eleicao_report = null;
$lista_votantes_report = [];
$titulo_relatorio = "Preview - Lista de Votantes";

try {
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
    $titulo_relatorio = "Preview - Lista de Votantes: " . htmlspecialchars($dados_eleicao_report['titulo_eleicao']);

    $status_permitidos = ['Votação Encerrada', 'Em Apuração', 'Resultados Publicados', 'Finalizada'];
    if (!in_array($dados_eleicao_report['status_eleicao'], $status_permitidos)) {
        die("A lista de votantes só pode ser gerada após o encerramento da votação. Status: " . $dados_eleicao_report['status_eleicao']);
    }

    $stmt_votantes = $pdo->prepare(
        "SELECT f.nome_completo, f.cpf, v.data_hora_voto
         FROM votos v
         JOIN funcionarios f ON v.funcionario_id = f.id
         WHERE v.eleicao_id = :eleicao_id
         ORDER BY v.data_hora_voto ASC"
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
            .print-document-container { box-shadow: none !important; margin: 0 !important; padding: 0 !important; border: none !important;}
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
        ob_start();
        $dados_votacao = $dados_votacao_template;
        include 'templates/lista_votantes_template.php';
        $html_content = ob_get_clean();
        echo $html_content;

        $gerar_pdf_real = false;
        if ($gerar_pdf_real) {
            echo "<div class='no-print mt-8 text-center text-red-500'>Geração de PDF real precisa ser configurada.</div>";
        }
        // Opcional: Registro na tabela atas (exemplo)
        if (!$gerar_pdf_real && isset($pdo)) {
            try {
                $stmt_check_ata = $pdo->prepare("SELECT id FROM atas WHERE eleicao_id = :eleicao_id AND tipo_ata = 'ListaVotantes'");
                $stmt_check_ata->execute([':eleicao_id' => $eleicao_id_get]);
                if (!$stmt_check_ata->fetch()) {
                     $stmt_ata = $pdo->prepare(
                        "INSERT INTO atas (eleicao_id, tipo_ata, titulo_documento, conteudo_ata)
                         VALUES (:eleicao_id, 'ListaVotantes', :titulo, :conteudo_html)"
                    );
                    $stmt_ata->execute([
                        ':eleicao_id' => $eleicao_id_get,
                        ':titulo' => 'Lista de Votantes - ' . ($dados_eleicao_report['titulo_eleicao'] ?? 'Eleição ' . $eleicao_id_get),
                        ':conteudo_html' => $html_content
                    ]);
                }
            } catch (PDOException $e) {
                error_log("Erro ao salvar registro da ata (Lista Votantes preview HTML): " . $e->getMessage());
            }
        }
        ?>
    </div>
     <div class="no-print mt-6 text-center">
         <button onclick="window.print()" class="bg-roxo-principal hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors">
            Imprimir / Salvar como PDF
        </button>
    </div>
</body>
</html>

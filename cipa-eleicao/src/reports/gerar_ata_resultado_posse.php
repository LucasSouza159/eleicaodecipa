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
$resultados_votacao_report = ['candidatos' => [], 'branco' => 0, 'nulo' => 0, 'total_votantes' => 0];
$candidatos_eleitos_titulares_report = [];
$candidatos_eleitos_suplentes_report = [];
$comissao_eleitoral_report = [];
$titulo_relatorio = "Preview - Ata de Resultado e Posse";

try {
    $stmt_eleicao_data = $pdo->prepare(
        "SELECT e.*, emp.nome_fantasia AS nome_empresa, emp.cnpj AS cnpj_empresa, emp.razao_social,
                f.endereco AS endereco_filial_completo, f.cidade AS cidade_filial, f.estado AS estado_filial
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
    $titulo_relatorio = "Preview - Ata de Resultado e Posse: " . htmlspecialchars($dados_eleicao_report['titulo_eleicao']);

    $dados_eleicao_report['endereco_empresa_completo'] = $dados_eleicao_report['endereco_filial_completo'] ?? ($dados_eleicao_report['razao_social'] . ' - Endereço da Matriz não especificado');
    $dados_eleicao_report['cidade_local'] = $dados_eleicao_report['cidade_filial'] ?? '[Cidade não especificada]';
    $dados_eleicao_report['estado_local'] = $dados_eleicao_report['estado_filial'] ?? '[UF]';

    $status_permitidos = ['Resultados Publicados', 'Finalizada', 'Em Apuração', 'Votação Encerrada'];
    if (!in_array($dados_eleicao_report['status_eleicao'], $status_permitidos)) {
        die("A Ata de Resultado e Posse só pode ser gerada após a apuração ou publicação dos resultados. Status atual: " . $dados_eleicao_report['status_eleicao']);
    }

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
         WHERE c.eleicao_id = ? AND c.status_candidatura = 'Aprovado'
         GROUP BY c.id, c.funcionario_id, c.nome_urna, f.nome_completo, f.cpf
         ORDER BY total_votos DESC, f.nome_completo ASC"
    );
    $stmt_votos_cand->execute([$eleicao_id_get]);
    $resultados_votacao_report['candidatos'] = $stmt_votos_cand->fetchAll(PDO::FETCH_ASSOC);

    $num_titulares = $dados_eleicao_report['numero_titulares_previstos'] ?? 0;
    $num_suplentes = $dados_eleicao_report['numero_suplentes_previstos'] ?? 0;

    $candidatos_ordenados = $resultados_votacao_report['candidatos'];
    $candidatos_eleitos_titulares_report = array_slice($candidatos_ordenados, 0, $num_titulares);
    $candidatos_eleitos_suplentes_report = array_slice($candidatos_ordenados, $num_titulares, $num_suplentes);

    $stmt_comissao = $pdo->prepare("SELECT nome_completo, papel_comissao, cpf FROM comissao WHERE eleicao_id = ? ORDER BY FIELD(papel_comissao, 'Presidente', 'Secretário', 'Membro'), nome_completo");
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
        $dados_resultado = $dados_para_template_final;
        include 'templates/ata_resultado_posse_template.php';
        $html_content = ob_get_clean();
        echo $html_content;

        $gerar_pdf_real = false;
        if ($gerar_pdf_real) {
            echo "<div class='no-print mt-8 text-center text-red-500'>Geração de PDF real precisa ser configurada.</div>";
        }
         // Opcional: Registro na tabela atas (exemplo)
        if (!$gerar_pdf_real && isset($pdo)) {
            try {
                 $stmt_check_ata = $pdo->prepare("SELECT id FROM atas WHERE eleicao_id = :eleicao_id AND tipo_ata = 'ResultadoEleicao'");
                $stmt_check_ata->execute([':eleicao_id' => $eleicao_id_get]);
                if (!$stmt_check_ata->fetch()) {
                    $stmt_ata = $pdo->prepare(
                        "INSERT INTO atas (eleicao_id, tipo_ata, titulo_documento, conteudo_ata)
                         VALUES (:eleicao_id, 'ResultadoEleicao', :titulo, :conteudo_html)"
                    );
                    $stmt_ata->execute([
                        ':eleicao_id' => $eleicao_id_get,
                        ':titulo' => 'Ata de Resultado e Posse - ' . ($dados_eleicao_report['titulo_eleicao'] ?? 'Eleição ' . $eleicao_id_get),
                        ':conteudo_html' => $html_content
                    ]);
                }
            } catch (PDOException $e) {
                error_log("Erro ao salvar registro da ata (Resultado e Posse preview HTML): " . $e->getMessage());
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

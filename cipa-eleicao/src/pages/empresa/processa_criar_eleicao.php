<?php
require_once '../../scripts/auth_empresa.php'; // Garante autenticação e inicia sessão
require_once '../../scripts/db_connection.php'; // Conexão com o banco

$empresa_id = $_SESSION['empresa_id'];
$erros = [];
$dados_formulario = $_POST; // Para repopular o formulário em caso de erro

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizar e validar os dados recebidos
    $titulo_eleicao = trim($dados_formulario['titulo_eleicao'] ?? '');
    $descricao = trim($dados_formulario['descricao'] ?? null);
    $ano_referencia = filter_var($dados_formulario['ano_referencia'] ?? '', FILTER_VALIDATE_INT, ["options" => ["min_range" => 2000, "max_range" => 2100]]);
    $numero_titulares_previstos = filter_var($dados_formulario['numero_titulares_previstos'] ?? 0, FILTER_VALIDATE_INT, ["options" => ["min_range" => 0]]);
    $numero_suplentes_previstos = filter_var($dados_formulario['numero_suplentes_previstos'] ?? 0, FILTER_VALIDATE_INT, ["options" => ["min_range" => 0]]);
    $filial_id = trim($dados_formulario['filial_id'] ?? '');
    $filial_id = empty($filial_id) ? null : filter_var($filial_id, FILTER_VALIDATE_INT);

    $data_convocacao = $dados_formulario['data_convocacao'] ?? '';
    $data_inicio_inscricao = $dados_formulario['data_inicio_inscricao_candidatos'] ?? '';
    $data_fim_inscricao = $dados_formulario['data_fim_inscricao_candidatos'] ?? '';
    $data_inicio_votacao = $dados_formulario['data_inicio_votacao'] ?? '';
    $data_fim_votacao = $dados_formulario['data_fim_votacao'] ?? '';
    $data_apuracao = $dados_formulario['data_apuracao'] ?? '';
    $data_posse_eleitos = $dados_formulario['data_posse_eleitos'] ?? '';
    $observacoes_gerais = trim($dados_formulario['observacoes_gerais'] ?? null);

    // Validações
    if (empty($titulo_eleicao)) $erros[] = "Título da Eleição é obrigatório.";
    if ($ano_referencia === false) $erros[] = "Ano de Referência inválido (deve ser entre 2000 e 2100).";
    if ($numero_titulares_previstos === false) $erros[] = "Número de titulares previstos inválido (deve ser um número igual ou maior que zero).";
    if ($numero_suplentes_previstos === false) $erros[] = "Número de suplentes previstos inválido (deve ser um número igual ou maior que zero).";
    if ($filial_id === false && !is_null($filial_id) && $dados_formulario['filial_id'] !== '') $erros[] = "ID da Filial inválido."; // Se fornecido, deve ser int

    // Validar datas obrigatórias
    $datas_obrigatorias = [
        'Data de Convocação' => $data_convocacao,
        'Início das Inscrições' => $data_inicio_inscricao,
        'Fim das Inscrições' => $data_fim_inscricao,
        'Início da Votação' => $data_inicio_votacao,
        'Fim da Votação' => $data_fim_votacao,
        'Data da Apuração' => $data_apuracao,
        'Data da Posse' => $data_posse_eleitos,
    ];
    foreach ($datas_obrigatorias as $nome_campo => $valor_data) {
        if (empty($valor_data)) {
            $erros[] = "$nome_campo é obrigatória.";
        }
    }

    // Validação da ordem cronológica das datas (simplificada)
    // Idealmente, converter para timestamp para comparações mais robustas
    if (empty($erros)) {
        if (strtotime($data_inicio_inscricao) <= strtotime($data_convocacao)) $erros[] = "Início das inscrições deve ser após a data de convocação.";
        if (strtotime($data_fim_inscricao) <= strtotime($data_inicio_inscricao)) $erros[] = "Fim das inscrições deve ser após o início das inscrições.";
        if (strtotime($data_inicio_votacao) <= strtotime($data_fim_inscricao)) $erros[] = "Início da votação deve ser após o fim das inscrições.";
        if (strtotime($data_fim_votacao) <= strtotime($data_inicio_votacao)) $erros[] = "Fim da votação deve ser após o início da votação.";
        if (strtotime($data_apuracao) < strtotime($data_fim_votacao)) $erros[] = "Data da apuração não pode ser anterior ao fim da votação."; // Pode ser no mesmo dia
        if (strtotime($data_posse_eleitos) <= strtotime($data_apuracao)) $erros[] = "Data da posse deve ser após a data da apuração.";
    }

    // TODO: Validar se filial_id pertence à empresa_id (consulta no banco)
    // if ($filial_id !== null) {
    //     $stmt = $pdo->prepare("SELECT COUNT(*) FROM filiais WHERE id = ? AND empresa_id = ?");
    //     $stmt->execute([$filial_id, $empresa_id]);
    //     if ($stmt->fetchColumn() == 0) {
    //         $erros[] = "Filial selecionada não pertence à sua empresa ou não existe.";
    //     }
    // }

    if (empty($erros)) {
        try {
            $sql = "INSERT INTO eleicoes (empresa_id, filial_id, titulo_eleicao, descricao, ano_referencia,
                                       numero_titulares_previstos, numero_suplentes_previstos,
                                       data_convocacao, data_inicio_inscricao_candidatos, data_fim_inscricao_candidatos,
                                       data_inicio_votacao, data_fim_votacao, data_apuracao, data_posse_eleitos,
                                       observacoes_gerais, status_eleicao)
                    VALUES (:empresa_id, :filial_id, :titulo_eleicao, :descricao, :ano_referencia,
                            :numero_titulares, :numero_suplentes,
                            :data_convocacao, :data_inicio_inscricao, :data_fim_inscricao,
                            :data_inicio_votacao, :data_fim_votacao, :data_apuracao, :data_posse_eleitos,
                            :observacoes_gerais, 'Planejada')";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
            $stmt->bindParam(':filial_id', $filial_id, PDO::PARAM_INT_OR_NULL);
            $stmt->bindParam(':titulo_eleicao', $titulo_eleicao);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':ano_referencia', $ano_referencia);
            $stmt->bindParam(':numero_titulares', $numero_titulares_previstos, PDO::PARAM_INT);
            $stmt->bindParam(':numero_suplentes', $numero_suplentes_previstos, PDO::PARAM_INT);
            $stmt->bindParam(':data_convocacao', $data_convocacao);
            $stmt->bindParam(':data_inicio_inscricao', $data_inicio_inscricao);
            $stmt->bindParam(':data_fim_inscricao', $data_fim_inscricao);
            $stmt->bindParam(':data_inicio_votacao', $data_inicio_votacao);
            $stmt->bindParam(':data_fim_votacao', $data_fim_votacao);
            $stmt->bindParam(':data_apuracao', $data_apuracao);
            $stmt->bindParam(':data_posse_eleitos', $data_posse_eleitos);
            $stmt->bindParam(':observacoes_gerais', $observacoes_gerais);

            $stmt->execute();

            $_SESSION['mensagem_sucesso'] = "Eleição criada com sucesso!";
            header("Location: gerenciar_eleicoes.php");
            exit();

        } catch (PDOException $e) {
            error_log("Erro ao criar eleição: " . $e->getMessage());
            $_SESSION['erros_criar_eleicao'] = ["Erro ao salvar a eleição no banco de dados. Detalhe: " . $e->getMessage()]; // Mostrar erro do DB para debug
            $_SESSION['dados_formulario_eleicao'] = $dados_formulario;
            header("Location: criar_eleicao.php");
            exit();
        }
    } else {
        // Armazenar erros e dados do formulário na sessão e redirecionar
        $_SESSION['erros_criar_eleicao'] = $erros;
        $_SESSION['dados_formulario_eleicao'] = $dados_formulario;
        header("Location: criar_eleicao.php");
        exit();
    }

} else {
    // Redirecionar se não for POST
    header("Location: criar_eleicao.php");
    exit();
}
?>

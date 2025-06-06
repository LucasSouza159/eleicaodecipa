<?php
require_once '../../scripts/auth_comissao.php'; // Garante autenticação e define $comissao_eleicao_id_logado
require_once '../../scripts/db_connection.php';

$eleicao_id = $comissao_eleicao_id_logado;
$erros = [];
$dados_formulario = $_POST;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $funcionario_id = filter_input(INPUT_POST, 'funcionario_id', FILTER_VALIDATE_INT);
    $numero_candidato_raw = trim(filter_input(INPUT_POST, 'numero_candidato'));
    $numero_candidato = ($numero_candidato_raw === '') ? null : filter_var($numero_candidato_raw, FILTER_VALIDATE_INT);
    $nome_urna = trim(filter_input(INPUT_POST, 'nome_urna'));
    $nome_urna = empty($nome_urna) ? null : $nome_urna;
    $plataforma_propostas = trim(filter_input(INPUT_POST, 'plataforma_propostas'));
    $plataforma_propostas = empty($plataforma_propostas) ? null : $plataforma_propostas;

    // Validação do ID do funcionário
    if ($funcionario_id === false || $funcionario_id <= 0) {
        $erros[] = "ID do Funcionário inválido.";
    } else {
        // Validar se o funcionário existe, pertence à empresa da eleição e está apto
        try {
            $stmt_check_func = $pdo->prepare(
                "SELECT f.id, f.empresa_id
                 FROM funcionarios f
                 JOIN eleicoes e ON f.empresa_id = e.empresa_id
                 WHERE f.id = ? AND e.id = ? AND f.status_funcionario = 'Ativo' AND f.permite_votar = TRUE"
            );
            $stmt_check_func->execute([$funcionario_id, $eleicao_id]);
            $funcionario_valido = $stmt_check_func->fetch(PDO::FETCH_ASSOC);

            if (!$funcionario_valido) {
                $erros[] = "Funcionário não encontrado, não pertence à empresa desta eleição, não está ativo ou não pode votar.";
            } else {
                // Verificar se o funcionário já é candidato nesta eleição
                $stmt_check_candidato_existente = $pdo->prepare("SELECT id FROM candidatos WHERE eleicao_id = ? AND funcionario_id = ?");
                $stmt_check_candidato_existente->execute([$eleicao_id, $funcionario_id]);
                if ($stmt_check_candidato_existente->fetch()) {
                    $erros[] = "Este funcionário já está inscrito como candidato nesta eleição.";
                }
            }
        } catch (PDOException $e) {
            error_log("Erro ao validar funcionário para candidatura: " . $e->getMessage());
            $erros[] = "Erro de banco de dados ao validar funcionário.";
        }
    }

    // Validação do número do candidato (se fornecido)
    if ($numero_candidato_raw !== '' && $numero_candidato === false) { // Se foi fornecido algo, mas não é int válido
        $erros[] = "Número do candidato deve ser um valor numérico.";
    } elseif ($numero_candidato !== null) {
        if ($numero_candidato <= 0) $erros[] = "Número do candidato deve ser positivo.";
        // Verificar unicidade do número do candidato para esta eleição
        try {
            $stmt_check_num_candidato = $pdo->prepare("SELECT id FROM candidatos WHERE eleicao_id = ? AND numero_candidato = ?");
            $stmt_check_num_candidato->execute([$eleicao_id, $numero_candidato]);
            if ($stmt_check_num_candidato->fetch()) {
                $erros[] = "Este número de candidato já está em uso nesta eleição.";
            }
        } catch (PDOException $e) {
            error_log("Erro ao verificar unicidade do número do candidato: " . $e->getMessage());
            $erros[] = "Erro de banco de dados ao verificar número do candidato.";
        }
    }


    if (empty($erros)) {
        try {
            $sql = "INSERT INTO candidatos (eleicao_id, funcionario_id, numero_candidato, nome_urna, plataforma_propostas, status_candidatura)
                    VALUES (?, ?, ?, ?, ?, 'Inscrito')";
            $stmt_insert = $pdo->prepare($sql);
            $stmt_insert->execute([$eleicao_id, $funcionario_id, $numero_candidato, $nome_urna, $plataforma_propostas]);

            $_SESSION['mensagem_sucesso_candidato'] = "Candidato inscrito com sucesso!";
            header("Location: gerenciar_candidatos.php");
            exit();

        } catch (PDOException $e) {
            error_log("Erro ao inscrever candidato: " . $e->getMessage());
            if ($e->getCode() == '23000') { // Violação de constraint UNIQUE
                 $erros[] = "Erro: Funcionário já candidato ou número de candidato duplicado (verificação dupla).";
            } else {
                $erros[] = "Erro ao salvar candidato no banco de dados. Detalhe: " . $e->getMessage();
            }
            $_SESSION['erros_inscrever_candidato'] = $erros;
            $_SESSION['dados_formulario_candidato'] = $dados_formulario;
            header("Location: gerenciar_candidatos.php");
            exit();
        }
    } else {
        $_SESSION['erros_inscrever_candidato'] = $erros;
        $_SESSION['dados_formulario_candidato'] = $dados_formulario;
        header("Location: gerenciar_candidatos.php");
        exit();
    }

} else {
    header("Location: gerenciar_candidatos.php");
    exit();
}
?>

<?php
require_once '../../scripts/auth_empresa.php';
require_once '../../scripts/db_connection.php';

$empresa_id = $_SESSION['empresa_id'];
$erros = [];
$dados_formulario = $_POST; // Para repopular

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_completo = trim($dados_formulario['nome_completo'] ?? '');
    $cpf = trim($dados_formulario['cpf'] ?? ''); // TODO: Implementar máscara e validação de formato/algoritmo
    $email = trim($dados_formulario['email'] ?? null);
    $email = empty($email) ? null : $email; // Tratar email vazio como NULL
    $matricula = trim($dados_formulario['matricula'] ?? null);
    $matricula = empty($matricula) ? null : $matricula; // Tratar matricula vazia como NULL
    $data_admissao = $dados_formulario['data_admissao'] ?? '';
    $cargo = trim($dados_formulario['cargo'] ?? null);
    $departamento = trim($dados_formulario['departamento'] ?? null);

    $filial_id_raw = trim($dados_formulario['filial_id'] ?? '');
    $filial_id = empty($filial_id_raw) ? null : filter_var($filial_id_raw, FILTER_VALIDATE_INT);

    $status_funcionario = $dados_formulario['status_funcionario'] ?? 'Ativo';
    $permite_votar = isset($dados_formulario['permite_votar']) ? 1 : 0;

    // Validações básicas
    if (empty($nome_completo)) $erros[] = "Nome completo é obrigatório.";
    if (empty($cpf)) $erros[] = "CPF é obrigatório."; // TODO: Validar formato e algoritmo do CPF
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = "Formato de email inválido.";
    if (empty($data_admissao)) {
        $erros[] = "Data de admissão é obrigatória.";
    } else {
        // Validar formato da data YYYY-MM-DD
        $d = DateTime::createFromFormat('Y-m-d', $data_admissao);
        if (!($d && $d->format('Y-m-d') === $data_admissao)) {
            $erros[] = "Formato de data de admissão inválido. Use AAAA-MM-DD.";
        }
    }
    if ($filial_id_raw !== '' && $filial_id === false) $erros[] = "ID da Filial inválido (deve ser um número).";

    $status_permitidos = ['Ativo', 'Inativo', 'Demitido', 'Afastado'];
    if (!in_array($status_funcionario, $status_permitidos)) $erros[] = "Status do funcionário inválido.";

    // Validação de unicidade (CPF, Email, Matrícula) dentro da empresa
    if (empty($erros)) {
        try {
            // CPF
            $stmt_cpf = $pdo->prepare("SELECT id FROM funcionarios WHERE cpf = ? AND empresa_id = ?");
            $stmt_cpf->execute([$cpf, $empresa_id]);
            if ($stmt_cpf->fetch()) $erros[] = "CPF já cadastrado para esta empresa.";

            // Email (se fornecido)
            if ($email !== null) {
                $stmt_email = $pdo->prepare("SELECT id FROM funcionarios WHERE email = ? AND empresa_id = ?");
                $stmt_email->execute([$email, $empresa_id]);
                if ($stmt_email->fetch()) $erros[] = "Email já cadastrado para esta empresa.";
            }

            // Matrícula (se fornecida)
            if ($matricula !== null) {
                $stmt_matricula = $pdo->prepare("SELECT id FROM funcionarios WHERE matricula = ? AND empresa_id = ?");
                $stmt_matricula->execute([$matricula, $empresa_id]);
                if ($stmt_matricula->fetch()) $erros[] = "Matrícula já cadastrada para esta empresa.";
            }

            // Validar se filial_id (se fornecido) pertence à empresa_id
            if ($filial_id !== null) {
                $stmt_filial = $pdo->prepare("SELECT id FROM filiais WHERE id = ? AND empresa_id = ?");
                $stmt_filial->execute([$filial_id, $empresa_id]);
                if ($stmt_filial->fetch() === false) {
                    $erros[] = "Filial selecionada não pertence à sua empresa ou não existe.";
                }
            }

        } catch (PDOException $e) {
            error_log("Erro ao verificar unicidade de funcionário: " . $e->getMessage());
            $erros[] = "Erro de banco de dados ao verificar dados. Tente novamente.";
        }
    }

    if (empty($erros)) {
        try {
            $sql = "INSERT INTO funcionarios (empresa_id, filial_id, nome_completo, cpf, email, matricula, data_admissao, cargo, departamento, status_funcionario, permite_votar)
                    VALUES (:empresa_id, :filial_id, :nome_completo, :cpf, :email, :matricula, :data_admissao, :cargo, :departamento, :status_funcionario, :permite_votar)";
            $stmt_insert = $pdo->prepare($sql);
            $stmt_insert->execute([
                ':empresa_id' => $empresa_id,
                ':filial_id' => $filial_id,
                ':nome_completo' => $nome_completo,
                ':cpf' => $cpf,
                ':email' => $email,
                ':matricula' => $matricula,
                ':data_admissao' => $data_admissao,
                ':cargo' => $cargo,
                ':departamento' => $departamento,
                ':status_funcionario' => $status_funcionario,
                ':permite_votar' => $permite_votar
            ]);

            $_SESSION['mensagem_sucesso'] = "Funcionário cadastrado com sucesso!";
            header("Location: gerenciar_funcionarios.php");
            exit();

        } catch (PDOException $e) {
            error_log("Erro ao cadastrar funcionário: " . $e->getMessage());
            $erros[] = "Erro ao salvar funcionário no banco de dados. Detalhe: " . $e->getMessage();
            // Para debug: $erros[] = "Erro ao cadastrar: " . $e->getMessage() . $e->getCode();
            // if ($e->getCode() == '23000') { // Código de violação de constraint unique
            //    $erros[] = "Erro: CPF, Email ou Matrícula já existem para esta empresa (verificação dupla).";
            // }
            $_SESSION['erros_cadastro_funcionario'] = $erros;
            $_SESSION['dados_formulario_funcionario'] = $dados_formulario;
            header("Location: cadastrar_funcionario.php");
            exit();
        }
    } else {
        $_SESSION['erros_cadastro_funcionario'] = $erros;
        $_SESSION['dados_formulario_funcionario'] = $dados_formulario;
        header("Location: cadastrar_funcionario.php");
        exit();
    }

} else {
    header("Location: cadastrar_funcionario.php");
    exit();
}
?>

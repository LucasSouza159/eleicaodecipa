<?php
require_once '../../scripts/auth_empresa.php';
require_once '../../scripts/db_connection.php';

$empresa_id = $_SESSION['empresa_id'];
$erros = [];
$dados_formulario = $_POST; // Para repopular

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $funcionario_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$funcionario_id) {
        $_SESSION['mensagem_erro'] = "ID do funcionário inválido para edição.";
        header("Location: gerenciar_funcionarios.php");
        exit();
    }

    // Verificar se o funcionário pertence à empresa logada antes de qualquer coisa
    try {
        $stmt_check_owner = $pdo->prepare("SELECT id FROM funcionarios WHERE id = ? AND empresa_id = ?");
        $stmt_check_owner->execute([$funcionario_id, $empresa_id]);
        if ($stmt_check_owner->fetch() === false) {
            $_SESSION['mensagem_erro'] = "Funcionário não encontrado ou não pertence à sua empresa (falha na edição).";
            header("Location: gerenciar_funcionarios.php");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Erro ao verificar propriedade do funcionário: " . $e->getMessage());
        $_SESSION['mensagem_erro'] = "Erro de banco de dados ao verificar funcionário.";
        header("Location: gerenciar_funcionarios.php");
        exit();
    }


    $nome_completo = trim($dados_formulario['nome_completo'] ?? '');
    $cpf = trim($dados_formulario['cpf'] ?? '');
    $email = trim($dados_formulario['email'] ?? null);
    $email = empty($email) ? null : $email;
    $matricula = trim($dados_formulario['matricula'] ?? null);
    $matricula = empty($matricula) ? null : $matricula;
    $data_admissao = $dados_formulario['data_admissao'] ?? '';
    $cargo = trim($dados_formulario['cargo'] ?? null);
    $departamento = trim($dados_formulario['departamento'] ?? null);

    $filial_id_raw = trim($dados_formulario['filial_id'] ?? '');
    $filial_id = empty($filial_id_raw) ? null : filter_var($filial_id_raw, FILTER_VALIDATE_INT);

    $status_funcionario = $dados_formulario['status_funcionario'] ?? 'Ativo';
    $permite_votar = isset($dados_formulario['permite_votar']) ? 1 : 0;

    // Validações (similares ao cadastro)
    if (empty($nome_completo)) $erros[] = "Nome completo é obrigatório.";
    if (empty($cpf)) $erros[] = "CPF é obrigatório.";
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = "Formato de email inválido.";
    if (empty($data_admissao)) {
        $erros[] = "Data de admissão é obrigatória.";
    } else {
        $d = DateTime::createFromFormat('Y-m-d', $data_admissao);
        if (!($d && $d->format('Y-m-d') === $data_admissao)) {
            $erros[] = "Formato de data de admissão inválido. Use AAAA-MM-DD.";
        }
    }
    if ($filial_id_raw !== '' && $filial_id === false) $erros[] = "ID da Filial inválido (deve ser um número).";

    $status_permitidos = ['Ativo', 'Inativo', 'Demitido', 'Afastado'];
    if (!in_array($status_funcionario, $status_permitidos)) $erros[] = "Status do funcionário inválido.";

    // Validação de unicidade (CPF, Email, Matrícula) desconsiderando o próprio funcionário
    if (empty($erros)) {
        try {
            $stmt_cpf = $pdo->prepare("SELECT id FROM funcionarios WHERE cpf = ? AND empresa_id = ? AND id != ?");
            $stmt_cpf->execute([$cpf, $empresa_id, $funcionario_id]);
            if ($stmt_cpf->fetch()) $erros[] = "CPF já cadastrado para outro funcionário nesta empresa.";

            if ($email !== null) {
                $stmt_email = $pdo->prepare("SELECT id FROM funcionarios WHERE email = ? AND empresa_id = ? AND id != ?");
                $stmt_email->execute([$email, $empresa_id, $funcionario_id]);
                if ($stmt_email->fetch()) $erros[] = "Email já cadastrado para outro funcionário nesta empresa.";
            }

            if ($matricula !== null) {
                $stmt_matricula = $pdo->prepare("SELECT id FROM funcionarios WHERE matricula = ? AND empresa_id = ? AND id != ?");
                $stmt_matricula->execute([$matricula, $empresa_id, $funcionario_id]);
                if ($stmt_matricula->fetch()) $erros[] = "Matrícula já cadastrada para outro funcionário nesta empresa.";
            }

            if ($filial_id !== null) {
                $stmt_filial = $pdo->prepare("SELECT id FROM filiais WHERE id = ? AND empresa_id = ?");
                $stmt_filial->execute([$filial_id, $empresa_id]);
                if ($stmt_filial->fetch() === false) {
                    $erros[] = "Filial selecionada não pertence à sua empresa ou não existe.";
                }
            }

        } catch (PDOException $e) {
            error_log("Erro ao verificar unicidade na edição de funcionário: " . $e->getMessage());
            $erros[] = "Erro de banco de dados ao verificar dados para edição. Tente novamente.";
        }
    }

    if (empty($erros)) {
        try {
            $sql = "UPDATE funcionarios SET
                        nome_completo = :nome_completo,
                        cpf = :cpf,
                        email = :email,
                        matricula = :matricula,
                        data_admissao = :data_admissao,
                        cargo = :cargo,
                        departamento = :departamento,
                        filial_id = :filial_id,
                        status_funcionario = :status_funcionario,
                        permite_votar = :permite_votar
                    WHERE id = :id AND empresa_id = :empresa_id";

            $stmt_update = $pdo->prepare($sql);
            $stmt_update->execute([
                ':nome_completo' => $nome_completo,
                ':cpf' => $cpf,
                ':email' => $email,
                ':matricula' => $matricula,
                ':data_admissao' => $data_admissao,
                ':cargo' => $cargo,
                ':departamento' => $departamento,
                ':filial_id' => $filial_id,
                ':status_funcionario' => $status_funcionario,
                ':permite_votar' => $permite_votar,
                ':id' => $funcionario_id,
                ':empresa_id' => $empresa_id
            ]);

            $_SESSION['mensagem_sucesso'] = "Funcionário atualizado com sucesso!";
            header("Location: gerenciar_funcionarios.php");
            exit();

        } catch (PDOException $e) {
            error_log("Erro ao editar funcionário: " . $e->getMessage());
            $erros[] = "Erro ao atualizar funcionário no banco de dados. Detalhe: " . $e->getMessage();
            $_SESSION['erros_editar_funcionario'] = $erros;
            $_SESSION['dados_formulario_funcionario'] = $dados_formulario; // Mantém os dados que o usuário tentou submeter
            header("Location: editar_funcionario.php?id=" . $funcionario_id);
            exit();
        }
    } else {
        $_SESSION['erros_editar_funcionario'] = $erros;
        $_SESSION['dados_formulario_funcionario'] = $dados_formulario; // Mantém os dados que o usuário tentou submeter
        header("Location: editar_funcionario.php?id=" . $funcionario_id);
        exit();
    }

} else {
    // Se não for POST, redireciona para a lista, pois não há ID para editar
    header("Location: gerenciar_funcionarios.php");
    exit();
}
?>

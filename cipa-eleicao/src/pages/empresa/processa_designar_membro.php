<?php
require_once '../../scripts/auth_empresa.php';
require_once '../../scripts/db_connection.php';

$empresa_id_logada = $_SESSION['empresa_id'];
$erros = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eleicao_id = filter_input(INPUT_POST, 'eleicao_id', FILTER_VALIDATE_INT);
    $nome_completo = trim(filter_input(INPUT_POST, 'nome_completo'));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $cpf = trim(filter_input(INPUT_POST, 'cpf')); // TODO: Adicionar máscara e validação de formato de CPF (XXX.XXX.XXX-XX)
    $papel_comissao = trim(filter_input(INPUT_POST, 'papel_comissao'));

    $dados_formulario_membro = $_POST; // Para repopular

    // Validar eleicao_id e se pertence à empresa
    if (!$eleicao_id) {
        $_SESSION['mensagem_erro'] = "ID da eleição inválido ao processar membro.";
        header("Location: gerenciar_eleicoes.php"); // Redireciona para a lista geral se o ID da eleição é perdido
        exit();
    }

    try {
        $stmt_check_eleicao = $pdo->prepare("SELECT id FROM eleicoes WHERE id = ? AND empresa_id = ?");
        $stmt_check_eleicao->execute([$eleicao_id, $empresa_id_logada]);
        if ($stmt_check_eleicao->fetch() === false) {
            $_SESSION['mensagem_erro'] = "A eleição especificada não pertence à sua empresa.";
            header("Location: gerenciar_eleicoes.php");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Erro ao verificar eleição: " . $e->getMessage());
        $_SESSION['mensagem_erro'] = "Erro de banco de dados ao verificar eleição.";
        header("Location: gerenciar_comissao_eleitoral.php?eleicao_id=" . $eleicao_id);
        exit();
    }

    // Validações dos dados do membro
    if (empty($nome_completo)) $erros[] = "Nome completo é obrigatório.";
    if (empty($email)) {
        $erros[] = "Email é obrigatório.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Formato de email inválido.";
    }
    if (empty($cpf)) {
        $erros[] = "CPF é obrigatório.";
    } // TODO: Adicionar validação de formato e algoritmo do CPF.
    $papeis_permitidos = ['Presidente', 'Secretário', 'Membro'];
    if (empty($papel_comissao) || !in_array($papel_comissao, $papeis_permitidos)) {
        $erros[] = "Papel na comissão é obrigatório e deve ser um valor válido.";
    }

    // Verificar duplicidade de email ou CPF na mesma eleição
    if (empty($erros)) {
        try {
            $stmt_check_email = $pdo->prepare("SELECT id FROM comissao WHERE email = ? AND eleicao_id = ?");
            $stmt_check_email->execute([$email, $eleicao_id]);
            if ($stmt_check_email->fetch()) {
                $erros[] = "Este email já está designado para a comissão desta eleição.";
            }

            $stmt_check_cpf = $pdo->prepare("SELECT id FROM comissao WHERE cpf = ? AND eleicao_id = ?");
            $stmt_check_cpf->execute([$cpf, $eleicao_id]);
            if ($stmt_check_cpf->fetch()) {
                $erros[] = "Este CPF já está designado para a comissão desta eleição.";
            }
        } catch (PDOException $e) {
            error_log("Erro ao verificar duplicidade de membro: " . $e->getMessage());
            $erros[] = "Erro de banco de dados ao verificar duplicidade. Tente novamente.";
        }
    }

    if (empty($erros)) {
        try {
            $sql = "INSERT INTO comissao (eleicao_id, nome_completo, email, cpf, papel_comissao, senha_hash_comissao)
                    VALUES (?, ?, ?, ?, ?, NULL)"; // senha_hash_comissao como NULL por agora
            $stmt_insert = $pdo->prepare($sql);
            $stmt_insert->execute([$eleicao_id, $nome_completo, $email, $cpf, $papel_comissao]);

            $_SESSION['mensagem_sucesso'] = "Membro da comissão designado com sucesso!";
            header("Location: gerenciar_comissao_eleitoral.php?eleicao_id=" . $eleicao_id);
            exit();

        } catch (PDOException $e) {
            error_log("Erro ao designar membro: " . $e->getMessage());
            // Verificar se o erro é de constraint unique (código 23000 ou 1062 para MySQL)
            if ($e->getCode() == '23000' || $e->getCode() == 1062) {
                 $erros[] = "Erro: Email ou CPF já cadastrado para esta eleição (verificação dupla).";
            } else {
                $erros[] = "Erro ao salvar membro no banco de dados. Detalhe: " . $e->getMessage();
            }
            $_SESSION['erros_designar_membro'] = $erros;
            $_SESSION['dados_formulario_membro'] = $dados_formulario_membro;
            header("Location: gerenciar_comissao_eleitoral.php?eleicao_id=" . $eleicao_id);
            exit();
        }
    } else {
        $_SESSION['erros_designar_membro'] = $erros;
        $_SESSION['dados_formulario_membro'] = $dados_formulario_membro;
        header("Location: gerenciar_comissao_eleitoral.php?eleicao_id=" . $eleicao_id);
        exit();
    }

} else {
    // Redirecionar se não for POST, idealmente para a lista de eleições ou página anterior
    header("Location: gerenciar_eleicoes.php");
    exit();
}
?>

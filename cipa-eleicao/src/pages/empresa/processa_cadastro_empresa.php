<?php
session_start();
require_once '../../scripts/db_connection.php'; // Inclui o arquivo de conexão
require_once '../../scripts/utils.php'; // Inclui as funções de utilidades

$erros = [];
$dados_formulario = $_POST; // Para repopular o formulário em caso de erro

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_fantasia = trim($_POST['nome_fantasia'] ?? '');
    $razao_social = trim($_POST['razao_social'] ?? '');
    $cnpj = trim($_POST['cnpj'] ?? ''); // TODO: Validar e limpar formato do CNPJ (ex: remover pontos e traços)
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirma_senha = $_POST['confirma_senha'] ?? '';

    // Validações básicas
    if (empty($nome_fantasia)) {
        $erros[] = "Nome Fantasia é obrigatório.";
    }
    if (empty($razao_social)) {
        $erros[] = "Razão Social é obrigatória.";
    }
    if (empty($cnpj)) {
        $erros[] = "CNPJ é obrigatório.";
    } elseif (!validarCNPJ($cnpj)) {
        $erros[] = "CNPJ inválido. Verifique o número digitado.";
    }
    // TODO: Adicionar limpeza do CNPJ (remover pontos, traços, barras) antes de validar e salvar.
    // $cnpj_limpo = preg_replace('/[^0-9]/', '', $cnpj); e usar $cnpj_limpo

    if (empty($email)) {
        $erros[] = "Email é obrigatório.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Formato de email inválido.";
    }
    if (empty($senha)) {
        $erros[] = "Senha é obrigatória.";
    } elseif (strlen($senha) < 6) { // Exemplo de requisito de tamanho mínimo
        $erros[] = "A senha deve ter pelo menos 6 caracteres.";
    }
    if ($senha !== $confirma_senha) {
        $erros[] = "As senhas não coincidem.";
    }

    // Se não houver erros de validação básica, verificar unicidade de CNPJ e Email
    if (empty($erros)) {
        try {
            // Verificar se CNPJ já existe
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM empresas WHERE cnpj = ?");
            $stmt->execute([$cnpj]);
            if ($stmt->fetchColumn() > 0) {
                $erros[] = "Este CNPJ já está cadastrado.";
            }

            // Verificar se Email já existe
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM empresas WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $erros[] = "Este Email já está cadastrado.";
            }
        } catch (PDOException $e) {
            error_log("Erro ao verificar CNPJ/Email: " . $e->getMessage());
            $erros[] = "Erro ao verificar dados. Tente novamente.";
        }
    }

    if (empty($erros)) {
        // Processar cadastro
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        try {
            $sql = "INSERT INTO empresas (nome_fantasia, razao_social, cnpj, email, senha_hash) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            // Bind dos parâmetros
            $stmt->bindParam(1, $nome_fantasia);
            $stmt->bindParam(2, $razao_social);
            $stmt->bindParam(3, $cnpj);
            $stmt->bindParam(4, $email);
            $stmt->bindParam(5, $senha_hash);

            $stmt->execute();
            $nova_empresa_id = $pdo->lastInsertId();

            registrarLog($pdo, 'AUDIT', 'CADASTRO_EMPRESA_SUCESSO', ['empresa_id' => $nova_empresa_id, 'email' => $email, 'cnpj' => $cnpj]);

            $_SESSION['mensagem_sucesso'] = "Empresa cadastrada com sucesso! Faça login.";
            header("Location: login_empresa.php");
            exit();
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar empresa: " . $e->getMessage());
            registrarLog($pdo, 'ERROR', 'CADASTRO_EMPRESA_FALHA_DB', ['erro' => $e->getMessage(), 'dados' => $dados_formulario]);
            $erros[] = "Erro ao processar o cadastro. Por favor, tente novamente mais tarde.";
        }
    }

    if (!empty($erros)) {
        registrarLog($pdo, 'WARNING', 'CADASTRO_EMPRESA_FALHA_VALIDACAO', ['erros' => $erros, 'dados' => $dados_formulario]);
        $_SESSION['erros_cadastro'] = $erros;
        $_SESSION['dados_formulario_cadastro'] = $dados_formulario;
        header("Location: cadastro_empresa.php");
        exit();
    }

} else {
    // Redirecionar se não for POST (ou tentativa de acesso direto)
    registrarLog($pdo ?? null, 'INFO', 'ACESSO_INVALIDO_PROCESSO_CADASTRO_EMPRESA', ['metodo_http' => $_SERVER["REQUEST_METHOD"]]);
    header("Location: cadastro_empresa.php");
    exit();
}
?>

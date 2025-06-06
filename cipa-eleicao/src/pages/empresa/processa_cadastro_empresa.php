<?php
session_start();
require_once '../../scripts/db_connection.php'; // Inclui o arquivo de conexão

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
    } // TODO: Adicionar validação de formato de CNPJ (XX.XXX.XXX/XXXX-XX) e algoritmo
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

            $_SESSION['mensagem_sucesso'] = "Empresa cadastrada com sucesso! Faça login.";
            header("Location: login_empresa.php");
            exit();
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar empresa: " . $e->getMessage());
            // Mensagem mais genérica para o usuário
            $erros[] = "Erro ao processar o cadastro. Por favor, tente novamente mais tarde.";
            // Para depuração, poderia ser $erros[] = "Erro ao cadastrar empresa: " . $e->getMessage();
        }
    }

    // Se houver erros, armazenar na sessão para exibir no formulário
    if (!empty($erros)) {
        $_SESSION['erros_cadastro'] = $erros;
        $_SESSION['dados_formulario_cadastro'] = $dados_formulario;
        header("Location: cadastro_empresa.php");
        exit();
    }

} else {
    // Redirecionar se não for POST
    header("Location: cadastro_empresa.php");
    exit();
}
?>

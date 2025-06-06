<?php
session_start();
require_once '../../scripts/db_connection.php'; // Inclui o arquivo de conexão

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = trim($_POST['login'] ?? ''); // Pode ser CNPJ ou Email
    $senha = $_POST['senha'] ?? '';

    if (empty($login) || empty($senha)) {
        $_SESSION['erro_login'] = "CNPJ/Email e Senha são obrigatórios.";
        header("Location: login_empresa.php");
        exit();
    }

    try {
        // Determinar se o login é um CNPJ ou Email (simplificado)
        // Uma abordagem mais robusta poderia verificar o formato do CNPJ vs Email
        $campo_busca = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'cnpj';

        $sql = "SELECT id, nome_fantasia, senha_hash FROM empresas WHERE $campo_busca = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$login]);
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($empresa && password_verify($senha, $empresa['senha_hash'])) {
            // Login bem-sucedido
            $_SESSION['empresa_id'] = $empresa['id'];
            $_SESSION['empresa_nome_fantasia'] = $empresa['nome_fantasia']; // Corrigido para nome_fantasia como no cadastro

            // Regenerar ID da sessão para segurança
            session_regenerate_id(true);

            header("Location: painel_empresa.php");
            exit();
        } else {
            // Credenciais inválidas
            $_SESSION['erro_login'] = "CNPJ/Email ou Senha inválidos.";
            header("Location: login_empresa.php");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Erro de login: " . $e->getMessage());
        $_SESSION['erro_login'] = "Erro no sistema ao tentar fazer login. Tente novamente mais tarde.";
        header("Location: login_empresa.php");
        exit();
    }

} else {
    // Redirecionar se não for POST
    header("Location: login_empresa.php");
    exit();
}
?>

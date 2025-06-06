<?php
session_start();
require_once '../../scripts/db_connection.php'; // Inclui o arquivo de conexão
require_once '../../scripts/utils.php';      // Inclui registrarLog

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
            $_SESSION['empresa_nome_fantasia'] = $empresa['nome_fantasia'];

            session_regenerate_id(true);

            registrarLog($pdo, 'AUDIT', 'LOGIN_EMPRESA_SUCESSO', ['empresa_id' => $empresa['id'], 'login_usado' => $login], $empresa['id'], 'Empresa');

            header("Location: painel_empresa.php");
            exit();
        } else {
            registrarLog($pdo, 'WARNING', 'LOGIN_EMPRESA_FALHA', ['login_usado' => $login, 'motivo' => 'Credenciais invalidas']);
            $_SESSION['erro_login'] = "CNPJ/Email ou Senha inválidos.";
            $_SESSION['dados_formulario_login'] = ['login' => $login]; // Para repopular
            header("Location: login_empresa.php");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Erro de login (Empresa): " . $e->getMessage());
        registrarLog($pdo, 'ERROR', 'LOGIN_EMPRESA_ERRO_DB', ['login_usado' => $login, 'erro' => $e->getMessage()]);
        $_SESSION['erro_login'] = "Erro no sistema ao tentar fazer login. Tente novamente mais tarde.";
        $_SESSION['dados_formulario_login'] = ['login' => $login];
        header("Location: login_empresa.php");
        exit();
    }

} else {
    registrarLog($pdo ?? null, 'INFO', 'ACESSO_INVALIDO_PROCESSO_LOGIN_EMPRESA', ['metodo_http' => $_SERVER["REQUEST_METHOD"]]);
    header("Location: login_empresa.php");
    exit();
}
?>

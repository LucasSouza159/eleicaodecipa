<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se a empresa está logada
if (!isset($_SESSION['empresa_id'])) {
    // Se não estiver logado, definir uma mensagem de erro (opcional)
    $_SESSION['erro_acesso'] = "Você precisa estar logado para acessar esta página.";

    // Redirecionar para a página de login
    // O caminho para login_empresa.php pode precisar ser ajustado dependendo de onde este script é incluído.
    // Assumindo que está em /src/scripts/ e o login está em /src/pages/empresa/
    header("Location: ../pages/empresa/login_empresa.php");
    exit();
}

// Opcional: verificar atividade da sessão para expirar automaticamente após um tempo
// if (isset($_SESSION['ultima_atividade']) && (time() - $_SESSION['ultima_atividade'] > 1800)) { // 30 minutos
//     // session_unset();     // remove todas as variáveis de sessão
//     // session_destroy();   // destrói a sessão
//     // header("Location: ../pages/empresa/login_empresa.php?sessao_expirada=1");
//     // exit();
// }
// $_SESSION['ultima_atividade'] = time(); // Atualizar o timestamp da última atividade

// Se chegou até aqui, a empresa está autenticada.
// Pode-se carregar mais dados da empresa da sessão se necessário.
$empresa_id_logada = $_SESSION['empresa_id'];
$empresa_nome_logada = $_SESSION['empresa_nome_fantasia'] ?? 'Empresa';

?>

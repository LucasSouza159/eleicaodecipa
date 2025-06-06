<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o administrador está logado
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['erro_login_admin'] = "Acesso não autorizado. Por favor, faça login como administrador.";

    // Determinar o caminho correto para login_admin.php a partir de /src/scripts/
    // Se este script é incluído por um arquivo em /src/pages/admin/, o redirecionamento deve ser para 'login_admin.php'
    // No entanto, se o script que inclui está em um nível diferente, isso precisa ser ajustado.
    // Para maior robustez, seria melhor usar um caminho absoluto ou uma constante de base URL.
    // Assumindo que quem inclui está em /src/pages/admin/
    // header("Location: login_admin.php"); // Isso funcionaria se auth_admin.php estivesse em /src/pages/admin/

    // Se o script que inclui auth_admin.php está em /src/pages/admin/,
    // o redirecionamento para login_admin.php (na mesma pasta) é direto.
    header("Location: login_admin.php");
    exit();
}

// Opcional: verificar atividade da sessão para expirar automaticamente após um tempo
// if (isset($_SESSION['admin_ultima_atividade']) && (time() - $_SESSION['admin_ultima_atividade'] > 3600)) { // 1 hora
//     unset($_SESSION['admin_id']); // Limpar apenas as variáveis de sessão do admin
//     unset($_SESSION['admin_nome']);
//     unset($_SESSION['admin_email']);
//     unset($_SESSION['admin_nivel_acesso']);
//     unset($_SESSION['admin_ultima_atividade']);
//     // session_destroy(); // Evitar se houver outros contextos de sessão
//     $_SESSION['erro_login_admin'] = "Sua sessão de administrador expirou por inatividade.";
//     header("Location: ../pages/admin/login_admin.php");
//     exit();
// }
// $_SESSION['admin_ultima_atividade'] = time(); // Atualizar o timestamp da última atividade

// Disponibilizar variáveis da sessão para fácil acesso nas páginas do painel admin
$admin_id_logado = $_SESSION['admin_id'];
$admin_nome_logado = $_SESSION['admin_nome'] ?? 'Admin';
$admin_email_logado = $_SESSION['admin_email'] ?? '';
$admin_nivel_acesso_logado = $_SESSION['admin_nivel_acesso'] ?? 'Admin';

?>

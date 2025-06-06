<?php
session_start();

// Destruir todas as variáveis de sessão do admin.
unset($_SESSION['admin_id']);
unset($_SESSION['admin_nome']);
unset($_SESSION['admin_email']);
unset($_SESSION['admin_nivel_acesso']);
unset($_SESSION['admin_ultima_atividade']); // Se estiver usando expiração de sessão

// Se você quiser destruir completamente a sessão (cuidado se houver outros contextos de login):
// session_destroy();

// Opcional: Definir uma mensagem de logout para exibir na página de login
$_SESSION['mensagem_logout_admin'] = "Você saiu do painel administrativo com sucesso.";

// Redirecionar para a página de login do admin
header("Location: login_admin.php");
exit();
?>

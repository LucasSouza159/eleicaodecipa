<?php
session_start();

// Destruir variáveis de sessão específicas da votação
unset($_SESSION['votacao_funcionario_id']);
unset($_SESSION['votacao_eleicao_id']);
unset($_SESSION['votacao_nome_funcionario']);
unset($_SESSION['votacao_ultima_atividade']); // Se estiver usando expiração de sessão

// Não é recomendado usar session_destroy() se você tiver outros contextos de login (ex: admin)
// na mesma sessão. Se a sessão é exclusivamente para votação, session_destroy() pode ser usado.
// Para este caso, apenas limpar as variáveis é mais seguro.

// Opcional: Definir uma mensagem de logout para exibir na página de login
// $_SESSION['mensagem_logout_votacao'] = "Você saiu da área de votação.";

// Redirecionar para a página de login da votação
header("Location: login_votacao.php");
exit();
?>

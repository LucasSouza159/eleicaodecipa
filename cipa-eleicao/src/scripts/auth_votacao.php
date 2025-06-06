<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o funcionário está logado para votar
if (!isset($_SESSION['votacao_funcionario_id']) || !isset($_SESSION['votacao_eleicao_id'])) {
    $_SESSION['erro_login_votacao'] = "Acesso não autorizado. Por favor, faça login para votar.";

    // Se o script que inclui auth_votacao.php está em /src/pages/votacao/,
    // o redirecionamento para login_votacao.php (na mesma pasta) é direto.
    header("Location: login_votacao.php");
    exit();
}

// Opcional: verificar atividade da sessão para expirar automaticamente após um tempo
// if (isset($_SESSION['votacao_ultima_atividade']) && (time() - $_SESSION['votacao_ultima_atividade'] > 600)) { // 10 minutos para votar
//     // Destruir apenas variáveis de sessão da votação
//     unset($_SESSION['votacao_funcionario_id']);
//     unset($_SESSION['votacao_eleicao_id']);
//     unset($_SESSION['votacao_nome_funcionario']);
//     unset($_SESSION['votacao_ultima_atividade']);
//     // session_destroy(); // Evitar destruir a sessão inteira se houver outros contextos
//     $_SESSION['erro_login_votacao'] = "Sua sessão de votação expirou por inatividade.";
//     header("Location: login_votacao.php");
//     exit();
// }
// $_SESSION['votacao_ultima_atividade'] = time(); // Atualizar o timestamp da última atividade

// Disponibilizar variáveis da sessão para fácil acesso nas páginas de votação
$votacao_funcionario_id_logado = $_SESSION['votacao_funcionario_id'];
$votacao_eleicao_id_logado = $_SESSION['votacao_eleicao_id'];
$votacao_nome_funcionario_logado = $_SESSION['votacao_nome_funcionario'] ?? 'Eleitor(a)';

?>

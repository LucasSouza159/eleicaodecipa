<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o membro da comissão está logado
if (!isset($_SESSION['comissao_id'])) {
    $_SESSION['erro_acesso_comissao'] = "Você precisa estar logado como membro da comissão para acessar esta página.";

    // Determinar o caminho base do projeto se necessário, ou usar caminho relativo
    // Se este script está em /src/scripts/ e o login está em /src/pages/comissao/
    $login_page = '../pages/comissao/login_comissao.php';
    // Se o script for incluído de uma página dentro de /src/pages/comissao/, o caminho para o login seria apenas 'login_comissao.php'
    // Para maior robustez, poderíamos definir uma constante com o caminho base do site.
    // Por ora, assumindo que será incluído de dentro de /src/pages/comissao/
    // header("Location: login_comissao.php"); // Este caminho seria se auth_comissao.php estivesse no mesmo nível de painel_comissao.php

    // Caminho relativo de /src/scripts/ para /src/pages/comissao/login_comissao.php
    // Este é um desafio comum com includes e caminhos relativos.
    // A forma mais segura é usar caminhos absolutos a partir da raiz do documento ou um config global.
    // Se o script que inclui auth_comissao.php está em /src/pages/comissao/,
    // o redirecionamento para login_comissao.php (na mesma pasta) é direto.
    header("Location: login_comissao.php");
    exit();
}

// Opcional: verificar atividade da sessão para expirar automaticamente após um tempo
// if (isset($_SESSION['comissao_ultima_atividade']) && (time() - $_SESSION['comissao_ultima_atividade'] > 1800)) { // 30 minutos
//     session_unset();
//     session_destroy();
//     $_SESSION['erro_login_comissao'] = "Sua sessão expirou por inatividade.";
//     header("Location: login_comissao.php"); // Ajustar caminho se necessário
//     exit();
// }
// $_SESSION['comissao_ultima_atividade'] = time(); // Atualizar o timestamp da última atividade

// Se chegou até aqui, o membro da comissão está autenticado.
// Pode-se carregar mais dados da sessão para variáveis locais para fácil acesso nas páginas protegidas:
$comissao_id_logado = $_SESSION['comissao_id'];
$comissao_eleicao_id_logado = $_SESSION['comissao_eleicao_id'];
$comissao_nome_membro_logado = $_SESSION['comissao_nome_membro'];
$comissao_papel_logado = $_SESSION['comissao_papel'];
$comissao_eleicao_titulo_logado = $_SESSION['comissao_eleicao_titulo'];

?>

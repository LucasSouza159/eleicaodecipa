<?php
// Inclui o script de autenticação. session_start() é chamado dentro dele.
require_once '../../scripts/auth_empresa.php';

// Se o script auth_empresa.php redirecionar, o código abaixo não será executado.
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel da Empresa</title>
</head>
<body>
    <h2>Painel da Empresa</h2>
    <?php
    // O script auth_empresa.php já garante que 'empresa_nome_fantasia' está na sessão se o usuário estiver logado.
    // A variável $empresa_nome_logada também é definida em auth_empresa.php.
    echo "<p>Bem-vindo(a), " . htmlspecialchars($empresa_nome_logada) . "!</p>";
    ?>
    <p>Este é o seu painel de controle.</p>

    <ul>
        <li><a href="#">Gerenciar Filiais</a> (Em breve)</li>
        <li><a href="gerenciar_funcionarios.php">Gerenciar Funcionários</a></li>
        <li><a href="gerenciar_eleicoes.php">Gerenciar Eleições</a></li>
        <li><a href="#">Configurações da Conta</a> (Em breve)</li>
    </ul>

    <br>
    <a href="logout_empresa.php">Sair</a>
</body>
</html>

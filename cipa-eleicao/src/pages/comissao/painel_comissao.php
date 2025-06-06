<?php
// Inclui o script de autenticação. session_start() é chamado dentro dele.
// O script auth_comissao.php garantirá que apenas membros logados da comissão acessem.
// E também define variáveis como $comissao_nome_membro_logado, $comissao_papel_logado, etc.
require_once '../../scripts/auth_comissao.php';
require_once '../../scripts/db_connection.php'; // Para buscar mais detalhes se necessário

// $comissao_eleicao_id_logado e $comissao_eleicao_titulo_logado já estão disponíveis de auth_comissao.php
// Se precisarmos de mais dados da eleição que não estão na sessão, podemos buscar:
$eleicao_detalhes_adicionais = null;
try {
    $stmt_eleicao = $pdo->prepare("SELECT ano_referencia, status_eleicao FROM eleicoes WHERE id = ?");
    $stmt_eleicao->execute([$comissao_eleicao_id_logado]);
    $eleicao_detalhes_adicionais = $stmt_eleicao->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ao buscar detalhes da eleição no painel da comissão: " . $e->getMessage());
    // Lidar com o erro, talvez exibir uma mensagem, mas não quebrar a página inteira.
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel da Comissão Eleitoral</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; color: #333; }
        .header { background-color: #0056b3; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { margin: 0; font-size: 1.5em; }
        .header a { color: white; text-decoration: none; padding: 5px 10px; border-radius: 4px; }
        .header a:hover { background-color: #004085; }
        .container { padding: 20px; }
        .welcome-message { margin-bottom: 20px; }
        .election-info { background-color: #e9ecef; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .election-info h3 { margin-top: 0; }
        .panel-links ul { list-style-type: none; padding: 0; }
        .panel-links ul li { margin-bottom: 10px; }
        .panel-links ul li a { display: block; padding: 10px; background-color: #fff; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #007bff; }
        .panel-links ul li a:hover { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Painel da Comissão</h1>
        <a href="logout_comissao.php">Sair</a>
    </div>

    <div class="container">
        <div class="welcome-message">
            <p>Bem-vindo(a), <strong><?php echo htmlspecialchars($comissao_nome_membro_logado); ?></strong>!</p>
            <p>Você está logado como: <strong><?php echo htmlspecialchars($comissao_papel_logado); ?></strong>.</p>
        </div>

        <div class="election-info">
            <h3>Informações da Eleição Ativa</h3>
            <p><strong>Título:</strong> <?php echo htmlspecialchars($comissao_eleicao_titulo_logado); ?></p>
            <?php if ($eleicao_detalhes_adicionais): ?>
                <p><strong>Ano de Referência:</strong> <?php echo htmlspecialchars($eleicao_detalhes_adicionais['ano_referencia']); ?></p>
                <p><strong>Status Atual:</strong> <?php echo htmlspecialchars($eleicao_detalhes_adicionais['status_eleicao']); ?></p>
            <?php endif; ?>
        </div>

        <div class="panel-links">
            <h4>Funcionalidades da Comissão:</h4>
            <ul>
                <li><a href="gerenciar_candidatos.php">Gerenciar Candidaturas</a></li>
                <li><a href="configurar_votacao_comissao.php?eleicao_id=<?php echo $comissao_eleicao_id_logado; ?>">Configurar/Monitorar Votação</a> (Ex: Abrir/Encerrar)</li>
                <li><a href="apurar_resultados_comissao.php?eleicao_id=<?php echo $comissao_eleicao_id_logado; ?>">Apurar Resultados</a></li>
                <li><a href="comunicados_comissao.php?eleicao_id=<?php echo $comissao_eleicao_id_logado; ?>">Emitir Comunicados</a></li>
                <!-- Adicionar mais links conforme as funcionalidades são desenvolvidas -->
            </ul>
        </div>
    </div>
</body>
</html>

<?php
require_once '../../scripts/auth_comissao.php'; // Garante autenticação e define $comissao_eleicao_id_logado
require_once '../../scripts/db_connection.php';

$eleicao_id = $comissao_eleicao_id_logado; // Definido em auth_comissao.php
$eleicao_titulo = $comissao_eleicao_titulo_logado; // Definido em auth_comissao.php
$ano_referencia = ''; // Pode ser buscado se necessário

// Buscar detalhes adicionais da eleição, como ano de referência
try {
    $stmt_eleicao_info = $pdo->prepare("SELECT ano_referencia FROM eleicoes WHERE id = ?");
    $stmt_eleicao_info->execute([$eleicao_id]);
    $eleicao_info = $stmt_eleicao_info->fetch(PDO::FETCH_ASSOC);
    if ($eleicao_info) {
        $ano_referencia = $eleicao_info['ano_referencia'];
    }
} catch (PDOException $e) {
    error_log("Erro ao buscar ano da eleição: " . $e->getMessage());
    // Não é crítico para a funcionalidade principal da página
}


$candidatos = [];
try {
    $stmt_candidatos = $pdo->prepare(
        "SELECT c.id AS candidato_id, f.nome_completo AS funcionario_nome, c.numero_candidato, c.nome_urna, c.status_candidatura
         FROM candidatos c
         JOIN funcionarios f ON c.funcionario_id = f.id
         WHERE c.eleicao_id = ?
         ORDER BY f.nome_completo ASC"
    );
    $stmt_candidatos->execute([$eleicao_id]);
    $candidatos = $stmt_candidatos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ao buscar candidatos: " . $e->getMessage());
    $erro_db_candidatos = "Não foi possível carregar os candidatos. Tente novamente mais tarde.";
}

// Para repopular formulário em caso de erro
$erros_inscricao = $_SESSION['erros_inscrever_candidato'] ?? [];
$dados_formulario_candidato = $_SESSION['dados_formulario_candidato'] ?? [];
unset($_SESSION['erros_inscrever_candidato'], $_SESSION['dados_formulario_candidato']);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Candidatos - Comissão</title>
    <style>
        body { font-family: sans-serif; margin: 0; background-color: #f8f9fa; }
        .container { width: 90%; margin: 20px auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2, h3, h4 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; margin-bottom: 30px; }
        th, td { border: 1px solid #dee2e6; padding: 10px; text-align: left; font-size: 0.9em; }
        th { background-color: #e9ecef; }
        .mensagem { padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align:center; }
        .sucesso { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .erro { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .acoes a { margin-right: 8px; text-decoration: none; padding: 5px 8px; border-radius:4px; font-size:0.85em; }
        .acoes a.editar { background-color: #ffc107; color:black; }
        .acoes a.status { background-color: #17a2b8; color:white; } /* Placeholder */
        .form-secao { margin-top: 30px; padding: 20px; border: 1px solid #ccc; border-radius: 5px; background-color: #fdfdfd; }
        .form-secao h4 { margin-top: 0; }
        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; margin-bottom: 4px; font-weight: bold; }
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group textarea,
        .form-group select { width: 100%; padding: 8px; box-sizing: border-box; border:1px solid #ced4da; border-radius:4px; }
        .form-group textarea { min-height: 80px; }
        .button { padding:10px 15px; background-color:#007bff; color:white; border:none; border-radius:4px; cursor:pointer; font-size: 1em; }
        .button:hover { background-color:#0056b3; }
        .erro-lista { list-style-type: none; padding: 0; margin: 0 0 10px 0; color: #721c24; }
        .nav-link { color: #007bff; text-decoration:none; margin-bottom:15px; display:inline-block;}
    </style>
</head>
<body>
    <div class="container">
        <h2>Gerenciar Candidaturas</h2>
        <a href="painel_comissao.php" class="nav-link">Voltar ao Painel da Comissão</a>

        <div class="election-info">
            <h3>Eleição: <?php echo htmlspecialchars($eleicao_titulo); ?> (<?php echo htmlspecialchars($ano_referencia); ?>)</h3>
        </div>

        <?php
        if (isset($_SESSION['mensagem_sucesso_candidato'])) {
            echo "<div class='mensagem sucesso'>" . htmlspecialchars($_SESSION['mensagem_sucesso_candidato']) . "</div>";
            unset($_SESSION['mensagem_sucesso_candidato']);
        }
        if (isset($erro_db_candidatos)) { // Erro ao buscar lista de candidatos
            echo "<div class='mensagem erro'>" . htmlspecialchars($erro_db_candidatos) . "</div>";
        }
        ?>

        <h4>Candidatos Inscritos</h4>
        <?php if (empty($candidatos) && !isset($erro_db_candidatos)): ?>
            <p>Nenhum candidato inscrito para esta eleição até o momento.</p>
        <?php elseif (!empty($candidatos)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nome do Funcionário</th>
                        <th>Nº Candidato</th>
                        <th>Nome na Urna</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($candidatos as $candidato): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($candidato['funcionario_nome']); ?></td>
                            <td><?php echo htmlspecialchars($candidato['numero_candidato'] ?? 'N/D'); ?></td>
                            <td><?php echo htmlspecialchars($candidato['nome_urna'] ?? 'N/D'); ?></td>
                            <td><?php echo htmlspecialchars($candidato['status_candidatura']); ?></td>
                            <td class="acoes">
                                <a href="editar_candidatura.php?candidato_id=<?php echo $candidato['candidato_id']; ?>" class="editar">Editar</a>
                                <!-- TODO: Implementar alteração de status -->
                                <a href="alterar_status_candidato.php?candidato_id=<?php echo $candidato['candidato_id']; ?>" class="status">Alterar Status</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="form-secao">
            <h4>Inscrever Novo Candidato</h4>
            <?php if (!empty($erros_inscricao)): ?>
                <ul class="erro-lista">
                    <?php foreach ($erros_inscricao as $erro): ?>
                        <li><?php echo htmlspecialchars($erro); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <form action="processa_inscrever_candidato.php" method="POST">
                <input type="hidden" name="eleicao_id" value="<?php echo $eleicao_id; ?>">

                <div class="form-group">
                    <label for="funcionario_id">ID do Funcionário:</label>
                    <input type="number" id="funcionario_id" name="funcionario_id" value="<?php echo htmlspecialchars($dados_formulario_candidato['funcionario_id'] ?? ''); ?>" required>
                    <small>//TODO: Implementar busca/dropdown dinâmico de funcionários aptos.</small>
                </div>

                <div class="form-group">
                    <label for="numero_candidato">Número do Candidato (opcional):</label>
                    <input type="number" id="numero_candidato" name="numero_candidato" value="<?php echo htmlspecialchars($dados_formulario_candidato['numero_candidato'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="nome_urna">Nome na Urna (opcional):</label>
                    <input type="text" id="nome_urna" name="nome_urna" value="<?php echo htmlspecialchars($dados_formulario_candidato['nome_urna'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="plataforma_propostas">Plataforma/Propostas (opcional):</label>
                    <textarea id="plataforma_propostas" name="plataforma_propostas"><?php echo htmlspecialchars($dados_formulario_candidato['plataforma_propostas'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="button">Inscrever Candidato</button>
            </form>
        </div>
    </div>
</body>
</html>

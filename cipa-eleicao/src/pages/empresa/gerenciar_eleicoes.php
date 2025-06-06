<?php
require_once '../../scripts/auth_empresa.php'; // Garante autenticação e inicia sessão
require_once '../../scripts/db_connection.php'; // Conexão com o banco

$empresa_id = $_SESSION['empresa_id'];
$eleicoes = [];

try {
    // Buscar eleições da empresa logada. Inclui eleições diretamente ligadas à empresa
    // ou a qualquer uma de suas filiais.
    // TODO: Quando o cadastro de filiais estiver pronto, refinar a consulta para buscar nome da filial.
    $stmt = $pdo->prepare(
        "SELECT e.id, e.titulo_eleicao, e.ano_referencia, e.status_eleicao, e.data_convocacao,
                f.nome_fantasia AS nome_filial
         FROM eleicoes e
         LEFT JOIN filiais f ON e.filial_id = f.id
         WHERE e.empresa_id = :empresa_id
         ORDER BY e.data_criacao DESC"
    );
    $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
    $stmt->execute();
    $eleicoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ao buscar eleições: " . $e->getMessage());
    // Pode definir uma mensagem de erro para exibir na página, se desejar
    $erro_db = "Não foi possível carregar as eleições. Tente novamente mais tarde.";
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Eleições - Painel da Empresa</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .mensagem { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .sucesso { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .erro { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .acoes a { margin-right: 5px; text-decoration: none; }
    </style>
</head>
<body>
    <h2>Gerenciar Eleições</h2>
    <p><a href="painel_empresa.php">Voltar ao Painel</a> | <a href="criar_eleicao.php">Criar Nova Eleição</a></p>

    <?php
    if (isset($_SESSION['mensagem_sucesso'])) {
        echo "<div class='mensagem sucesso'>" . htmlspecialchars($_SESSION['mensagem_sucesso']) . "</div>";
        unset($_SESSION['mensagem_sucesso']);
    }
    if (isset($erro_db)) {
        echo "<div class='mensagem erro'>" . htmlspecialchars($erro_db) . "</div>";
    }
    ?>

    <?php if (empty($eleicoes) && !isset($erro_db)): ?>
        <p>Nenhuma eleição encontrada para esta empresa.</p>
    <?php elseif (!empty($eleicoes)): ?>
        <table>
            <thead>
                <tr>
                    <th>Título da Eleição</th>
                    <th>Filial</th>
                    <th>Ano Referência</th>
                    <th>Status</th>
                    <th>Data de Convocação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($eleicoes as $eleicao): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($eleicao['titulo_eleicao']); ?></td>
                        <td><?php echo htmlspecialchars($eleicao['nome_filial'] ?? 'N/A (Empresa Principal)'); ?></td>
                        <td><?php echo htmlspecialchars($eleicao['ano_referencia']); ?></td>
                        <td><?php echo htmlspecialchars($eleicao['status_eleicao']); ?></td>
                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($eleicao['data_convocacao']))); ?></td>
                        <td class="acoes">
                            <a href="ver_detalhes_eleicao.php?id=<?php echo $eleicao['id']; ?>">Ver Detalhes</a>
                            <a href="editar_eleicao.php?id=<?php echo $eleicao['id']; ?>">Editar</a>
                            <a href="gerenciar_comissao_eleitoral.php?eleicao_id=<?php echo $eleicao['id']; ?>">Gerenciar Comissão</a>
                            <!-- Adicionar mais ações conforme necessário -->
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</body>
</html>

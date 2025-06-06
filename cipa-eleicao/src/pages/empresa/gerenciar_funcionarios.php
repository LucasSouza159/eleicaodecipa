<?php
require_once '../../scripts/auth_empresa.php';
require_once '../../scripts/db_connection.php';

$empresa_id = $_SESSION['empresa_id'];
$funcionarios = [];

try {
    $stmt = $pdo->prepare(
        "SELECT id, nome_completo, cpf, email, matricula, data_admissao, status_funcionario
         FROM funcionarios
         WHERE empresa_id = :empresa_id
         ORDER BY nome_completo ASC"
    );
    $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
    $stmt->execute();
    $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ao buscar funcionários: " . $e->getMessage());
    $erro_db = "Não foi possível carregar os funcionários. Tente novamente mais tarde.";
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Funcionários - Painel da Empresa</title>
    <style>
        body { font-family: sans-serif; }
        .container { width: 95%; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 0.9em; }
        th { background-color: #f2f2f2; }
        .mensagem { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .sucesso { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .erro { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .acoes a { margin-right: 5px; text-decoration: none; padding: 3px 6px; border-radius:3px; font-size:0.85em;}
        .acoes a.editar { background-color: #ffc107; color:black;}
        .acoes a.detalhes { background-color: #17a2b8; color:white;}
        .button-group { margin-bottom: 15px;}
        .button-group a, .button-group button { text-decoration:none; padding: 8px 12px; border-radius:4px; border: 1px solid #ccc; margin-right:5px; background-color:#007bff; color:white;}
        .button-group button.import { background-color: #28a745;}
    </style>
</head>
<body>
    <div class="container">
        <h2>Gerenciar Funcionários</h2>
        <p><a href="painel_empresa.php">Voltar ao Painel</a></p>

        <div class="button-group">
            <a href="cadastrar_funcionario.php">Cadastrar Novo Funcionário</a>
            <button type="button" class="import" onclick="alert('Funcionalidade de Importar CSV será implementada em breve.');">Importar CSV</button>
        </div>

        <?php
        if (isset($_SESSION['mensagem_sucesso'])) {
            echo "<div class='mensagem sucesso'>" . htmlspecialchars($_SESSION['mensagem_sucesso']) . "</div>";
            unset($_SESSION['mensagem_sucesso']);
        }
        if (isset($_SESSION['mensagem_erro'])) {
            echo "<div class='mensagem erro'>" . htmlspecialchars($_SESSION['mensagem_erro']) . "</div>";
            unset($_SESSION['mensagem_erro']);
        }
        if (isset($erro_db)) {
            echo "<div class='mensagem erro'>" . htmlspecialchars($erro_db) . "</div>";
        }
        ?>

        <?php if (empty($funcionarios) && !isset($erro_db)): ?>
            <p>Nenhum funcionário cadastrado para esta empresa.</p>
        <?php elseif (!empty($funcionarios)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nome Completo</th>
                        <th>CPF</th>
                        <th>Email</th>
                        <th>Matrícula</th>
                        <th>Data de Admissão</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($funcionarios as $funcionario): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($funcionario['nome_completo']); ?></td>
                            <td><?php echo htmlspecialchars($funcionario['cpf']); ?></td>
                            <td><?php echo htmlspecialchars($funcionario['email'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($funcionario['matricula'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($funcionario['data_admissao']))); ?></td>
                            <td><?php echo htmlspecialchars($funcionario['status_funcionario']); ?></td>
                            <td class="acoes">
                                <a href="editar_funcionario.php?id=<?php echo $funcionario['id']; ?>" class="editar">Editar</a>
                                <!-- <a href="ver_detalhes_funcionario.php?id=<?php echo $funcionario['id']; ?>" class="detalhes">Detalhes</a> -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>

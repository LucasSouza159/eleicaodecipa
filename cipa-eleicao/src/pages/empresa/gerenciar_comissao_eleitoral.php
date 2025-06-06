<?php
require_once '../../scripts/auth_empresa.php';
require_once '../../scripts/db_connection.php';

$empresa_id_logada = $_SESSION['empresa_id'];
$eleicao_id = filter_input(INPUT_GET, 'eleicao_id', FILTER_VALIDATE_INT);
$eleicao = null;
$membros_comissao = [];

if (!$eleicao_id) {
    $_SESSION['mensagem_erro'] = "ID da eleição inválido.";
    header("Location: gerenciar_eleicoes.php");
    exit();
}

// Validar se a eleição pertence à empresa logada e buscar dados da eleição
try {
    $stmt = $pdo->prepare("SELECT id, titulo_eleicao, ano_referencia FROM eleicoes WHERE id = ? AND empresa_id = ?");
    $stmt->execute([$eleicao_id, $empresa_id_logada]);
    $eleicao = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$eleicao) {
        $_SESSION['mensagem_erro'] = "Eleição não encontrada ou não pertence à sua empresa.";
        header("Location: gerenciar_eleicoes.php");
        exit();
    }

    // Buscar membros da comissão para esta eleição
    $stmt_membros = $pdo->prepare("SELECT id, nome_completo, email, cpf, papel_comissao, senha_hash_comissao FROM comissao WHERE eleicao_id = ? ORDER BY nome_completo ASC");
    $stmt_membros->execute([$eleicao_id]);
    $membros_comissao = $stmt_membros->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erro ao buscar dados da eleição/comissão: " . $e->getMessage());
    $_SESSION['mensagem_erro'] = "Erro ao carregar dados. Tente novamente.";
    header("Location: gerenciar_eleicoes.php");
    exit();
}

// Recuperar dados do formulário e erros da sessão, se existirem (para repopular)
$erros_designar = $_SESSION['erros_designar_membro'] ?? [];
$dados_formulario_membro = $_SESSION['dados_formulario_membro'] ?? [];
unset($_SESSION['erros_designar_membro'], $_SESSION['dados_formulario_membro']);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Comissão Eleitoral - <?php echo htmlspecialchars($eleicao['titulo_eleicao'] ?? 'Eleição'); ?></title>
    <style>
        body { font-family: sans-serif; }
        .container { width: 90%; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .mensagem { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .sucesso { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .erro { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .form-designar { margin-top: 30px; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        .form-designar h3 { margin-top: 0; }
        .form-group { margin-bottom: 10px; }
        .form-group label { display: block; margin-bottom: 3px; }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group select { width: 100%; padding: 8px; box-sizing: border-box; }
        .erro-lista { list-style-type: none; padding: 0; margin: 0 0 10px 0; }
        .erro-lista li { color: #721c24; }
        .acoes a { margin-right: 5px; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Gerenciar Comissão Eleitoral</h2>
        <p><a href="gerenciar_eleicoes.php">Voltar para Gerenciar Eleições</a></p>

        <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
            <div class="mensagem sucesso"><?php echo htmlspecialchars($_SESSION['mensagem_sucesso']); unset($_SESSION['mensagem_sucesso']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['mensagem_erro'])): ?>
            <div class="mensagem erro"><?php echo htmlspecialchars($_SESSION['mensagem_erro']); unset($_SESSION['mensagem_erro']); ?></div>
        <?php endif; ?>

        <h3>Eleição: <?php echo htmlspecialchars($eleicao['titulo_eleicao']); ?> (<?php echo htmlspecialchars($eleicao['ano_referencia']); ?>)</h3>

        <h4>Membros da Comissão Designados</h4>
        <?php if (empty($membros_comissao)): ?>
            <p>Nenhum membro designado para esta comissão eleitoral ainda.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Nome Completo</th>
                        <th>Email</th>
                        <th>CPF</th>
                        <th>Papel</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($membros_comissao as $membro): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($membro['nome_completo']); ?></td>
                            <td><?php echo htmlspecialchars($membro['email']); ?></td>
                            <td><?php echo htmlspecialchars($membro['cpf']); ?></td>
                            <td><?php echo htmlspecialchars($membro['papel_comissao']); ?></td>
                            <td class="acoes">
                            <?php if (is_null($membro['senha_hash_comissao'])): ?>
                                <a href="definir_senha_comissao.php?membro_id=<?php echo $membro['id']; ?>&eleicao_id=<?php echo $eleicao_id; ?>" class="definir-senha">Definir Senha</a>
                            <?php else: ?>
                                <span class="senha-definida">Senha Definida</span>
                                <!-- Opcional: Link para redefinir
                                <a href="definir_senha_comissao.php?membro_id=<?php echo $membro['id']; ?>&eleicao_id=<?php echo $eleicao_id; ?>" class="redefinir-senha">Redefinir Senha</a>
                                -->
                            <?php endif; ?>
                            <a href="remover_membro_comissao.php?membro_id=<?php echo $membro['id']; ?>&eleicao_id=<?php echo $eleicao_id; ?>" onclick="return confirm('Tem certeza que deseja remover este membro da comissão?');" class="remover">Remover</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="form-designar">
            <h3>Designar Novo Membro para Comissão</h3>
            <?php if (!empty($erros_designar)): ?>
                <ul class="erro-lista">
                    <?php foreach ($erros_designar as $erro): ?>
                        <li><?php echo htmlspecialchars($erro); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <form action="processa_designar_membro.php" method="POST">
                <input type="hidden" name="eleicao_id" value="<?php echo $eleicao_id; ?>">

                <div class="form-group">
                    <label for="nome_completo">Nome Completo:</label>
                    <input type="text" id="nome_completo" name="nome_completo" value="<?php echo htmlspecialchars($dados_formulario_membro['nome_completo'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($dados_formulario_membro['email'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="cpf">CPF (XXX.XXX.XXX-XX):</label>
                    <input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($dados_formulario_membro['cpf'] ?? ''); ?>" required> <!-- TODO: Adicionar máscara -->
                </div>
                <div class="form-group">
                    <label for="papel_comissao">Papel na Comissão:</label>
                    <select id="papel_comissao" name="papel_comissao" required>
                        <option value="">Selecione o papel</option>
                        <option value="Presidente" <?php echo (isset($dados_formulario_membro['papel_comissao']) && $dados_formulario_membro['papel_comissao'] == 'Presidente') ? 'selected' : ''; ?>>Presidente</option>
                        <option value="Secretário" <?php echo (isset($dados_formulario_membro['papel_comissao']) && $dados_formulario_membro['papel_comissao'] == 'Secretário') ? 'selected' : ''; ?>>Secretário</option>
                        <option value="Membro" <?php echo (isset($dados_formulario_membro['papel_comissao']) && $dados_formulario_membro['papel_comissao'] == 'Membro') ? 'selected' : ''; ?>>Membro</option>
                    </select>
                </div>
                <button type="submit">Designar Membro</button>
            </form>
        </div>
    </div>
</body>
</html>

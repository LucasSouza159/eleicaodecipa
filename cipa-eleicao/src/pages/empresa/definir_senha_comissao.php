<?php
require_once '../../scripts/auth_empresa.php';
require_once '../../scripts/db_connection.php';

$empresa_id_logada = $_SESSION['empresa_id'];
$membro_id = filter_input(INPUT_GET, 'membro_id', FILTER_VALIDATE_INT);
$eleicao_id = filter_input(INPUT_GET, 'eleicao_id', FILTER_VALIDATE_INT);

$membro_comissao = null;

if (!$membro_id || !$eleicao_id) {
    $_SESSION['mensagem_erro'] = "IDs inválidos para definir senha.";
    header("Location: gerenciar_eleicoes.php"); // Volta para a lista geral de eleições se algo estiver muito errado
    exit();
}

// Validar se a eleição (e o membro) pertencem à empresa logada
try {
    $stmt = $pdo->prepare(
        "SELECT c.id, c.nome_completo, c.email, e.id AS id_eleicao
         FROM comissao c
         JOIN eleicoes e ON c.eleicao_id = e.id
         WHERE c.id = :membro_id AND e.id = :eleicao_id AND e.empresa_id = :empresa_id"
    );
    $stmt->bindParam(':membro_id', $membro_id, PDO::PARAM_INT);
    $stmt->bindParam(':eleicao_id', $eleicao_id, PDO::PARAM_INT);
    $stmt->bindParam(':empresa_id', $empresa_id_logada, PDO::PARAM_INT);
    $stmt->execute();
    $membro_comissao = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$membro_comissao) {
        $_SESSION['mensagem_erro'] = "Membro da comissão não encontrado ou não pertence a uma eleição válida da sua empresa.";
        // Redirecionar para a gerência da comissão específica se o eleicao_id for conhecido e válido, senão para a lista geral
        if ($eleicao_id_valido_anteriormente = filter_var($_GET['eleicao_id'] ?? null, FILTER_VALIDATE_INT)) {
             header("Location: gerenciar_comissao_eleitoral.php?eleicao_id=" . $eleicao_id_valido_anteriormente);
        } else {
            header("Location: gerenciar_eleicoes.php");
        }
        exit();
    }
} catch (PDOException $e) {
    error_log("Erro ao buscar membro da comissão para definir senha: " . $e->getMessage());
    $_SESSION['mensagem_erro'] = "Erro ao carregar dados do membro da comissão. Tente novamente.";
    header("Location: gerenciar_comissao_eleitoral.php?eleicao_id=" . $eleicao_id);
    exit();
}

$erros_definir_senha = $_SESSION['erros_definir_senha_comissao'] ?? [];
unset($_SESSION['erros_definir_senha_comissao']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Definir Senha para Membro da Comissão</title>
    <style>
        body { font-family: sans-serif; }
        .container { width: 50%; margin: auto; padding:20px; border:1px solid #ccc; border-radius:5px; margin-top:20px;}
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight:bold; }
        .form-group input[type="password"] { width: 100%; padding: 10px; box-sizing: border-box; border:1px solid #ccc; border-radius:4px; }
        .erro-lista { list-style-type: none; padding: 0; margin: 0 0 15px 0; }
        .erro-lista li { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 5px; border-radius: 5px; }
        .button { padding:10px 15px; background-color:#007bff; color:white; border:none; border-radius:4px; cursor:pointer; }
        .button:hover { background-color:#0056b3; }
        a { color: #007bff; text-decoration:none;}
        .info-membro p { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Definir Senha para Membro da Comissão</h2>
        <p><a href="gerenciar_comissao_eleitoral.php?eleicao_id=<?php echo $eleicao_id; ?>">Voltar para Gerenciar Comissão</a></p>

        <div class="info-membro">
            <p><strong>Nome:</strong> <?php echo htmlspecialchars($membro_comissao['nome_completo']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($membro_comissao['email']); ?></p>
        </div>
        <hr>

        <?php if (!empty($erros_definir_senha)): ?>
            <ul class="erro-lista">
                <?php foreach ($erros_definir_senha as $erro): ?>
                    <li><?php echo htmlspecialchars($erro); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form action="processa_definir_senha_comissao.php" method="POST">
            <input type="hidden" name="membro_id" value="<?php echo $membro_id; ?>">
            <input type="hidden" name="eleicao_id" value="<?php echo $eleicao_id; ?>">

            <div class="form-group">
                <label for="nova_senha">Nova Senha:</label>
                <input type="password" id="nova_senha" name="nova_senha" required>
            </div>

            <div class="form-group">
                <label for="confirma_nova_senha">Confirmar Nova Senha:</label>
                <input type="password" id="confirma_nova_senha" name="confirma_nova_senha" required>
            </div>

            <button type="submit" class="button">Definir Senha</button>
        </form>
    </div>
</body>
</html>

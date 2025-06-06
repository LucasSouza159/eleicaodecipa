<?php
require_once '../../scripts/auth_empresa.php';
require_once '../../scripts/db_connection.php';

$empresa_id = $_SESSION['empresa_id'];

// Recuperar dados do formulário e erros da sessão, se existirem
$erros_cadastro_funcionario = $_SESSION['erros_cadastro_funcionario'] ?? [];
$dados_formulario_funcionario = $_SESSION['dados_formulario_funcionario'] ?? [];

unset($_SESSION['erros_cadastro_funcionario']);
unset($_SESSION['dados_formulario_funcionario']);

// TODO: Popular dinamicamente o dropdown de filiais
// $filiais = [];
// try {
//     $stmt_filiais = $pdo->prepare("SELECT id, nome_fantasia FROM filiais WHERE empresa_id = ? ORDER BY nome_fantasia ASC");
//     $stmt_filiais->execute([$empresa_id]);
//     $filiais = $stmt_filiais->fetchAll(PDO::FETCH_ASSOC);
// } catch (PDOException $e) {
//     error_log("Erro ao buscar filiais: " . $e->getMessage());
//     $erros_cadastro_funcionario[] = "Erro ao carregar lista de filiais.";
// }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Novo Funcionário - Painel da Empresa</title>
    <style>
        body { font-family: sans-serif; }
        .container { width: 80%; margin: auto; padding:20px; border:1px solid #ccc; border-radius:5px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight:bold; }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="date"],
        .form-group select { width: 100%; padding: 10px; box-sizing: border-box; border:1px solid #ccc; border-radius:4px; }
        .form-group input[type="checkbox"] { margin-right: 5px; }
        .erro-lista { list-style-type: none; padding: 0; margin: 0 0 15px 0; }
        .erro-lista li { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 5px; border-radius: 5px; }
        .button { padding:10px 15px; background-color:#007bff; color:white; border:none; border-radius:4px; cursor:pointer; }
        .button:hover { background-color:#0056b3; }
        a { color: #007bff; text-decoration:none;}
    </style>
</head>
<body>
    <div class="container">
        <h2>Cadastrar Novo Funcionário</h2>
        <p><a href="gerenciar_funcionarios.php">Voltar para Gerenciar Funcionários</a></p>

        <?php if (!empty($erros_cadastro_funcionario)): ?>
            <ul class="erro-lista">
                <?php foreach ($erros_cadastro_funcionario as $erro): ?>
                    <li><?php echo htmlspecialchars($erro); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form action="processa_cadastrar_funcionario.php" method="POST">
            <div class="form-group">
                <label for="nome_completo">Nome Completo:</label>
                <input type="text" id="nome_completo" name="nome_completo" value="<?php echo htmlspecialchars($dados_formulario_funcionario['nome_completo'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="cpf">CPF (XXX.XXX.XXX-XX):</label>
                <input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($dados_formulario_funcionario['cpf'] ?? ''); ?>" required> <!-- TODO: Adicionar máscara de CPF -->
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($dados_formulario_funcionario['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="matricula">Matrícula:</label>
                <input type="text" id="matricula" name="matricula" value="<?php echo htmlspecialchars($dados_formulario_funcionario['matricula'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="data_admissao">Data de Admissão:</label>
                <input type="date" id="data_admissao" name="data_admissao" value="<?php echo htmlspecialchars($dados_formulario_funcionario['data_admissao'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="cargo">Cargo:</label>
                <input type="text" id="cargo" name="cargo" value="<?php echo htmlspecialchars($dados_formulario_funcionario['cargo'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="departamento">Departamento:</label>
                <input type="text" id="departamento" name="departamento" value="<?php echo htmlspecialchars($dados_formulario_funcionario['departamento'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="filial_id">Filial (ID):</label>
                <!-- TODO: Substituir por dropdown dinâmico quando o cadastro de filiais estiver pronto -->
                <input type="text" id="filial_id" name="filial_id" placeholder="ID da Filial (se aplicável)" value="<?php echo htmlspecialchars($dados_formulario_funcionario['filial_id'] ?? ''); ?>">
                <?php /*
                <select id="filial_id" name="filial_id">
                    <option value="">Selecione uma filial (se aplicável)</option>
                    <?php foreach ($filiais as $filial): ?>
                        <option value="<?php echo $filial['id']; ?>" <?php echo (isset($dados_formulario_funcionario['filial_id']) && $dados_formulario_funcionario['filial_id'] == $filial['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($filial['nome_fantasia']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                */ ?>
            </div>

            <div class="form-group">
                <label for="status_funcionario">Status do Funcionário:</label>
                <select id="status_funcionario" name="status_funcionario" required>
                    <option value="Ativo" <?php echo (isset($dados_formulario_funcionario['status_funcionario']) && $dados_formulario_funcionario['status_funcionario'] == 'Ativo') ? 'selected' : ''; ?>>Ativo</option>
                    <option value="Inativo" <?php echo (isset($dados_formulario_funcionario['status_funcionario']) && $dados_formulario_funcionario['status_funcionario'] == 'Inativo') ? 'selected' : ''; ?>>Inativo</option>
                    <option value="Demitido" <?php echo (isset($dados_formulario_funcionario['status_funcionario']) && $dados_formulario_funcionario['status_funcionario'] == 'Demitido') ? 'selected' : ''; ?>>Demitido</option>
                    <option value="Afastado" <?php echo (isset($dados_formulario_funcionario['status_funcionario']) && $dados_formulario_funcionario['status_funcionario'] == 'Afastado') ? 'selected' : ''; ?>>Afastado</option>
                </select>
            </div>

            <div class="form-group">
                <label for="permite_votar">
                    <input type="checkbox" id="permite_votar" name="permite_votar" value="1" <?php echo (!isset($dados_formulario_funcionario['permite_votar']) || !empty($dados_formulario_funcionario['permite_votar'])) ? 'checked' : ''; ?>>
                    Permite Votar
                </label>
            </div>

            <button type="submit" class="button">Cadastrar Funcionário</button>
        </form>
    </div>
</body>
</html>

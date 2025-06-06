<?php
require_once '../../scripts/auth_comissao.php'; // Garante autenticação e define $comissao_eleicao_id_logado
require_once '../../scripts/db_connection.php';

$eleicao_id_comissao = $comissao_eleicao_id_logado;
$candidato_id = filter_input(INPUT_GET, 'candidato_id', FILTER_VALIDATE_INT);
$candidatura = null;
$funcionario_info = null;

if (!$candidato_id) {
    $_SESSION['mensagem_erro_candidato'] = "ID da candidatura inválido."; // Usar uma chave de sessão diferente para evitar conflitos
    header("Location: gerenciar_candidatos.php");
    exit();
}

// Recuperar dados do formulário e erros da sessão, se existirem
$erros_editar_candidatura = $_SESSION['erros_editar_candidatura'] ?? [];
$dados_formulario_edicao_candidato = $_SESSION['dados_formulario_edicao_candidato'] ?? [];
unset($_SESSION['erros_editar_candidatura'], $_SESSION['dados_formulario_edicao_candidato']);


// Buscar dados da candidatura e validar se pertence à eleição da comissão logada
try {
    $stmt = $pdo->prepare(
        "SELECT c.*, f.nome_completo AS funcionario_nome
         FROM candidatos c
         JOIN funcionarios f ON c.funcionario_id = f.id
         WHERE c.id = ? AND c.eleicao_id = ?"
    );
    $stmt->execute([$candidato_id, $eleicao_id_comissao]);
    $candidatura = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$candidatura) {
        $_SESSION['mensagem_erro_candidato'] = "Candidatura não encontrada ou não pertence à sua eleição.";
        header("Location: gerenciar_candidatos.php");
        exit();
    }
    // Se não houver dados de formulário de uma tentativa anterior, preenche com dados do banco
    if (empty($dados_formulario_edicao_candidato)) {
        $dados_formulario_edicao_candidato = $candidatura;
    }
    $funcionario_info = ['nome_completo' => $candidatura['funcionario_nome']];


} catch (PDOException $e) {
    error_log("Erro ao buscar candidatura para edição: " . $e->getMessage());
    $_SESSION['mensagem_erro_candidato'] = "Erro ao carregar dados da candidatura.";
    header("Location: gerenciar_candidatos.php");
    exit();
}

$status_candidatura_permitidos = ['Inscrito', 'Aprovado', 'Reprovado', 'Eleito', 'Suplente', 'Não Eleito'];

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Candidatura - Comissão</title>
    <style>
        body { font-family: sans-serif; margin: 0; background-color: #f8f9fa; }
        .container { width: 70%; margin: 20px auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2, h3 { color: #333; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group textarea,
        .form-group select { width: 100%; padding: 10px; box-sizing: border-box; border:1px solid #ced4da; border-radius:4px; }
        .form-group textarea { min-height: 100px; }
        .button { padding:10px 15px; background-color:#007bff; color:white; border:none; border-radius:4px; cursor:pointer; font-size: 1em; }
        .button:hover { background-color:#0056b3; }
        .erro-lista { list-style-type: none; padding: 0; margin: 0 0 15px 0; color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius:4px; }
        .erro-lista li { padding: 10px; }
        .nav-link { color: #007bff; text-decoration:none; margin-bottom:15px; display:inline-block;}
        .info-candidato p { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Candidatura</h2>
        <a href="gerenciar_candidatos.php" class="nav-link">Voltar para Gerenciar Candidatos</a>

        <?php if ($funcionario_info): ?>
            <div class="info-candidato">
                <h3>Candidato: <?php echo htmlspecialchars($funcionario_info['nome_completo']); ?></h3>
            </div>
        <?php endif; ?>

        <?php if (!empty($erros_editar_candidatura)): ?>
            <ul class="erro-lista">
                <?php foreach ($erros_editar_candidatura as $erro): ?>
                    <li><?php echo htmlspecialchars($erro); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form action="processa_editar_candidatura.php" method="POST">
            <input type="hidden" name="candidato_id" value="<?php echo $candidato_id; ?>">

            <div class="form-group">
                <label for="numero_candidato">Número do Candidato (opcional):</label>
                <input type="number" id="numero_candidato" name="numero_candidato" value="<?php echo htmlspecialchars($dados_formulario_edicao_candidato['numero_candidato'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="nome_urna">Nome na Urna (opcional):</label>
                <input type="text" id="nome_urna" name="nome_urna" value="<?php echo htmlspecialchars($dados_formulario_edicao_candidato['nome_urna'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="plataforma_propostas">Plataforma/Propostas (opcional):</label>
                <textarea id="plataforma_propostas" name="plataforma_propostas"><?php echo htmlspecialchars($dados_formulario_edicao_candidato['plataforma_propostas'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="status_candidatura">Status da Candidatura:</label>
                <select id="status_candidatura" name="status_candidatura" required>
                    <?php foreach ($status_candidatura_permitidos as $status): ?>
                        <option value="<?php echo $status; ?>" <?php echo (isset($dados_formulario_edicao_candidato['status_candidatura']) && $dados_formulario_edicao_candidato['status_candidatura'] == $status) ? 'selected' : ''; ?>>
                            <?php echo $status; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="button">Salvar Alterações</button>
        </form>
    </div>
</body>
</html>

<?php
require_once '../../scripts/auth_empresa.php';
require_once '../../scripts/db_connection.php';

$empresa_id = $_SESSION['empresa_id'];

// Recuperar dados do formulário e erros da sessão, se existirem
$erros = $_SESSION['erros_criar_eleicao'] ?? [];
$dados_formulario = $_SESSION['dados_formulario_eleicao'] ?? [];

unset($_SESSION['erros_criar_eleicao']);
unset($_SESSION['dados_formulario_eleicao']);

// TODO: Popular dinamicamente o dropdown de filiais
// $filiais = [];
// try {
//     $stmt = $pdo->prepare("SELECT id, nome_fantasia FROM filiais WHERE empresa_id = ? ORDER BY nome_fantasia ASC");
//     $stmt->execute([$empresa_id]);
//     $filiais = $stmt->fetchAll(PDO::FETCH_ASSOC);
// } catch (PDOException $e) {
//     error_log("Erro ao buscar filiais: " . $e->getMessage());
//     $erros[] = "Erro ao carregar lista de filiais.";
// }

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Nova Eleição - Painel da Empresa</title>
    <style>
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="date"],
        .form-group input[type="datetime-local"],
        .form-group textarea,
        .form-group select { width: 100%; padding: 8px; box-sizing: border-box; }
        .erro-lista { list-style-type: none; padding: 0; margin: 0 0 15px 0; }
        .erro-lista li { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 5px; border-radius: 5px; }
        .mensagem { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .erro_db { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <h2>Criar Nova Eleição</h2>
    <p><a href="gerenciar_eleicoes.php">Voltar para Gerenciar Eleições</a></p>

    <?php if (!empty($erros)): ?>
        <ul class="erro-lista">
            <?php foreach ($erros as $erro): ?>
                <li><?php echo htmlspecialchars($erro); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php
    if (isset($_SESSION['mensagem_erro_db'])) {
        echo "<div class='mensagem erro_db'>" . htmlspecialchars($_SESSION['mensagem_erro_db']) . "</div>";
        unset($_SESSION['mensagem_erro_db']);
    }
    ?>

    <form action="processa_criar_eleicao.php" method="POST">
        <div class="form-group">
            <label for="titulo_eleicao">Título da Eleição:</label>
            <input type="text" id="titulo_eleicao" name="titulo_eleicao" value="<?php echo htmlspecialchars($dados_formulario['titulo_eleicao'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao"><?php echo htmlspecialchars($dados_formulario['descricao'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="ano_referencia">Ano de Referência (YYYY):</label>
            <input type="number" id="ano_referencia" name="ano_referencia" value="<?php echo htmlspecialchars($dados_formulario['ano_referencia'] ?? date('Y')); ?>" required min="2000" max="2100">
        </div>

        <div class="form-group">
            <label for="filial_id">Filial (Opcional):</label>
            <!-- TODO: Substituir por dropdown dinâmico quando o cadastro de filiais estiver pronto -->
            <input type="text" id="filial_id" name="filial_id" placeholder="ID da Filial (se aplicável)" value="<?php echo htmlspecialchars($dados_formulario['filial_id'] ?? ''); ?>">
            <!-- Exemplo de dropdown estático/comentado:
            <select id="filial_id" name="filial_id">
                <option value="">Selecione uma filial (se aplicável)</option>
                <?php // foreach ($filiais as $filial): ?>
                    <option value="<?php // echo $filial['id']; ?>" <?php // echo (isset($dados_formulario['filial_id']) && $dados_formulario['filial_id'] == $filial['id']) ? 'selected' : ''; ?>>
                        <?php // echo htmlspecialchars($filial['nome_fantasia']); ?>
                    </option>
                <?php // endforeach; ?>
            </select>
            -->
        </div>

        <div class="form-group">
            <label for="data_convocacao">Data de Convocação:</label>
            <input type="date" id="data_convocacao" name="data_convocacao" value="<?php echo htmlspecialchars($dados_formulario['data_convocacao'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="data_inicio_inscricao_candidatos">Início das Inscrições:</label>
            <input type="datetime-local" id="data_inicio_inscricao_candidatos" name="data_inicio_inscricao_candidatos" value="<?php echo htmlspecialchars($dados_formulario['data_inicio_inscricao_candidatos'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="data_fim_inscricao_candidatos">Fim das Inscrições:</label>
            <input type="datetime-local" id="data_fim_inscricao_candidatos" name="data_fim_inscricao_candidatos" value="<?php echo htmlspecialchars($dados_formulario['data_fim_inscricao_candidatos'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="data_inicio_votacao">Início da Votação:</label>
            <input type="datetime-local" id="data_inicio_votacao" name="data_inicio_votacao" value="<?php echo htmlspecialchars($dados_formulario['data_inicio_votacao'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="data_fim_votacao">Fim da Votação:</label>
            <input type="datetime-local" id="data_fim_votacao" name="data_fim_votacao" value="<?php echo htmlspecialchars($dados_formulario['data_fim_votacao'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="data_apuracao">Data da Apuração:</label>
            <input type="datetime-local" id="data_apuracao" name="data_apuracao" value="<?php echo htmlspecialchars($dados_formulario['data_apuracao'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="data_posse_eleitos">Data da Posse dos Eleitos:</label>
            <input type="date" id="data_posse_eleitos" name="data_posse_eleitos" value="<?php echo htmlspecialchars($dados_formulario['data_posse_eleitos'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="observacoes_gerais">Observações Gerais:</label>
            <textarea id="observacoes_gerais" name="observacoes_gerais"><?php echo htmlspecialchars($dados_formulario['observacoes_gerais'] ?? ''); ?></textarea>
        </div>

        <button type="submit">Salvar Eleição</button>
    </form>

</body>
</html>

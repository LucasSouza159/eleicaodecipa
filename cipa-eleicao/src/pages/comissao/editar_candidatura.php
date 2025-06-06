<?php
require_once '../../scripts/auth_comissao.php';
require_once '../../scripts/db_connection.php';

$eleicao_id_comissao = $comissao_eleicao_id_logado;
$candidato_id = filter_input(INPUT_GET, 'candidato_id', FILTER_VALIDATE_INT);
$candidatura = null;
$funcionario_info = null;

if (!$candidato_id) {
    $_SESSION['mensagem_erro_candidato'] = "ID da candidatura inválido.";
    header("Location: gerenciar_candidatos.php");
    exit();
}

$erros_editar_candidatura = $_SESSION['erros_editar_candidatura'] ?? [];
$dados_formulario_edicao_candidato = $_SESSION['dados_formulario_edicao_candidato'] ?? [];
unset($_SESSION['erros_editar_candidatura'], $_SESSION['dados_formulario_edicao_candidato']);

try {
    $stmt = $pdo->prepare(
        "SELECT c.*, f.nome_completo AS funcionario_nome
         FROM candidatos c
         JOIN funcionarios f ON c.funcionario_id = f.id
         WHERE c.id = ? AND c.eleicao_id = ?"
    );
    $stmt->execute([$candidato_id, $eleicao_id_comissao]);
    $candidatura_db = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$candidatura_db) {
        $_SESSION['mensagem_erro_candidato'] = "Candidatura não encontrada ou não pertence à sua eleição.";
        header("Location: gerenciar_candidatos.php");
        exit();
    }
    if (empty($dados_formulario_edicao_candidato)) { // Só preenche com DB se não houver dados de tentativa anterior
        $dados_formulario_edicao_candidato = $candidatura_db;
    }
    $funcionario_info = ['nome_completo' => $candidatura_db['funcionario_nome']]; // Pega nome do DB sempre

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
    <title>Editar Candidatura - CIPA Fácil</title>
    <link href="../../src/styles/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

    <nav class="bg-azul-cipa text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="text-xl font-bold">
                    Comissão: <?php echo htmlspecialchars($comissao_eleicao_titulo_logado); ?>
                </div>
                 <div>
                    <span class="text-sm mr-4"><?php echo htmlspecialchars($comissao_nome_membro_logado . " - " . $comissao_papel_logado); ?></span>
                    <a href="logout_comissao.php" class="px-4 py-2 bg-red-500 hover:bg-red-600 rounded-md text-sm font-medium transition-colors">Sair</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto p-6 mt-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-cinza-chumbo">Editar Candidatura</h2>
            <a href="gerenciar_candidatos.php" class="text-azul-cipa hover:text-blue-700">&larr; Voltar para Gerenciar Candidatos</a>
        </div>

        <div class="bg-white p-8 rounded-lg shadow-lg">
            <?php if ($funcionario_info): ?>
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
                    <h3 class="text-lg font-semibold text-azul-cipa">Candidato: <?php echo htmlspecialchars($funcionario_info['nome_completo']); ?></h3>
                    <p class="text-sm text-gray-600">Eleição: <?php echo htmlspecialchars($comissao_eleicao_titulo_logado); ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($erros_editar_candidatura)): ?>
                <div class="p-4 mb-6 text-sm text-red-700 bg-red-100 border border-red-400 rounded-lg" role="alert">
                    <p class="font-bold">Foram encontrados os seguintes erros:</p>
                    <ul class="list-disc pl-5 mt-2">
                        <?php foreach ($erros_editar_candidatura as $erro): ?>
                            <li><?php echo htmlspecialchars($erro); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="processa_editar_candidatura.php" method="POST" class="space-y-6">
                <input type="hidden" name="candidato_id" value="<?php echo $candidato_id; ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="numero_candidato" class="block text-sm font-medium text-gray-700">Número do Candidato (opcional):</label>
                        <input type="number" id="numero_candidato" name="numero_candidato"
                               value="<?php echo htmlspecialchars($dados_formulario_edicao_candidato['numero_candidato'] ?? ''); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-azul-cipa focus:border-azul-cipa">
                    </div>
                    <div>
                        <label for="nome_urna" class="block text-sm font-medium text-gray-700">Nome na Urna (opcional):</label>
                        <input type="text" id="nome_urna" name="nome_urna"
                               value="<?php echo htmlspecialchars($dados_formulario_edicao_candidato['nome_urna'] ?? ''); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-azul-cipa focus:border-azul-cipa">
                    </div>
                </div>

                <div>
                    <label for="plataforma_propostas" class="block text-sm font-medium text-gray-700">Plataforma/Propostas (opcional):</label>
                    <textarea id="plataforma_propostas" name="plataforma_propostas" rows="4"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-azul-cipa focus:border-azul-cipa"><?php echo htmlspecialchars($dados_formulario_edicao_candidato['plataforma_propostas'] ?? ''); ?></textarea>
                </div>

                <div>
                    <label for="status_candidatura" class="block text-sm font-medium text-gray-700">Status da Candidatura:</label>
                    <select id="status_candidatura" name="status_candidatura" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-azul-cipa focus:border-azul-cipa">
                        <?php foreach ($status_candidatura_permitidos as $status): ?>
                            <option value="<?php echo $status; ?>"
                                <?php echo (isset($dados_formulario_edicao_candidato['status_candidatura']) && $dados_formulario_edicao_candidato['status_candidatura'] == $status) ? 'selected' : ''; ?>>
                                <?php echo $status; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="pt-5">
                    <button type="submit"
                            class="w-full md:w-auto flex justify-center py-3 px-6 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-azul-cipa hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-azul-cipa">
                        Salvar Alterações na Candidatura
                    </button>
                </div>
            </form>
        </div>
    </main>
    <footer class="text-center p-4 mt-8 text-sm text-gray-500">
        &copy; <?php echo date("Y"); ?> CIPA Fácil Online.
    </footer>
</body>
</html>

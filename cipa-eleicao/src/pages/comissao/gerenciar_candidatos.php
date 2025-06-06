<?php
require_once '../../scripts/auth_comissao.php';
require_once '../../scripts/db_connection.php';

$eleicao_id = $comissao_eleicao_id_logado;
$eleicao_titulo = $comissao_eleicao_titulo_logado;
$ano_referencia = '';

try {
    $stmt_eleicao_info = $pdo->prepare("SELECT ano_referencia FROM eleicoes WHERE id = ?");
    $stmt_eleicao_info->execute([$eleicao_id]);
    $eleicao_info = $stmt_eleicao_info->fetch(PDO::FETCH_ASSOC);
    if ($eleicao_info) {
        $ano_referencia = $eleicao_info['ano_referencia'];
    }
} catch (PDOException $e) {
    error_log("Erro ao buscar ano da eleição: " . $e->getMessage());
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

$erros_inscricao = $_SESSION['erros_inscrever_candidato'] ?? [];
$dados_formulario_candidato = $_SESSION['dados_formulario_candidato'] ?? [];
unset($_SESSION['erros_inscrever_candidato'], $_SESSION['dados_formulario_candidato']);

// Unificar mensagens de erro e sucesso
$mensagem_sucesso = $_SESSION['mensagem_sucesso_candidato'] ?? null;
unset($_SESSION['mensagem_sucesso_candidato']);
$mensagem_erro = $_SESSION['mensagem_erro_candidato'] ?? null;
unset($_SESSION['mensagem_erro_candidato']);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Candidatos - CIPA Fácil</title>
    <link href="../../src/styles/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

    <nav class="bg-azul-cipa text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="text-xl font-bold">
                    Comissão: <?php echo htmlspecialchars($eleicao_titulo); ?>
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
            <h2 class="text-2xl font-semibold text-cinza-chumbo">Gerenciar Candidaturas</h2>
            <a href="painel_comissao.php" class="text-azul-cipa hover:text-blue-700">&larr; Voltar ao Painel da Comissão</a>
        </div>

        <p class="mb-6 text-gray-700">Eleição: <strong><?php echo htmlspecialchars($eleicao_titulo); ?></strong> (Ano: <?php echo htmlspecialchars($ano_referencia); ?>)</p>

        <?php
        if ($mensagem_sucesso) {
            echo "<div class='p-4 mb-4 text-sm text-green-700 bg-green-100 border border-green-400 rounded-lg' role='alert'>" . htmlspecialchars($mensagem_sucesso) . "</div>";
        }
        if ($mensagem_erro) {
            echo "<div class='p-4 mb-4 text-sm text-red-700 bg-red-100 border border-red-400 rounded-lg' role='alert'>" . htmlspecialchars($mensagem_erro) . "</div>";
        }
        if (isset($erro_db_candidatos)) {
            echo "<div class='p-4 mb-4 text-sm text-red-700 bg-red-100 border border-red-400 rounded-lg' role='alert'>" . htmlspecialchars($erro_db_candidatos) . "</div>";
        }
        ?>

        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h3 class="text-xl font-semibold text-azul-cipa mb-4">Candidatos Inscritos</h3>
            <?php if (empty($candidatos) && !isset($erro_db_candidatos)): ?>
                <p class="text-gray-600">Nenhum candidato inscrito para esta eleição até o momento.</p>
            <?php elseif (!empty($candidatos)): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Funcionário</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nº</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nome na Urna</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($candidatos as $candidato): ?>
                                <tr>
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm"><p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($candidato['funcionario_nome']); ?></p></td>
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm"><p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($candidato['numero_candidato'] ?? 'N/D'); ?></p></td>
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm"><p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($candidato['nome_urna'] ?? 'N/D'); ?></p></td>
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                        <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs
                                            <?php
                                                switch ($candidato['status_candidatura']) {
                                                    case 'Inscrito': echo 'text-blue-700 bg-blue-100'; break;
                                                    case 'Aprovado': echo 'text-green-700 bg-green-100'; break;
                                                    case 'Reprovado': echo 'text-red-700 bg-red-100'; break;
                                                    case 'Eleito': echo 'text-purple-700 bg-purple-100'; break;
                                                    case 'Suplente': echo 'text-yellow-700 bg-yellow-100'; break;
                                                    default: echo 'text-gray-700 bg-gray-100';
                                                }
                                            ?>">
                                            <?php echo htmlspecialchars($candidato['status_candidatura']); ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm whitespace-no-wrap">
                                        <a href="editar_candidatura.php?candidato_id=<?php echo $candidato['candidato_id']; ?>" class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md text-xs font-medium transition-colors">Editar</a>
                                        <?php if ($candidato['status_candidatura'] == 'Inscrito'): ?>
                                            <a href="processa_alterar_status_candidato.php?candidato_id=<?php echo $candidato['candidato_id']; ?>&novo_status=Aprovado" class="ml-2 px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded-md text-xs font-medium transition-colors" onclick="return confirm('Aprovar candidatura?');">Aprovar</a>
                                            <a href="processa_alterar_status_candidato.php?candidato_id=<?php echo $candidato['candidato_id']; ?>&novo_status=Reprovado" class="ml-2 px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded-md text-xs font-medium transition-colors" onclick="return confirm('Reprovar candidatura?');">Reprovar</a>
                                        <?php elseif (in_array($candidato['status_candidatura'], ['Aprovado', 'Reprovado'])): ?>
                                            <a href="processa_alterar_status_candidato.php?candidato_id=<?php echo $candidato['candidato_id']; ?>&novo_status=Inscrito" class="ml-2 px-3 py-1 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-xs font-medium transition-colors" onclick="return confirm('Reverter para Inscrito?');">P/ Inscrito</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold text-azul-cipa mb-4">Inscrever Novo Candidato</h3>
            <?php if (!empty($erros_inscricao)): ?>
                <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 border border-red-400 rounded-lg" role="alert">
                    <ul class="list-disc pl-5">
                        <?php foreach ($erros_inscricao as $erro): ?>
                            <li><?php echo htmlspecialchars($erro); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form action="processa_inscrever_candidato.php" method="POST" class="space-y-4">
                <input type="hidden" name="eleicao_id" value="<?php echo $eleicao_id; ?>">

                <div>
                    <label for="funcionario_id" class="block text-sm font-medium text-gray-700">ID do Funcionário:</label>
                    <input type="number" id="funcionario_id" name="funcionario_id" value="<?php echo htmlspecialchars($dados_formulario_candidato['funcionario_id'] ?? ''); ?>" required
                           class="mt-1 block w-full md:w-1/2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-azul-cipa focus:border-azul-cipa">
                    <p class="text-xs text-gray-500 mt-1">//TODO: Implementar busca/dropdown dinâmico de funcionários aptos.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="numero_candidato" class="block text-sm font-medium text-gray-700">Número do Candidato (opcional):</label>
                        <input type="number" id="numero_candidato" name="numero_candidato" value="<?php echo htmlspecialchars($dados_formulario_candidato['numero_candidato'] ?? ''); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-azul-cipa focus:border-azul-cipa">
                    </div>
                    <div>
                        <label for="nome_urna" class="block text-sm font-medium text-gray-700">Nome na Urna (opcional):</label>
                        <input type="text" id="nome_urna" name="nome_urna" value="<?php echo htmlspecialchars($dados_formulario_candidato['nome_urna'] ?? ''); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-azul-cipa focus:border-azul-cipa">
                    </div>
                </div>

                <div>
                    <label for="plataforma_propostas" class="block text-sm font-medium text-gray-700">Plataforma/Propostas (opcional):</label>
                    <textarea id="plataforma_propostas" name="plataforma_propostas" rows="3"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-azul-cipa focus:border-azul-cipa"><?php echo htmlspecialchars($dados_formulario_candidato['plataforma_propostas'] ?? ''); ?></textarea>
                </div>
                <div>
                    <button type="submit" class="w-full md:w-auto px-6 py-2 bg-azul-cipa hover:bg-blue-700 text-white font-bold rounded-md shadow-md transition-colors">
                        Inscrever Candidato
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

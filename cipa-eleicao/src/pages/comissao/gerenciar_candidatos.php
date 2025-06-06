<?php
$titulo_pagina_comissao = "Gerenciar Candidaturas";
require_once 'includes/header_painel_comissao.php';
// auth_comissao, db_connection, $comissao_eleicao_id_logado, $comissao_eleicao_titulo_logado já incluídos/definidos

$eleicao_id = $comissao_eleicao_id_logado;
$eleicao_titulo = $comissao_eleicao_titulo_logado;
$ano_referencia = '';

if(isset($pdo)){ // Verifica se $pdo está disponível
    try {
        $stmt_eleicao_info = $pdo->prepare("SELECT ano_referencia FROM eleicoes WHERE id = ?");
        $stmt_eleicao_info->execute([$eleicao_id]);
        $eleicao_info_db = $stmt_eleicao_info->fetch(PDO::FETCH_ASSOC); // Renomeado para evitar conflito
        if ($eleicao_info_db) {
            $ano_referencia = $eleicao_info_db['ano_referencia'];
        }
    } catch (PDOException $e) {
        error_log("Erro ao buscar ano da eleição (gerenciar_candidatos): " . $e->getMessage());
    }
}


$candidatos = [];
if(isset($pdo)){
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
        error_log("Erro ao buscar candidatos (gerenciar_candidatos): " . $e->getMessage());
        $_SESSION['mensagem_erro_comissao_global'] = "Não foi possível carregar os candidatos. Tente novamente mais tarde.";
    }
}


$erros_inscricao = $_SESSION['erros_inscrever_candidato'] ?? [];
$dados_formulario_candidato = $_SESSION['dados_formulario_candidato'] ?? [];
unset($_SESSION['erros_inscrever_candidato'], $_SESSION['dados_formulario_candidato']);

$mensagem_sucesso = $_SESSION['mensagem_sucesso_candidato'] ?? null;
unset($_SESSION['mensagem_sucesso_candidato']);
$mensagem_erro = $_SESSION['mensagem_erro_candidato'] ?? null; // Erros específicos da página de candidatura
unset($_SESSION['mensagem_erro_candidato']);

?>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
    <h1 class="text-2xl sm:text-3xl font-bold text-cinza-chumbo mb-4 sm:mb-0">
        Gerenciar Candidaturas
    </h1>
    <!-- Link para voltar ao painel já está no header_painel_comissao -->
</div>

<p class="mb-6 text-gray-700 text-lg">Eleição: <strong class="font-semibold text-azul-cipa"><?php echo htmlspecialchars($eleicao_titulo); ?></strong> (Ano: <?php echo htmlspecialchars($ano_referencia); ?>)</p>

<?php
if ($mensagem_sucesso) {
    echo "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6' role='alert'><p>" . htmlspecialchars($mensagem_sucesso) . "</p></div>";
}
if ($mensagem_erro) {
    echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6' role='alert'><p>" . htmlspecialchars($mensagem_erro) . "</p></div>";
}
?>

<div class="bg-white p-6 rounded-xl shadow-lg mb-8">
    <h2 class="text-xl font-semibold text-cinza-chumbo mb-4">Candidatos Inscritos</h2>
    <?php if (empty($candidatos) && !isset($_SESSION['mensagem_erro_comissao_global'])): ?>
        <div class="text-center py-4">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            <p class="text-gray-600 text-lg mt-3">Nenhum candidato inscrito para esta eleição.</p>
        </div>
    <?php elseif (!empty($candidatos)): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Funcionário</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Nº</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Nome Urna</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($candidatos as $candidato): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($candidato['funcionario_nome']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($candidato['numero_candidato'] ?? 'N/D'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($candidato['nome_urna'] ?? 'N/D'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                <?php
                                    switch ($candidato['status_candidatura']) {
                                        case 'Inscrito': echo 'bg-blue-100 text-blue-800'; break;
                                        case 'Aprovado': echo 'bg-green-100 text-green-800'; break;
                                        case 'Reprovado': echo 'bg-red-100 text-red-800'; break;
                                        case 'Eleito': echo 'bg-purple-100 text-purple-800'; break;
                                        case 'Suplente': echo 'bg-yellow-100 text-yellow-800'; break;
                                        default: echo 'bg-gray-100 text-gray-800';
                                    }
                                ?>">
                                    <?php echo htmlspecialchars($candidato['status_candidatura']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="editar_candidatura.php?candidato_id=<?php echo $candidato['candidato_id']; ?>" class="text-white bg-yellow-500 hover:bg-yellow-600 px-3 py-1 rounded-md text-xs shadow-sm transition-colors">Editar</a>
                                <?php if ($candidato['status_candidatura'] == 'Inscrito'): ?>
                                    <a href="processa_alterar_status_candidato.php?candidato_id=<?php echo $candidato['candidato_id']; ?>&novo_status=Aprovado" class="ml-2 text-white bg-green-500 hover:bg-green-600 px-3 py-1 rounded-md text-xs shadow-sm transition-colors" onclick="return confirm('Aprovar candidatura?');">Aprovar</a>
                                    <a href="processa_alterar_status_candidato.php?candidato_id=<?php echo $candidato['candidato_id']; ?>&novo_status=Reprovado" class="ml-2 text-white bg-red-500 hover:bg-red-600 px-3 py-1 rounded-md text-xs shadow-sm transition-colors" onclick="return confirm('Reprovar candidatura?');">Reprovar</a>
                                <?php elseif (in_array($candidato['status_candidatura'], ['Aprovado', 'Reprovado'])): ?>
                                    <a href="processa_alterar_status_candidato.php?candidato_id=<?php echo $candidato['candidato_id']; ?>&novo_status=Inscrito" class="ml-2 text-white bg-gray-500 hover:bg-gray-600 px-3 py-1 rounded-md text-xs shadow-sm transition-colors" onclick="return confirm('Reverter para Inscrito?');">P/ Inscrito</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="bg-white p-6 sm:p-8 rounded-xl shadow-lg mt-8">
    <h2 class="text-xl font-semibold text-cinza-chumbo mb-4">Inscrever Novo Candidato</h2>
    <?php if (!empty($erros_inscricao)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
            <p class="font-bold">Erros ao inscrever candidato:</p>
            <ul class="list-disc pl-5 mt-2 text-sm">
                <?php foreach ($erros_inscricao as $erro): ?>
                    <li><?php echo htmlspecialchars($erro); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form action="processa_inscrever_candidato.php" method="POST" class="space-y-6">
        <input type="hidden" name="eleicao_id" value="<?php echo $eleicao_id; ?>">

        <div>
            <label for="funcionario_id" class="block text-sm font-medium text-gray-700 mb-1">ID do Funcionário:</label>
            <input type="number" id="funcionario_id" name="funcionario_id" value="<?php echo htmlspecialchars($dados_formulario_candidato['funcionario_id'] ?? ''); ?>" required
                   class="appearance-none block w-full md:w-1/2 px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-azul-cipa focus:border-azul-cipa sm:text-sm">
            <p class="mt-1 text-xs text-gray-500">//TODO: Implementar busca/dropdown dinâmico de funcionários aptos.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="numero_candidato" class="block text-sm font-medium text-gray-700 mb-1">Número do Candidato (opcional):</label>
                <input type="number" id="numero_candidato" name="numero_candidato" value="<?php echo htmlspecialchars($dados_formulario_candidato['numero_candidato'] ?? ''); ?>"
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-azul-cipa focus:border-azul-cipa sm:text-sm">
            </div>
            <div>
                <label for="nome_urna" class="block text-sm font-medium text-gray-700 mb-1">Nome na Urna (opcional):</label>
                <input type="text" id="nome_urna" name="nome_urna" value="<?php echo htmlspecialchars($dados_formulario_candidato['nome_urna'] ?? ''); ?>"
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-azul-cipa focus:border-azul-cipa sm:text-sm">
            </div>
        </div>

        <div>
            <label for="plataforma_propostas" class="block text-sm font-medium text-gray-700 mb-1">Plataforma/Propostas (opcional):</label>
            <textarea id="plataforma_propostas" name="plataforma_propostas" rows="3"
                      class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-azul-cipa focus:border-azul-cipa sm:text-sm"><?php echo htmlspecialchars($dados_formulario_candidato['plataforma_propostas'] ?? ''); ?></textarea>
        </div>
        <div class="pt-2">
            <button type="submit" class="w-full sm:w-auto flex justify-center py-2 px-6 border border-transparent rounded-lg shadow-md text-base font-medium text-white bg-azul-cipa hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-azul-cipa">
                Inscrever Candidato
            </button>
        </div>
    </form>
</div>

<?php
require_once 'includes/footer_painel_comissao.php';
?>

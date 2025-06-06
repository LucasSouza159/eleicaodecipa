<?php
$titulo_pagina_admin = "Gerenciar Todas Eleições";
require_once 'includes/header_painel_admin.php';

$eleicoes = [];
$erro_busca_eleicoes = null;
$total_eleicoes = 0;

$pagina_corrente = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
$itens_por_pagina = 15;
$offset = ($pagina_corrente - 1) * $itens_por_pagina;

if (isset($pdo)) {
    try {
        $stmt_total = $pdo->query("SELECT COUNT(*) FROM eleicoes");
        $total_eleicoes = (int) $stmt_total->fetchColumn();

        $stmt_eleicoes = $pdo->prepare(
            "SELECT e.id, e.titulo_eleicao, e.ano_referencia, e.status_eleicao,
                    e.data_convocacao, e.data_inicio_votacao, e.data_fim_votacao,
                    emp.nome_fantasia AS nome_empresa
             FROM eleicoes e
             JOIN empresas emp ON e.empresa_id = emp.id
             ORDER BY e.data_criacao DESC
             LIMIT :limit OFFSET :offset"
        );
        $stmt_eleicoes->bindParam(':limit', $itens_por_pagina, PDO::PARAM_INT);
        $stmt_eleicoes->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt_eleicoes->execute();
        $eleicoes = $stmt_eleicoes->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Erro ao buscar todas as eleições (Admin): " . $e->getMessage());
        $erro_busca_eleicoes = "Não foi possível carregar a lista de eleições. Tente novamente mais tarde.";
    }
} else {
    $erro_busca_eleicoes = "Erro crítico: A conexão com o banco de dados não está disponível.";
}
$total_paginas_eleicoes = ceil($total_eleicoes / $itens_por_pagina);
?>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
    <h1 class="text-2xl sm:text-3xl font-bold text-cinza-chumbo mb-4 sm:mb-0">
        Gerenciamento de Todas as Eleições do Sistema
    </h1>
     <!-- Botão para criar nova eleição globalmente (se fizer sentido para o admin) -->
    <button type="button"
            class="bg-verde-cipa hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out opacity-50 cursor-not-allowed"
            title="Funcionalidade em desenvolvimento">
        + Criar Nova Eleição Global (Em breve)
    </button>
</div>

<?php
if ($erro_busca_eleicoes) {
    echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6' role='alert'><p>" . htmlspecialchars($erro_busca_eleicoes) . "</p></div>";
}
// Mensagens de sucesso/erro de outras operações
if (isset($_SESSION['mensagem_sucesso_admin_op_eleicao'])) {
    echo "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6' role='alert'><p>" . htmlspecialchars($_SESSION['mensagem_sucesso_admin_op_eleicao']) . "</p></div>";
    unset($_SESSION['mensagem_sucesso_admin_op_eleicao']);
}
if (isset($_SESSION['mensagem_erro_admin_op_eleicao'])) {
    echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6' role='alert'><p>" . htmlspecialchars($_SESSION['mensagem_erro_admin_op_eleicao']) . "</p></div>";
    unset($_SESSION['mensagem_erro_admin_op_eleicao']);
}
?>

<?php if (empty($eleicoes) && !$erro_busca_eleicoes): ?>
    <div class="bg-white p-6 rounded-xl shadow-lg text-center">
         <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-gray-600 text-lg mt-4">Nenhuma eleição cadastrada no sistema.</p>
    </div>
<?php elseif (!empty($eleicoes)): ?>
    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Título da Eleição</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Empresa</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider hidden sm:table-cell">Ano</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider hidden lg:table-cell">Início Votação</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider hidden lg:table-cell">Fim Votação</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($eleicoes as $eleicao): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $eleicao['id']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($eleicao['titulo_eleicao']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($eleicao['nome_empresa']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell"><?php echo htmlspecialchars($eleicao['ano_referencia']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                <?php
                                    switch ($eleicao['status_eleicao']) {
                                        case 'Planejada': echo 'bg-gray-100 text-gray-800'; break;
                                        case 'Convocada': echo 'bg-blue-100 text-blue-800'; break;
                                        case 'Inscrições Abertas': echo 'bg-yellow-100 text-yellow-800'; break;
                                        case 'Em Votação': echo 'bg-red-100 text-red-800'; break;
                                        case 'Votação Encerrada': case 'Em Apuração': echo 'bg-purple-100 text-purple-800'; break;
                                        case 'Resultados Publicados': case 'Finalizada': echo 'bg-green-100 text-green-800'; break;
                                        default: echo 'bg-gray-100 text-gray-800';
                                    }
                                ?>">
                                    <?php echo htmlspecialchars($eleicao['status_eleicao']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell"><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($eleicao['data_inicio_votacao']))); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell"><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($eleicao['data_fim_votacao']))); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="#" class="text-xs px-3 py-1 bg-azul-cipa hover:bg-blue-700 text-white rounded-md shadow-sm transition-colors opacity-50 cursor-not-allowed" title="Em breve">Detalhes</a>
                                <!-- <a href="#" class="ml-2 text-xs px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded-md shadow-sm transition-colors opacity-50 cursor-not-allowed" title="Em breve">Suspender</a> -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($total_paginas_eleicoes > 1): ?>
        <div class="mt-6 flex justify-center items-center space-x-2">
            <?php if ($pagina_corrente > 1): ?>
                <a href="?pagina=<?php echo $pagina_corrente - 1; ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Anterior</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_paginas_eleicoes; $i++): ?>
                <a href="?pagina=<?php echo $i; ?>"
                   class="px-4 py-2 text-sm font-medium <?php echo ($i == $pagina_corrente) ? 'text-white bg-cinza-chumbo border-cinza-chumbo' : 'text-gray-700 bg-white border-gray-300'; ?> rounded-md hover:bg-gray-50">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            <?php if ($pagina_corrente < $total_paginas_eleicoes): ?>
                <a href="?pagina=<?php echo $pagina_corrente + 1; ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Próxima</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

<?php endif; ?>

<?php
require_once 'includes/footer_painel_admin.php';
?>

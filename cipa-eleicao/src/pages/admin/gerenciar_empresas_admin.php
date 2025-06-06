<?php
$titulo_pagina_admin = "Gerenciar Empresas";
require_once 'includes/header_painel_admin.php';
// auth_admin.php e db_connection.php já são incluídos em header_painel_admin.php
// $admin_id_logado, $admin_nome_logado, etc., já estão disponíveis.

$empresas = [];
$erro_busca = null;
$total_empresas = 0;

// Paginação (básico)
$pagina_corrente = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
$itens_por_pagina = 15; // Definir quantos itens por página
$offset = ($pagina_corrente - 1) * $itens_por_pagina;

if (isset($pdo)) {
    try {
        // Contar total de empresas para paginação
        $stmt_total = $pdo->query("SELECT COUNT(*) FROM empresas");
        $total_empresas = (int) $stmt_total->fetchColumn();

        // Buscar empresas com paginação
        $stmt_empresas = $pdo->prepare(
            "SELECT id, nome_fantasia, razao_social, cnpj, email, data_cadastro
             FROM empresas
             ORDER BY data_cadastro DESC
             LIMIT :limit OFFSET :offset"
        );
        $stmt_empresas->bindParam(':limit', $itens_por_pagina, PDO::PARAM_INT);
        $stmt_empresas->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt_empresas->execute();
        $empresas = $stmt_empresas->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Erro ao buscar empresas (Admin): " . $e->getMessage());
        $erro_busca = "Não foi possível carregar a lista de empresas. Tente novamente mais tarde.";
        // Em um cenário real, poderia redirecionar ou exibir uma mensagem mais amigável.
    }
} else {
    $erro_busca = "Erro crítico: A conexão com o banco de dados não está disponível.";
}
$total_paginas = ceil($total_empresas / $itens_por_pagina);
?>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
    <h1 class="text-2xl sm:text-3xl font-bold text-cinza-chumbo mb-4 sm:mb-0">
        Gerenciamento de Empresas Cadastradas
    </h1>
    <!-- Botão para adicionar nova empresa (placeholder para funcionalidade futura) -->
    <button type="button"
            class="bg-verde-cipa hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out opacity-50 cursor-not-allowed"
            title="Funcionalidade em desenvolvimento">
        + Adicionar Nova Empresa (Em breve)
    </button>
</div>

<?php
if ($erro_busca) {
    echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6' role='alert'><p>" . htmlspecialchars($erro_busca) . "</p></div>";
}
// Mensagens de sucesso/erro de outras operações (ex: edição de status)
if (isset($_SESSION['mensagem_sucesso_admin_op'])) {
    echo "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6' role='alert'><p>" . htmlspecialchars($_SESSION['mensagem_sucesso_admin_op']) . "</p></div>";
    unset($_SESSION['mensagem_sucesso_admin_op']);
}
if (isset($_SESSION['mensagem_erro_admin_op'])) {
    echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6' role='alert'><p>" . htmlspecialchars($_SESSION['mensagem_erro_admin_op']) . "</p></div>";
    unset($_SESSION['mensagem_erro_admin_op']);
}
?>

<?php if (empty($empresas) && !$erro_busca): ?>
    <div class="bg-white p-6 rounded-xl shadow-lg text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        <p class="text-gray-600 text-lg mt-4">Nenhuma empresa cadastrada no sistema.</p>
    </div>
<?php elseif (!empty($empresas)): ?>
    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Nome Fantasia</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider hidden md:table-cell">Razão Social</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">CNPJ</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider hidden sm:table-cell">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider hidden lg:table-cell">Cadastro</th>
                        <!-- Se adicionar status para empresa:
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Status</th>
                        -->
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($empresas as $empresa): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $empresa['id']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($empresa['nome_fantasia']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell"><?php echo htmlspecialchars($empresa['razao_social']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($empresa['cnpj']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell"><?php echo htmlspecialchars($empresa['email']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell"><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($empresa['data_cadastro']))); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="#" class="text-xs px-3 py-1 bg-azul-cipa hover:bg-blue-700 text-white rounded-md shadow-sm transition-colors opacity-50 cursor-not-allowed" title="Em breve">Detalhes</a>
                                <!-- <a href="#" class="ml-2 text-xs px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md shadow-sm transition-colors opacity-50 cursor-not-allowed" title="Em breve">Editar Status</a> -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($total_paginas > 1): ?>
        <div class="mt-6 flex justify-center items-center space-x-2">
            <?php if ($pagina_corrente > 1): ?>
                <a href="?pagina=<?php echo $pagina_corrente - 1; ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Anterior</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <a href="?pagina=<?php echo $i; ?>"
                   class="px-4 py-2 text-sm font-medium <?php echo ($i == $pagina_corrente) ? 'text-white bg-cinza-chumbo border-cinza-chumbo' : 'text-gray-700 bg-white border-gray-300'; ?> rounded-md hover:bg-gray-50">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($pagina_corrente < $total_paginas): ?>
                <a href="?pagina=<?php echo $pagina_corrente + 1; ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Próxima</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

<?php endif; ?>

<?php
require_once 'includes/footer_painel_admin.php';
?>

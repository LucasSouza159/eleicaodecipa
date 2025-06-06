<?php
$titulo_pagina = "Gerenciar Eleições";
require_once 'includes/header_painel.php';
// auth_empresa e db_connection já incluídos em header_painel.php
// $empresa_id já está disponível

$eleicoes = [];
try {
    $stmt = $pdo->prepare(
        "SELECT e.id, e.titulo_eleicao, e.ano_referencia, e.status_eleicao, e.data_convocacao,
                f.nome_fantasia AS nome_filial
         FROM eleicoes e
         LEFT JOIN filiais f ON e.filial_id = f.id
         WHERE e.empresa_id = :empresa_id
         ORDER BY e.data_criacao DESC"
    );
    $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
    $stmt->execute();
    $eleicoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ao buscar eleições: " . $e->getMessage());
    $_SESSION['mensagem_erro_global'] = "Não foi possível carregar as eleições. Tente novamente mais tarde.";
    // header("Location: painel_empresa.php"); // Evitar header aqui, pois pode quebrar o layout
    // exit;
}
?>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
    <h1 class="text-2xl sm:text-3xl font-bold text-cinza-chumbo mb-4 sm:mb-0">
        Gerenciamento de Eleições
    </h1>
    <a href="criar_eleicao.php"
       class="bg-verde-cipa hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out whitespace-nowrap">
        + Criar Nova Eleição
    </a>
</div>

<?php
// Mensagens de sucesso/erro específicas desta página (ex: de processa_criar_eleicao)
if (isset($_SESSION['mensagem_sucesso'])) {
    echo "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6' role='alert'><p>" . htmlspecialchars($_SESSION['mensagem_sucesso']) . "</p></div>";
    unset($_SESSION['mensagem_sucesso']);
}
if (isset($_SESSION['mensagem_erro'])) {
    echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6' role='alert'><p>" . htmlspecialchars($_SESSION['mensagem_erro']) . "</p></div>";
    unset($_SESSION['mensagem_erro']);
}
?>

<?php if (empty($eleicoes) && !isset($_SESSION['mensagem_erro_global'])): ?>
    <div class="bg-white p-6 rounded-xl shadow-lg text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-gray-600 text-lg mt-4">Nenhuma eleição encontrada para esta empresa.</p>
        <p class="mt-2 text-sm text-gray-500">Clique em "Criar Nova Eleição" para começar.</p>
    </div>
<?php elseif (!empty($eleicoes)): ?>
    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Título da Eleição</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Filial</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Ano</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Convocação</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($eleicoes as $eleicao): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($eleicao['titulo_eleicao']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($eleicao['nome_filial'] ?? 'N/A'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($eleicao['ano_referencia']); ?></td>
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars(date('d/m/Y', strtotime($eleicao['data_convocacao']))); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="gerenciar_comissao_eleitoral.php?eleicao_id=<?php echo $eleicao['id']; ?>" class="text-xs px-3 py-1 bg-purple-500 hover:bg-purple-600 text-white rounded-md shadow-sm transition-colors">Comissão</a>
                                <a href="editar_eleicao.php?id=<?php echo $eleicao['id']; ?>" class="text-xs px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md shadow-sm transition-colors">Editar</a>
                                <!-- <a href="ver_detalhes_eleicao.php?id=<?php echo $eleicao['id']; ?>" class="text-blue-600 hover:text-blue-800">Detalhes</a> -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php
require_once 'includes/footer_painel.php';
?>

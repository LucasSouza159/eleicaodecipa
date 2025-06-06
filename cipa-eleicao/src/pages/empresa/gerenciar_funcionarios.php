<?php
$titulo_pagina = "Gerenciar Funcionários";
require_once 'includes/header_painel.php';
// auth_empresa e db_connection já incluídos em header_painel.php
// $empresa_id já está disponível

$funcionarios = [];
try {
    $stmt = $pdo->prepare(
        "SELECT id, nome_completo, cpf, email, matricula, data_admissao, status_funcionario
         FROM funcionarios
         WHERE empresa_id = :empresa_id
         ORDER BY nome_completo ASC"
    );
    $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
    $stmt->execute();
    $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ao buscar funcionários: " . $e->getMessage());
    $_SESSION['mensagem_erro_global'] = "Não foi possível carregar os funcionários. Tente novamente mais tarde.";
}
?>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
    <h1 class="text-2xl sm:text-3xl font-bold text-cinza-chumbo mb-4 sm:mb-0">
        Gerenciamento de Funcionários
    </h1>
    <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
        <a href="cadastrar_funcionario.php"
           class="w-full sm:w-auto text-center bg-verde-cipa hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out whitespace-nowrap">
            + Novo Funcionário
        </a>
        <button type="button"
                onclick="alert('Funcionalidade de Importar CSV será implementada em breve.');"
                class="w-full sm:w-auto text-center bg-azul-cipa hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out whitespace-nowrap">
            Importar CSV
        </button>
    </div>
</div>

<?php
// Mensagens de sucesso/erro específicas desta página
if (isset($_SESSION['mensagem_sucesso'])) {
    echo "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6' role='alert'><p>" . htmlspecialchars($_SESSION['mensagem_sucesso']) . "</p></div>";
    unset($_SESSION['mensagem_sucesso']);
}
if (isset($_SESSION['mensagem_erro'])) { // Para erros gerais vindos de outras páginas (ex: ID inválido na edição)
    echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6' role='alert'><p>" . htmlspecialchars($_SESSION['mensagem_erro']) . "</p></div>";
    unset($_SESSION['mensagem_erro']);
}
?>

<?php if (empty($funcionarios) && !isset($_SESSION['mensagem_erro_global'])): ?>
    <div class="bg-white p-6 rounded-xl shadow-lg text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <p class="text-gray-600 text-lg mt-4">Nenhum funcionário cadastrado.</p>
        <p class="mt-2 text-sm text-gray-500">Utilize os botões acima para adicionar ou importar funcionários.</p>
    </div>
<?php elseif (!empty($funcionarios)): ?>
    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Nome Completo</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">CPF</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Matrícula</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Admissão</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($funcionarios as $funcionario): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($funcionario['nome_completo']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($funcionario['cpf']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($funcionario['email'] ?? 'N/A'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($funcionario['matricula'] ?? 'N/A'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars(date('d/m/Y', strtotime($funcionario['data_admissao']))); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                <?php echo $funcionario['status_funcionario'] === 'Ativo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo htmlspecialchars($funcionario['status_funcionario']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="editar_funcionario.php?id=<?php echo $funcionario['id']; ?>" class="text-xs px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md shadow-sm transition-colors">Editar</a>
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

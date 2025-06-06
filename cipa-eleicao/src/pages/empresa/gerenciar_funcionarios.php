<?php
require_once '../../scripts/auth_empresa.php';
require_once '../../scripts/db_connection.php';

$empresa_id = $_SESSION['empresa_id'];
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
    $erro_db = "Não foi possível carregar os funcionários. Tente novamente mais tarde.";
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Funcionários - CIPA Fácil</title>
    <link href="../../src/styles/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

    <!-- Navbar do Painel -->
    <nav class="bg-roxo-principal text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="text-xl font-bold">
                    Painel da Empresa: <?php echo htmlspecialchars($_SESSION['empresa_nome_fantasia'] ?? 'Empresa'); ?>
                </div>
                <div>
                    <a href="logout_empresa.php" class="px-4 py-2 bg-red-500 hover:bg-red-600 rounded-md text-sm font-medium transition-colors">Sair</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto p-6 mt-8">
        <div class="flex flex-col md:flex-row justify-between md:items-center mb-6 gap-4">
            <h2 class="text-2xl font-semibold text-cinza-chumbo">Gerenciar Funcionários</h2>
            <div class="flex flex-col md:flex-row gap-2">
                <a href="painel_empresa.php" class="text-roxo-principal hover:text-purple-700 self-start md:self-center md:mr-4">&larr; Voltar ao Painel</a>
                <a href="cadastrar_funcionario.php"
                   class="bg-verde-cipa hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md shadow-md transition-colors text-center">
                    + Cadastrar Novo Funcionário
                </a>
                <button type="button"
                        onclick="alert('Funcionalidade de Importar CSV será implementada em breve.');"
                        class="bg-azul-cipa hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md shadow-md transition-colors">
                    Importar CSV
                </button>
            </div>
        </div>

        <?php
        if (isset($_SESSION['mensagem_sucesso'])) {
            echo "<div class='p-4 mb-4 text-sm text-green-700 bg-green-100 border border-green-400 rounded-lg' role='alert'>" . htmlspecialchars($_SESSION['mensagem_sucesso']) . "</div>";
            unset($_SESSION['mensagem_sucesso']);
        }
        if (isset($_SESSION['mensagem_erro'])) {
            echo "<div class='p-4 mb-4 text-sm text-red-700 bg-red-100 border border-red-400 rounded-lg' role='alert'>" . htmlspecialchars($_SESSION['mensagem_erro']) . "</div>";
            unset($_SESSION['mensagem_erro']);
        }
        if (isset($erro_db)) {
            echo "<div class='p-4 mb-4 text-sm text-red-700 bg-red-100 border border-red-400 rounded-lg' role='alert'>" . htmlspecialchars($erro_db) . "</div>";
        }
        ?>

        <?php if (empty($funcionarios) && !isset($erro_db)): ?>
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <p class="text-gray-600 text-lg">Nenhum funcionário cadastrado para esta empresa.</p>
                <p class="mt-2">Clique em "Cadastrar Novo Funcionário" ou "Importar CSV" para começar.</p>
            </div>
        <?php elseif (!empty($funcionarios)): ?>
            <div class="bg-white shadow-md rounded-lg overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nome Completo</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">CPF</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Matrícula</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Data Admissão</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($funcionarios as $funcionario): ?>
                            <tr>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm"><p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($funcionario['nome_completo']); ?></p></td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm"><p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($funcionario['cpf']); ?></p></td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm"><p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($funcionario['email'] ?? 'N/A'); ?></p></td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm"><p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($funcionario['matricula'] ?? 'N/A'); ?></p></td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm"><p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars(date('d/m/Y', strtotime($funcionario['data_admissao']))); ?></p></td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                    <span class="relative inline-block px-3 py-1 font-semibold <?php echo $funcionario['status_funcionario'] === 'Ativo' ? 'text-green-900' : 'text-red-900'; ?> leading-tight">
                                        <span aria-hidden class="absolute inset-0 <?php echo $funcionario['status_funcionario'] === 'Ativo' ? 'bg-green-200' : 'bg-red-200'; ?> opacity-50 rounded-full"></span>
                                        <span class="relative"><?php echo htmlspecialchars($funcionario['status_funcionario']); ?></span>
                                    </span>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                    <a href="editar_funcionario.php?id=<?php echo $funcionario['id']; ?>" class="text-xs px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md transition-colors">Editar</a>
                                    <!-- <a href="ver_detalhes_funcionario.php?id=<?php echo $funcionario['id']; ?>" class="text-blue-600 hover:text-blue-800">Detalhes</a> -->
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
    <footer class="text-center p-4 mt-8 text-sm text-gray-500">
        &copy; <?php echo date("Y"); ?> CIPA Fácil Online.
    </footer>
</body>
</html>

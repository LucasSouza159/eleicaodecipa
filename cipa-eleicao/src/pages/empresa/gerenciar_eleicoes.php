<?php
require_once '../../scripts/auth_empresa.php';
require_once '../../scripts/db_connection.php';

$empresa_id = $_SESSION['empresa_id'];
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
    $erro_db = "Não foi possível carregar as eleições. Tente novamente mais tarde.";
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Eleições - CIPA Fácil</title>
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
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-cinza-chumbo">Gerenciar Eleições</h2>
            <div>
                <a href="painel_empresa.php" class="text-roxo-principal hover:text-purple-700 mr-4">&larr; Voltar ao Painel</a>
                <a href="criar_eleicao.php"
                   class="bg-verde-cipa hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md shadow-md transition-colors">
                    + Criar Nova Eleição
                </a>
            </div>
        </div>

        <?php
        if (isset($_SESSION['mensagem_sucesso'])) {
            echo "<div class='p-4 mb-4 text-sm text-green-700 bg-green-100 border border-green-400 rounded-lg' role='alert'>" . htmlspecialchars($_SESSION['mensagem_sucesso']) . "</div>";
            unset($_SESSION['mensagem_sucesso']);
        }
        if (isset($_SESSION['mensagem_erro'])) { // Para erros gerais vindos de outras páginas
            echo "<div class='p-4 mb-4 text-sm text-red-700 bg-red-100 border border-red-400 rounded-lg' role='alert'>" . htmlspecialchars($_SESSION['mensagem_erro']) . "</div>";
            unset($_SESSION['mensagem_erro']);
        }
        if (isset($erro_db)) {
            echo "<div class='p-4 mb-4 text-sm text-red-700 bg-red-100 border border-red-400 rounded-lg' role='alert'>" . htmlspecialchars($erro_db) . "</div>";
        }
        ?>

        <?php if (empty($eleicoes) && !isset($erro_db)): ?>
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <p class="text-gray-600 text-lg">Nenhuma eleição encontrada para esta empresa.</p>
                <p class="mt-2">Clique em "Criar Nova Eleição" para começar.</p>
            </div>
        <?php elseif (!empty($eleicoes)): ?>
            <div class="bg-white shadow-md rounded-lg overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Título da Eleição
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Filial
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Ano Referência
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Data de Convocação
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($eleicoes as $eleicao): ?>
                            <tr>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($eleicao['titulo_eleicao']); ?></p>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($eleicao['nome_filial'] ?? 'N/A (Empresa Principal)'); ?></p>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($eleicao['ano_referencia']); ?></p>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                    <span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                        <span aria-hidden class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                        <span class="relative"><?php echo htmlspecialchars($eleicao['status_eleicao']); ?></span>
                                    </span>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars(date('d/m/Y', strtotime($eleicao['data_convocacao']))); ?></p>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm space-x-2">
                                    <a href="ver_detalhes_eleicao.php?id=<?php echo $eleicao['id']; ?>" class="text-xs px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition-colors">Detalhes</a>
                                    <a href="editar_eleicao.php?id=<?php echo $eleicao['id']; ?>" class="text-xs px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md transition-colors">Editar</a>
                                    <a href="gerenciar_comissao_eleitoral.php?eleicao_id=<?php echo $eleicao['id']; ?>" class="text-xs px-2 py-1 bg-purple-500 hover:bg-purple-600 text-white rounded-md transition-colors">Comissão</a>
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

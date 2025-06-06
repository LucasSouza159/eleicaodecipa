<?php
session_start();
require_once '../../scripts/db_connection.php'; // Para buscar eleições em votação

$eleicoes_em_votacao = [];
$erro_eleicao = null;
try {
    // Buscar eleições que estão atualmente "Em Votação"
    // Poderíamos refinar para buscar eleições da empresa do funcionário se tivéssemos essa info antes do login,
    // mas para um portal geral de votação, listamos todas.
    $stmt = $pdo->query("SELECT id, titulo_eleicao FROM eleicoes WHERE status_eleicao = 'Em Votação' ORDER BY titulo_eleicao ASC");
    $eleicoes_em_votacao = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($eleicoes_em_votacao)) {
        $erro_eleicao = "No momento, não há eleições abertas para votação.";
    }

} catch (PDOException $e) {
    error_log("Erro ao buscar eleições em votação: " . $e->getMessage());
    $erro_eleicao = "Ocorreu um erro ao carregar as eleições disponíveis. Tente novamente mais tarde.";
}

// Tenta pegar o eleicao_id da URL se especificado, ou o primeiro da lista se houver apenas um.
$eleicao_id_selecionada = filter_input(INPUT_GET, 'eleicao_id', FILTER_VALIDATE_INT);
if (!$eleicao_id_selecionada && count($eleicoes_em_votacao) === 1) {
    $eleicao_id_selecionada = $eleicoes_em_votacao[0]['id'];
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login para Votação CIPA - CIPA Fácil</title>
    <link href="../../src/styles/output.css" rel="stylesheet">
    <style>
        html, body { height: 100%; margin: 0; }
    </style>
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center font-sans p-4">

    <div class="w-full max-w-lg p-8 space-y-6 bg-white shadow-xl rounded-lg">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-roxo-principal">CIPA Fácil</h1>
            <h2 class="text-2xl font-semibold text-cinza-chumbo mt-2">Portal de Votação</h2>
        </div>

        <?php
        if (isset($_SESSION['erro_login_votacao'])) {
            echo "<div class='p-4 mb-4 text-sm text-red-700 bg-red-100 border border-red-400 rounded-lg' role='alert'>" . htmlspecialchars($_SESSION['erro_login_votacao']) . "</div>";
            unset($_SESSION['erro_login_votacao']);
        }
        if ($erro_eleicao) {
             echo "<div class='p-4 mb-4 text-sm text-yellow-700 bg-yellow-100 border border-yellow-400 rounded-lg' role='alert'>" . htmlspecialchars($erro_eleicao) . "</div>";
        }
        ?>

        <?php if (!$erro_eleicao): // Só mostra o form se houver eleições ?>
        <form action="processa_login_votacao.php" method="POST" class="space-y-6">

            <?php if (count($eleicoes_em_votacao) > 1 && !$eleicao_id_selecionada): ?>
                <div class="form-group">
                    <label for="eleicao_id" class="block text-sm font-medium text-gray-700">Selecione a Eleição:</label>
                    <select id="eleicao_id" name="eleicao_id" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
                        <option value="">-- Escolha uma eleição --</option>
                        <?php foreach ($eleicoes_em_votacao as $eleicao): ?>
                            <option value="<?php echo $eleicao['id']; ?>">
                                <?php echo htmlspecialchars($eleicao['titulo_eleicao']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php elseif ($eleicao_id_selecionada): ?>
                <input type="hidden" name="eleicao_id" value="<?php echo $eleicao_id_selecionada; ?>">
                <div class="p-3 mb-4 text-sm text-blue-700 bg-blue-100 border border-blue-300 rounded-lg">
                    Você está votando para: <strong>
                    <?php
                        // Encontra o título da eleição selecionada para exibição
                        $titulo_exibicao = "Eleição não especificada";
                        foreach($eleicoes_em_votacao as $el) {
                            if ($el['id'] == $eleicao_id_selecionada) {
                                $titulo_exibicao = $el['titulo_eleicao'];
                                break;
                            }
                        }
                        echo htmlspecialchars($titulo_exibicao);
                    ?>
                    </strong>
                </div>
            <?php endif; ?>


            <div>
                <label for="cpf" class="block text-sm font-medium text-gray-700">CPF:</label>
                <input type="text" id="cpf" name="cpf" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm"
                       placeholder="Seu CPF (somente números)"> <!-- TODO: Adicionar máscara de CPF -->
            </div>

            <div>
                <label for="data_nascimento" class="block text-sm font-medium text-gray-700">Data de Nascimento:</label>
                <input type="date" id="data_nascimento" name="data_nascimento" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
            </div>

            <div>
                <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-roxo-principal hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-roxo-principal"
                        <?php if (empty($eleicoes_em_votacao) || (count($eleicoes_em_votacao) > 1 && !$eleicao_id_selecionada && empty($_POST['eleicao_id']) ) ) echo 'disabled'; ?> >
                        <!-- Desabilita se não houver eleição selecionável ou selecionada -->
                    Acessar Urna
                </button>
            </div>
        </form>
        <?php endif; ?>

        <p class="text-sm text-center mt-6">
            <a href="../../index.php" class="font-medium text-gray-600 hover:text-roxo-principal">
                &larr; Voltar à Página Inicial
            </a>
        </p>
    </div>
</body>
</html>

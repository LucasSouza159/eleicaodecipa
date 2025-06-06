<?php
session_start();
require_once '../../scripts/db_connection.php';

$eleicoes_em_votacao = [];
$erro_eleicao = null;
$eleicao_unica = false; // Flag para saber se há apenas uma eleição

try {
    $stmt = $pdo->query("SELECT id, titulo_eleicao FROM eleicoes WHERE status_eleicao = 'Em Votação' ORDER BY titulo_eleicao ASC");
    $eleicoes_em_votacao = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($eleicoes_em_votacao)) {
        $erro_eleicao = "No momento, não há eleições abertas para votação.";
    } elseif (count($eleicoes_em_votacao) === 1) {
        $eleicao_unica = true;
    }

} catch (PDOException $e) {
    error_log("Erro ao buscar eleições em votação: " . $e->getMessage());
    $erro_eleicao = "Ocorreu um erro ao carregar as eleições. Tente novamente mais tarde.";
}

$eleicao_id_selecionada = filter_input(INPUT_GET, 'eleicao_id', FILTER_VALIDATE_INT);
if (!$eleicao_id_selecionada && $eleicao_unica) {
    $eleicao_id_selecionada = $eleicoes_em_votacao[0]['id'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login para Votação | CIPA Fácil</title>
    <link href="../../src/styles/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg w-full space-y-8 bg-white p-8 sm:p-10 rounded-xl shadow-2xl">
            <div>
                <img class="mx-auto h-16 w-auto" src="../../assets/images/logo_cipa_facil_sm.png" alt="CIPA Fácil Logo">
                <h2 class="mt-6 text-center text-3xl font-extrabold text-cinza-chumbo">
                    Identificação do Eleitor
                </h2>
            </div>

            <?php
            if (isset($_SESSION['erro_login_votacao'])) {
                echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 text-sm' role='alert'>" . htmlspecialchars($_SESSION['erro_login_votacao']) . "</div>";
                unset($_SESSION['erro_login_votacao']);
            }
            if ($erro_eleicao) {
                 echo "<div class='bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 text-sm' role='alert'>" . htmlspecialchars($erro_eleicao) . "</div>";
            }
            ?>

            <?php if (!$erro_eleicao || !empty($eleicoes_em_votacao)): ?>
            <form action="processa_login_votacao.php" method="POST" class="mt-8 space-y-6">

                <?php if (count($eleicoes_em_votacao) > 1 && !$eleicao_id_selecionada): ?>
                    <div>
                        <label for="eleicao_id" class="block text-sm font-medium text-gray-700 mb-1">Selecione a Eleição:</label>
                        <select id="eleicao_id" name="eleicao_id" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
                            <option value="">-- Escolha uma eleição --</option>
                            <?php foreach ($eleicoes_em_votacao as $eleicao): ?>
                                <option value="<?php echo $eleicao['id']; ?>" <?php echo ($eleicao_id_selecionada == $eleicao['id']) ? 'selected' : ''; ?>>
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
                    <label for="cpf" class="block text-sm font-medium text-gray-700 mb-1">CPF:</label>
                    <input type="text" id="cpf" name="cpf" required
                           class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm"
                           placeholder="Seu CPF (somente números)">
                </div>

                <div>
                    <label for="data_nascimento" class="block text-sm font-medium text-gray-700 mb-1">Data de Nascimento:</label>
                    <input type="date" id="data_nascimento" name="data_nascimento" required
                           class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
                </div>

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-roxo-principal hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-roxo-principal"
                            <?php if (empty($eleicoes_em_votacao) || (count($eleicoes_em_votacao) > 1 && !$eleicao_id_selecionada && empty($_POST['eleicao_id']) ) ) echo 'disabled class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-400 cursor-not-allowed"'; ?> >
                        Acessar Urna
                    </button>
                </div>
            </form>
            <?php endif; ?>

            <p class="mt-6 text-center text-sm">
                <a href="../../index.php" class="font-medium text-gray-600 hover:text-roxo-principal">
                    &larr; Voltar para a página inicial
                </a>
            </p>
        </div>
    </div>
</body>
</html>

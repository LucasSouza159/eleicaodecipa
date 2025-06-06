<?php
require_once '../../scripts/auth_votacao.php';
require_once '../../scripts/db_connection.php';

$eleicao_id = $votacao_eleicao_id_logado;
$nome_funcionario = $votacao_nome_funcionario_logado;

$eleicao_info = null;
$candidatos_aprovados = [];
$erro_urna = null;

if(isset($pdo)){
    try {
        $stmt_eleicao = $pdo->prepare("SELECT titulo_eleicao, ano_referencia, status_eleicao FROM eleicoes WHERE id = ?");
        $stmt_eleicao->execute([$eleicao_id]);
        $eleicao_info = $stmt_eleicao->fetch(PDO::FETCH_ASSOC);

        if (!$eleicao_info) {
            throw new Exception("Detalhes da eleição não encontrados.");
        }
        if ($eleicao_info['status_eleicao'] !== 'Em Votação') {
            unset($_SESSION['votacao_funcionario_id'], $_SESSION['votacao_eleicao_id'], $_SESSION['votacao_nome_funcionario']);
            $_SESSION['erro_login_votacao'] = "Esta eleição não está mais aberta para votação.";
            header("Location: login_votacao.php");
            exit();
        }

        $stmt_candidatos = $pdo->prepare(
            "SELECT c.id AS candidato_id, c.numero_candidato, c.nome_urna, f.nome_completo AS funcionario_nome, f.cargo
             FROM candidatos c
             JOIN funcionarios f ON c.funcionario_id = f.id
             WHERE c.eleicao_id = ? AND c.status_candidatura = 'Aprovado'
             ORDER BY c.numero_candidato ASC, c.nome_urna ASC"
        );
        $stmt_candidatos->execute([$eleicao_id]);
        $candidatos_aprovados = $stmt_candidatos->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        error_log("Erro na urna (Eleição ID: $eleicao_id, Funcionário ID: $votacao_funcionario_id_logado): " . $e->getMessage());
        $erro_urna = "Ocorreu um erro ao carregar a urna: " . $e->getMessage();
    }
} else {
    $erro_urna = "Erro crítico: A conexão com o banco de dados não está disponível.";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Urna de Votação | CIPA Fácil</title>
    <link href="../../src/styles/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto px-4 py-8 max-w-5xl">
        <header class="bg-white shadow-lg rounded-xl p-6 mb-8">
            <div class="text-center">
                <?php if ($eleicao_info): ?>
                    <h1 class="text-3xl font-bold text-roxo-principal"><?php echo htmlspecialchars($eleicao_info['titulo_eleicao']); ?></h1>
                    <p class="text-xl text-cinza-chumbo">Ano de Referência: <?php echo htmlspecialchars($eleicao_info['ano_referencia']); ?></p>
                <?php endif; ?>
                <p class="text-md text-gray-700 mt-3">Bem-vindo(a) à urna de votação, <strong class="font-semibold"><?php echo htmlspecialchars($nome_funcionario); ?></strong>.</p>
                <p class="text-sm text-gray-500">Por favor, selecione o candidato de sua preferência ou vote em branco/nulo.</p>
            </div>
        </header>

        <?php
        if (isset($_SESSION['erro_urna'])) {
            echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md text-sm' role='alert'>" . htmlspecialchars($_SESSION['erro_urna']) . "</div>";
            unset($_SESSION['erro_urna']);
        }
        if ($erro_urna): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md text-center" role="alert">
                <?php echo htmlspecialchars($erro_urna); ?>
                <p class="mt-2"><a href="logout_votacao.php" class="font-medium text-red-600 hover:text-red-800 underline">Sair da Votação</a></p>
            </div>
        <?php endif; ?>

        <?php if (!$erro_urna && empty($candidatos_aprovados)): ?>
            <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 p-6 mb-6 rounded-md text-center shadow">
                <p class="text-lg font-medium">Não há candidatos aprovados para esta eleição no momento.</p>
                <p class="mt-1">Você ainda pode votar em Branco ou Nulo utilizando os botões abaixo.</p>
            </div>
        <?php endif; ?>

        <?php if (!$erro_urna): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-10">
                <?php foreach ($candidatos_aprovados as $candidato): ?>
                    <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-200 hover:shadow-2xl transition-all duration-300 flex flex-col justify-between">
                        <div class="text-center">
                            <div class="w-24 h-24 bg-gray-300 rounded-full mx-auto mb-3 flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                <!-- TODO: Substituir por foto do candidato se disponível -->
                            </div>
                            <p class="text-3xl font-bold text-roxo-principal mb-1"><?php echo htmlspecialchars($candidato['numero_candidato'] ?? '--'); ?></p>
                            <h3 class="text-lg font-semibold text-cinza-chumbo truncate" title="<?php echo htmlspecialchars($candidato['nome_urna'] ?? $candidato['funcionario_nome']); ?>">
                                <?php echo htmlspecialchars($candidato['nome_urna'] ?? $candidato['funcionario_nome']); ?>
                            </h3>
                            <p class="text-xs text-gray-500 truncate" title="<?php echo htmlspecialchars($candidato['funcionario_nome']); ?>"><?php echo htmlspecialchars($candidato['funcionario_nome']); ?></p>
                            <?php if (!empty($candidato['cargo'])): ?>
                                <p class="text-xs text-gray-500 italic truncate" title="<?php echo htmlspecialchars($candidato['cargo']); ?>"><?php echo htmlspecialchars($candidato['cargo']); ?></p>
                            <?php endif; ?>
                        </div>
                        <a href="confirmar_voto.php?candidato_id=<?php echo $candidato['candidato_id']; ?>"
                           class="mt-5 block w-full bg-verde-cipa hover:bg-green-600 text-white font-bold py-2.5 px-4 rounded-lg text-center transition-colors duration-300 shadow-md hover:shadow-lg">
                            VOTAR <?php echo htmlspecialchars($candidato['numero_candidato'] ?? ''); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <hr class="my-10 border-gray-300">

            <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-8">
                <a href="confirmar_voto.php?voto_especial=branco"
                   class="w-full sm:w-auto bg-gray-200 hover:bg-gray-300 text-cinza-chumbo font-semibold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 text-center text-lg">
                    Votar em Branco
                </a>
                <a href="confirmar_voto.php?voto_especial=nulo"
                   class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 text-center text-lg">
                    Votar Nulo
                </a>
            </div>
        <?php endif; ?>

        <div class="mt-12 text-center">
            <a href="logout_votacao.php" class="text-sm text-gray-600 hover:text-roxo-principal underline">
                Cancelar Votação e Sair
            </a>
        </div>
    </div>
    <footer class="text-center p-4 mt-4 text-sm text-gray-500">
        &copy; <?php echo date("Y"); ?> CIPA Fácil Online. Ambiente de votação seguro.
    </footer>
</body>
</html>

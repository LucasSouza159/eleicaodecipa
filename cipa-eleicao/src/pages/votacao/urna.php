<?php
require_once '../../scripts/auth_votacao.php'; // Garante autenticação e define variáveis de sessão
require_once '../../scripts/db_connection.php';

$eleicao_id = $votacao_eleicao_id_logado;
$nome_funcionario = $votacao_nome_funcionario_logado;

$eleicao_info = null;
$candidatos_aprovados = [];
$erro_urna = null;

try {
    // Buscar informações da eleição
    $stmt_eleicao = $pdo->prepare("SELECT titulo_eleicao, ano_referencia, status_eleicao FROM eleicoes WHERE id = ?");
    $stmt_eleicao->execute([$eleicao_id]);
    $eleicao_info = $stmt_eleicao->fetch(PDO::FETCH_ASSOC);

    if (!$eleicao_info) {
        throw new Exception("Detalhes da eleição não encontrados.");
    }
    if ($eleicao_info['status_eleicao'] !== 'Em Votação') {
        // Destruir sessão de votação e redirecionar se a eleição não está mais em votação
        unset($_SESSION['votacao_funcionario_id'], $_SESSION['votacao_eleicao_id'], $_SESSION['votacao_nome_funcionario']);
        $_SESSION['erro_login_votacao'] = "Esta eleição não está mais aberta para votação.";
        header("Location: login_votacao.php");
        exit();
    }

    // Buscar candidatos aprovados para esta eleição
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
    error_log("Erro na urna de votação (Eleição ID: $eleicao_id, Funcionário ID: $votacao_funcionario_id_logado): " . $e->getMessage());
    $erro_urna = "Ocorreu um erro ao carregar a urna de votação: " . $e->getMessage();
    // Considerar redirecionar ou mostrar mensagem mais genérica
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Urna de Votação CIPA - CIPA Fácil</title>
    <link href="../../src/styles/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto p-4 md:p-8 max-w-4xl">
        <header class="text-center mb-8">
            <?php if ($eleicao_info): ?>
                <h1 class="text-3xl font-bold text-roxo-principal"><?php echo htmlspecialchars($eleicao_info['titulo_eleicao']); ?></h1>
                <p class="text-xl text-cinza-chumbo">Ano de Referência: <?php echo htmlspecialchars($eleicao_info['ano_referencia']); ?></p>
            <?php endif; ?>
            <p class="text-md text-gray-700 mt-2">Bem-vindo(a), <strong class="font-semibold"><?php echo htmlspecialchars($nome_funcionario); ?></strong>. Escolha seu candidato.</p>
        </header>

        <?php if ($erro_urna): ?>
            <div class="p-4 mb-6 text-center text-red-700 bg-red-100 border border-red-400 rounded-lg" role="alert">
                <?php echo htmlspecialchars($erro_urna); ?>
                <p class="mt-2"><a href="logout_votacao.php" class="font-medium text-red-700 hover:text-red-900">Sair da Votação</a></p>
            </div>
        <?php endif; ?>

        <?php if (!$erro_urna && empty($candidatos_aprovados)): ?>
            <div class="p-4 mb-6 text-center text-yellow-700 bg-yellow-100 border border-yellow-400 rounded-lg" role="alert">
                Não há candidatos aprovados para esta eleição no momento.
                 <p class="mt-2">Você ainda pode votar em Branco ou Nulo.</p>
            </div>
        <?php endif; ?>

        <?php if (!$erro_urna): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-8">
                <?php foreach ($candidatos_aprovados as $candidato): ?>
                    <div class="bg-white p-5 rounded-lg shadow-lg border border-gray-200 hover:shadow-xl transition-shadow">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-roxo-principal"><?php echo htmlspecialchars($candidato['numero_candidato'] ?? '--'); ?></p>
                            <h3 class="text-xl font-semibold text-cinza-chumbo mt-1"><?php echo htmlspecialchars($candidato['nome_urna'] ?? $candidato['funcionario_nome']); ?></h3>
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($candidato['funcionario_nome']); ?></p>
                            <?php if (!empty($candidato['cargo'])): ?>
                                <p class="text-xs text-gray-500 italic"><?php echo htmlspecialchars($candidato['cargo']); ?></p>
                            <?php endif; ?>
                        </div>
                        <a href="confirmar_voto.php?candidato_id=<?php echo $candidato['candidato_id']; ?>"
                           class="mt-4 block w-full bg-verde-cipa hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md text-center transition-colors">
                            Votar <?php echo htmlspecialchars($candidato['numero_candidato'] ?? ''); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <hr class="my-8 border-gray-300">

            <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-6">
                <a href="confirmar_voto.php?voto_especial=branco"
                   class="w-full sm:w-auto bg-gray-200 hover:bg-gray-300 text-cinza-chumbo font-semibold py-3 px-6 rounded-lg shadow-md transition-colors text-center">
                    Votar em Branco
                </a>
                <a href="confirmar_voto.php?voto_especial=nulo"
                   class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition-colors text-center">
                    Votar Nulo
                </a>
            </div>
        <?php endif; ?>

        <div class="mt-10 text-center">
            <a href="logout_votacao.php" class="text-sm text-gray-600 hover:text-roxo-principal">Cancelar e Sair</a>
        </div>
    </div>
</body>
</html>

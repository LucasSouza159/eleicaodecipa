<?php
require_once '../../scripts/auth_comissao.php';
require_once '../../scripts/db_connection.php';

$eleicao_id = $comissao_eleicao_id_logado;
$eleicao_info = null;
$resultados = ['candidatos' => [], 'branco' => 0, 'nulo' => 0, 'total_votantes' => 0];
$erro_apuracao = null;

try {
    // Buscar informações e status da eleição
    $stmt_eleicao = $pdo->prepare("SELECT titulo_eleicao, ano_referencia, status_eleicao, numero_titulares_previstos, numero_suplentes_previstos FROM eleicoes WHERE id = ?");
    $stmt_eleicao->execute([$eleicao_id]);
    $eleicao_info = $stmt_eleicao->fetch(PDO::FETCH_ASSOC);

    if (!$eleicao_info) {
        throw new Exception("Detalhes da eleição não encontrados.");
    }

    $status_permitidos_apuracao = ['Votação Encerrada', 'Em Apuração', 'Resultados Publicados', 'Finalizada'];

    if (in_array($eleicao_info['status_eleicao'], $status_permitidos_apuracao)) {
        // Contar total de votantes
        $stmt_total = $pdo->prepare("SELECT COUNT(id) AS total FROM votos WHERE eleicao_id = ?");
        $stmt_total->execute([$eleicao_id]);
        $resultados['total_votantes'] = $stmt_total->fetchColumn();

        // Contar votos em branco
        $stmt_branco = $pdo->prepare("SELECT COUNT(id) AS total FROM votos WHERE eleicao_id = ? AND tipo_voto_especial = 'Branco'");
        $stmt_branco->execute([$eleicao_id]);
        $resultados['branco'] = $stmt_branco->fetchColumn();

        // Contar votos nulos
        $stmt_nulo = $pdo->prepare("SELECT COUNT(id) AS total FROM votos WHERE eleicao_id = ? AND tipo_voto_especial = 'Nulo'");
        $stmt_nulo->execute([$eleicao_id]);
        $resultados['nulo'] = $stmt_nulo->fetchColumn();

        // Contar votos por candidato aprovado
        $stmt_votos_candidatos = $pdo->prepare(
            "SELECT c.id AS candidato_id, c.nome_urna, f.nome_completo AS funcionario_nome, COUNT(v.id) AS total_votos
             FROM candidatos c
             JOIN funcionarios f ON c.funcionario_id = f.id
             LEFT JOIN votos v ON c.id = v.candidato_id AND v.eleicao_id = c.eleicao_id
             WHERE c.eleicao_id = ? AND c.status_candidatura = 'Aprovado'
             GROUP BY c.id, c.nome_urna, f.nome_completo
             ORDER BY total_votos DESC, c.nome_urna ASC"
        );
        $stmt_votos_candidatos->execute([$eleicao_id]);
        $resultados['candidatos'] = $stmt_votos_candidatos->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (Exception $e) {
    error_log("Erro na apuração de resultados (Eleição ID: $eleicao_id): " . $e->getMessage());
    $erro_apuracao = "Ocorreu um erro ao processar a apuração: " . $e->getMessage();
}

$mensagem_sucesso = $_SESSION['mensagem_sucesso_apuracao'] ?? null;
unset($_SESSION['mensagem_sucesso_apuracao']);
$mensagem_erro = $_SESSION['mensagem_erro_apuracao'] ?? null;
unset($_SESSION['mensagem_erro_apuracao']);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apuração e Resultados - CIPA Fácil</title>
    <link href="../../src/styles/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

    <nav class="bg-azul-cipa text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="text-xl font-bold">Comissão: <?php echo htmlspecialchars($comissao_eleicao_titulo_logado); ?></div>
                <div>
                    <span class="text-sm mr-4"><?php echo htmlspecialchars($comissao_nome_membro_logado . " - " . $comissao_papel_logado); ?></span>
                    <a href="logout_comissao.php" class="px-4 py-2 bg-red-500 hover:bg-red-600 rounded-md text-sm font-medium">Sair</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto p-6 mt-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-cinza-chumbo">Apuração e Resultados da Eleição</h2>
            <a href="painel_comissao.php" class="text-azul-cipa hover:text-blue-700">&larr; Voltar ao Painel</a>
        </div>

        <?php if ($eleicao_info): ?>
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h3 class="text-xl font-semibold text-azul-cipa mb-2"><?php echo htmlspecialchars($eleicao_info['titulo_eleicao']); ?></h3>
                <p class="text-gray-700">Ano de Referência: <?php echo htmlspecialchars($eleicao_info['ano_referencia']); ?></p>
                <p class="text-gray-700">Status Atual: <span class="font-semibold <?php
                    switch ($eleicao_info['status_eleicao']) {
                        case 'Em Votação': echo 'text-yellow-600'; break;
                        case 'Votação Encerrada': case 'Em Apuração': echo 'text-red-600'; break;
                        case 'Resultados Publicados': case 'Finalizada': echo 'text-green-600'; break;
                        default: echo 'text-gray-600';
                    }
                ?>"><?php echo htmlspecialchars($eleicao_info['status_eleicao']); ?></span></p>
            </div>
        <?php endif; ?>

        <?php
        if ($mensagem_sucesso) {
            echo "<div class='p-4 mb-4 text-sm text-green-700 bg-green-100 border border-green-400 rounded-lg' role='alert'>" . htmlspecialchars($mensagem_sucesso) . "</div>";
        }
        if ($mensagem_erro) {
            echo "<div class='p-4 mb-4 text-sm text-red-700 bg-red-100 border border-red-400 rounded-lg' role='alert'>" . htmlspecialchars($mensagem_erro) . "</div>";
        }
        if ($erro_apuracao) {
            echo "<div class='p-4 mb-4 text-sm text-red-700 bg-red-100 border border-red-400 rounded-lg' role='alert'>" . htmlspecialchars($erro_apuracao) . "</div>";
        }
        ?>

        <?php if ($eleicao_info && $eleicao_info['status_eleicao'] === 'Em Votação'): ?>
            <div class="bg-white p-6 rounded-lg shadow-md text-center mb-6">
                <p class="text-lg text-gray-700 mb-4">A votação para esta eleição ainda está em andamento.</p>
                <form action="processa_encerrar_votacao.php" method="POST" onsubmit="return confirm('Tem certeza que deseja encerrar a votação e iniciar a apuração? Esta ação não pode ser desfeita.');">
                    <input type="hidden" name="eleicao_id" value="<?php echo $eleicao_id; ?>">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition-colors">
                        Encerrar Votação e Iniciar Apuração
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($eleicao_info && in_array($eleicao_info['status_eleicao'], $status_permitidos_apuracao)): ?>
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h3 class="text-xl font-semibold text-azul-cipa mb-4">Resultados da Votação</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 text-center">
                    <div class="p-4 bg-gray-100 rounded-lg shadow">
                        <p class="text-2xl font-bold text-roxo-principal"><?php echo $resultados['total_votantes']; ?></p>
                        <p class="text-gray-600">Total de Votantes</p>
                    </div>
                    <div class="p-4 bg-gray-100 rounded-lg shadow">
                        <p class="text-2xl font-bold text-roxo-principal"><?php echo $resultados['branco']; ?></p>
                        <p class="text-gray-600">Votos em Branco</p>
                    </div>
                    <div class="p-4 bg-gray-100 rounded-lg shadow">
                        <p class="text-2xl font-bold text-roxo-principal"><?php echo $resultados['nulo']; ?></p>
                        <p class="text-gray-600">Votos Nulos</p>
                    </div>
                </div>

                <h4 class="text-lg font-semibold text-cinza-chumbo mb-3">Votos por Candidato:</h4>
                <?php if (empty($resultados['candidatos'])): ?>
                    <p class="text-gray-600">Nenhum voto computado para candidatos aprovados.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full leading-normal">
                            <thead>
                                <tr>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pos.</th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Candidato (Nome na Urna)</th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nome Completo</th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Votos</th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Classificação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $posicao = 1;
                                $titulares_cont = 0;
                                $suplentes_cont = 0;
                                $num_titulares = $eleicao_info['numero_titulares_previstos'] ?? 0;
                                $num_suplentes = $eleicao_info['numero_suplentes_previstos'] ?? 0;

                                foreach ($resultados['candidatos'] as $idx => $candidato):
                                    $classificacao = '';
                                    $cor_class = 'text-gray-700';
                                    if ($num_titulares > 0 && $titulares_cont < $num_titulares) {
                                        $classificacao = 'Titular';
                                        $cor_class = 'text-green-600 font-semibold';
                                        $titulares_cont++;
                                    } elseif ($num_suplentes > 0 && $suplentes_cont < $num_suplentes) {
                                        $classificacao = 'Suplente';
                                        $cor_class = 'text-yellow-600 font-semibold';
                                        $suplentes_cont++;
                                    }
                                ?>
                                <tr>
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-center"><?php echo $posicao++; ?></td>
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm"><p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($candidato['nome_urna'] ?? $candidato['funcionario_nome']); ?></p></td>
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm"><p class="text-gray-600 whitespace-no-wrap"><?php echo htmlspecialchars($candidato['funcionario_nome']); ?></p></td>
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-center"><p class="text-gray-900 font-bold whitespace-no-wrap"><?php echo $candidato['total_votos']; ?></p></td>
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm"><span class="<?php echo $cor_class; ?>"><?php echo $classificacao; ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                     <p class="text-xs text-gray-500 mt-1">Obs: A classificação em Titulares e Suplentes é baseada nos números previstos (Titulares: <?php echo $num_titulares; ?>, Suplentes: <?php echo $num_suplentes; ?>) e pode necessitar de critérios de desempate não aplicados aqui.</p>
                <?php endif; ?>
            </div>

            <?php if ($eleicao_info && in_array($eleicao_info['status_eleicao'], ['Votação Encerrada', 'Em Apuração'])): ?>
                <div class="bg-white p-6 rounded-lg shadow-md text-center mt-6">
                    <form action="processa_publicar_resultados.php" method="POST" onsubmit="return confirm('Tem certeza que deseja publicar os resultados? Após publicados, eles podem se tornar visíveis para outros perfis (ex: funcionários).');">
                        <input type="hidden" name="eleicao_id" value="<?php echo $eleicao_id; ?>">
                        <button type="submit" class="bg-azul-cipa hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition-colors">
                            Publicar Resultados
                        </button>
                    </form>
                </div>
            <?php elseif ($eleicao_info && in_array($eleicao_info['status_eleicao'], ['Resultados Publicados', 'Finalizada'])): ?>
                 <div class="p-4 my-6 text-sm text-center text-green-700 bg-green-100 border border-green-400 rounded-lg" role="alert">
                    Os resultados desta eleição já foram publicados.
                </div>
            <?php endif; ?>

        <?php elseif ($eleicao_info && $eleicao_info['status_eleicao'] !== 'Em Votação'): ?>
             <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <p class="text-gray-700">A apuração para esta eleição ainda não pode ser iniciada ou já foi concluída (Status: <?php echo htmlspecialchars($eleicao_info['status_eleicao']); ?>).</p>
            </div>
        <?php endif; ?>
    </main>

    <footer class="text-center p-4 mt-8 text-sm text-gray-500">
        &copy; <?php echo date("Y"); ?> CIPA Fácil Online.
    </footer>
</body>
</html>

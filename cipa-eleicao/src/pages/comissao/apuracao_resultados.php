<?php
$titulo_pagina_comissao = "Apuração e Resultados";
require_once 'includes/header_painel_comissao.php';
// auth_comissao, db_connection, $comissao_eleicao_id_logado, $comissao_eleicao_titulo_logado já incluídos/definidos

$eleicao_id = $comissao_eleicao_id_logado;
$eleicao_info = null;
$resultados = ['candidatos' => [], 'branco' => 0, 'nulo' => 0, 'total_votantes' => 0];
$erro_apuracao = null;

if(isset($pdo)){
    try {
        $stmt_eleicao = $pdo->prepare("SELECT titulo_eleicao, ano_referencia, status_eleicao, numero_titulares_previstos, numero_suplentes_previstos FROM eleicoes WHERE id = ?");
        $stmt_eleicao->execute([$eleicao_id]);
        $eleicao_info = $stmt_eleicao->fetch(PDO::FETCH_ASSOC);

        if (!$eleicao_info) {
            throw new Exception("Detalhes da eleição não encontrados.");
        }

        $status_permitidos_apuracao = ['Votação Encerrada', 'Em Apuração', 'Resultados Publicados', 'Finalizada'];

        if (in_array($eleicao_info['status_eleicao'], $status_permitidos_apuracao)) {
            $stmt_total = $pdo->prepare("SELECT COUNT(id) AS total FROM votos WHERE eleicao_id = ?");
            $stmt_total->execute([$eleicao_id]);
            $resultados['total_votantes'] = $stmt_total->fetchColumn();

            $stmt_branco = $pdo->prepare("SELECT COUNT(id) AS total FROM votos WHERE eleicao_id = ? AND tipo_voto_especial = 'Branco'");
            $stmt_branco->execute([$eleicao_id]);
            $resultados['branco'] = $stmt_branco->fetchColumn();

            $stmt_nulo = $pdo->prepare("SELECT COUNT(id) AS total FROM votos WHERE eleicao_id = ? AND tipo_voto_especial = 'Nulo'");
            $stmt_nulo->execute([$eleicao_id]);
            $resultados['nulo'] = $stmt_nulo->fetchColumn();

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
        $_SESSION['mensagem_erro_comissao_global'] = "Ocorreu um erro ao processar a apuração: " . $e->getMessage();
    }
}

$mensagem_sucesso = $_SESSION['mensagem_sucesso_apuracao'] ?? null;
unset($_SESSION['mensagem_sucesso_apuracao']);
$mensagem_erro = $_SESSION['mensagem_erro_apuracao'] ?? null; // Erros específicos da página
unset($_SESSION['mensagem_erro_apuracao']);

?>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
    <h1 class="text-2xl sm:text-3xl font-bold text-cinza-chumbo mb-4 sm:mb-0">
        Apuração e Resultados da Eleição
    </h1>
    <!-- Link para voltar ao painel já está no header_painel_comissao -->
</div>


<?php if ($eleicao_info): ?>
    <div class="bg-white p-6 rounded-xl shadow-lg mb-6">
        <h2 class="text-xl font-semibold text-azul-cipa mb-2"><?php echo htmlspecialchars($eleicao_info['titulo_eleicao']); ?></h2>
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
// Mensagens de sucesso/erro específicas desta página ou de processamentos relacionados
if ($mensagem_sucesso) {
    echo "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6' role='alert'><p>" . htmlspecialchars($mensagem_sucesso) . "</p></div>";
}
if ($mensagem_erro) {
    echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6' role='alert'><p>" . htmlspecialchars($mensagem_erro) . "</p></div>";
}
?>

<?php if ($eleicao_info && $eleicao_info['status_eleicao'] === 'Em Votação'): ?>
    <div class="bg-white p-6 rounded-xl shadow-lg text-center mb-6">
        <p class="text-lg text-gray-700 mb-4">A votação para esta eleição ainda está em andamento.</p>
        <form action="processa_encerrar_votacao.php" method="POST" onsubmit="return confirm('Tem certeza que deseja encerrar a votação e iniciar a apuração? Esta ação não pode ser desfeita.');">
            <input type="hidden" name="eleicao_id" value="<?php echo $eleicao_id; ?>">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition-colors">
                Encerrar Votação e Iniciar Apuração
            </button>
        </form>
    </div>
<?php endif; ?>

<?php if ($eleicao_info && in_array($eleicao_info['status_eleicao'], ['Votação Encerrada', 'Em Apuração', 'Resultados Publicados', 'Finalizada'])): ?>
    <div class="bg-white p-6 rounded-xl shadow-lg mb-6">
        <h2 class="text-xl font-semibold text-azul-cipa mb-4">Resultados da Votação</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 text-center">
            <div class="p-4 bg-gray-50 rounded-lg shadow-md">
                <p class="text-3xl font-bold text-roxo-principal"><?php echo $resultados['total_votantes']; ?></p>
                <p class="text-gray-600">Total de Votantes</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg shadow-md">
                <p class="text-3xl font-bold text-roxo-principal"><?php echo $resultados['branco']; ?></p>
                <p class="text-gray-600">Votos em Branco</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg shadow-md">
                <p class="text-3xl font-bold text-roxo-principal"><?php echo $resultados['nulo']; ?></p>
                <p class="text-gray-600">Votos Nulos</p>
            </div>
        </div>

        <h3 class="text-lg font-semibold text-cinza-chumbo mb-3">Votos por Candidato:</h3>
        <?php if (empty($resultados['candidatos'])): ?>
            <p class="text-gray-600 italic">Nenhum voto computado para candidatos aprovados ou nenhum candidato aprovado.</p>
        <?php else: ?>
            <div class="overflow-x-auto rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Pos.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Candidato (Nome Urna)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider hidden sm:table-cell">Nome Completo</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Votos</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Classificação</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        $posicao = 1;
                        $titulares_cont = 0;
                        $suplentes_cont = 0;
                        $num_titulares = $eleicao_info['numero_titulares_previstos'] ?? 0;
                        $num_suplentes = $eleicao_info['numero_suplentes_previstos'] ?? 0;

                        foreach ($resultados['candidatos'] as $candidato):
                            $classificacao = '-';
                            $cor_class = 'text-gray-700';
                            if ($candidato['total_votos'] > 0) { // Só classifica quem teve voto, ou ajustar regra
                                if ($num_titulares > 0 && $titulares_cont < $num_titulares) {
                                    $classificacao = 'Titular';
                                    $cor_class = 'text-green-600 font-semibold';
                                    $titulares_cont++;
                                } elseif ($num_suplentes > 0 && $suplentes_cont < $num_suplentes) {
                                    $classificacao = 'Suplente';
                                    $cor_class = 'text-yellow-600 font-semibold';
                                    $suplentes_cont++;
                                } else {
                                     $classificacao = 'Não Eleito';
                                }
                            }
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center"><?php echo $posicao++; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($candidato['nome_urna'] ?? $candidato['funcionario_nome']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell"><?php echo htmlspecialchars($candidato['funcionario_nome']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold text-center"><?php echo $candidato['total_votos']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm <?php echo $cor_class; ?>"><?php echo $classificacao; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if($num_titulares > 0 || $num_suplentes > 0): ?>
            <p class="text-xs text-gray-500 mt-2">Obs: A classificação é baseada nos números previstos (Titulares: <?php echo $num_titulares; ?>, Suplentes: <?php echo $num_suplentes; ?>) e pode necessitar de critérios de desempate (NR 5 item 5.4.11) não aplicados automaticamente aqui.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php if ($eleicao_info && in_array($eleicao_info['status_eleicao'], ['Votação Encerrada', 'Em Apuração'])): ?>
        <div class="bg-white p-6 rounded-xl shadow-lg text-center mt-6">
            <form action="processa_publicar_resultados.php" method="POST" onsubmit="return confirm('Tem certeza que deseja publicar os resultados? Esta ação pode notificar os envolvidos.');">
                <input type="hidden" name="eleicao_id" value="<?php echo $eleicao_id; ?>">
                <button type="submit" class="bg-azul-cipa hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition-colors">
                    Publicar Resultados
                </button>
            </form>
        </div>
    <?php elseif ($eleicao_info && in_array($eleicao_info['status_eleicao'], ['Resultados Publicados', 'Finalizada'])): ?>
         <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 my-6 rounded-md text-sm text-center" role="alert">
            Os resultados desta eleição já foram publicados.
        </div>
    <?php endif; ?>

<?php elseif ($eleicao_info && $eleicao_info['status_eleicao'] !== 'Em Votação'): ?>
     <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 my-6 rounded-md text-sm" role="alert">
        A apuração para esta eleição ainda não pode ser iniciada ou o status da eleição não permite visualização de resultados parciais (Status: <?php echo htmlspecialchars($eleicao_info['status_eleicao']); ?>).
    </div>
<?php endif; ?>

<?php
require_once 'includes/footer_painel_comissao.php';
?>

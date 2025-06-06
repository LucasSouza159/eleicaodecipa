<?php
$titulo_pagina = "Criar Nova Eleição";
require_once 'includes/header_painel.php';
// auth_empresa e db_connection já incluídos em header_painel.php
// $empresa_id já está disponível

$erros = $_SESSION['erros_criar_eleicao'] ?? [];
$dados_formulario = $_SESSION['dados_formulario_eleicao'] ?? [];

unset($_SESSION['erros_criar_eleicao']);
unset($_SESSION['dados_formulario_eleicao']);

// TODO: Popular dinamicamente o dropdown de filiais
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl sm:text-3xl font-bold text-cinza-chumbo">
        Criar Nova Eleição
    </h1>
    <a href="gerenciar_eleicoes.php" class="text-roxo-principal hover:text-purple-700 font-medium">
        &larr; Voltar para Gerenciar Eleições
    </a>
</div>

<div class="bg-white p-6 sm:p-8 rounded-xl shadow-2xl max-w-3xl mx-auto">
    <?php if (!empty($erros)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
            <p class="font-bold">Foram encontrados os seguintes erros:</p>
            <ul class="list-disc pl-5 mt-2 text-sm">
                <?php foreach ($erros as $erro): ?>
                    <li><?php echo htmlspecialchars($erro); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php
    // Mensagem de erro do DB vinda do processamento
    if (isset($_SESSION['mensagem_erro_db'])) {
        echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md text-sm' role='alert'>" . htmlspecialchars($_SESSION['mensagem_erro_db']) . "</div>";
        unset($_SESSION['mensagem_erro_db']);
    }
    ?>

    <form action="processa_criar_eleicao.php" method="POST" class="space-y-6">

        <div>
            <label for="titulo_eleicao" class="block text-sm font-medium text-gray-700 mb-1">Título da Eleição:</label>
            <input type="text" id="titulo_eleicao" name="titulo_eleicao" value="<?php echo htmlspecialchars($dados_formulario['titulo_eleicao'] ?? ''); ?>" required
                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="ano_referencia" class="block text-sm font-medium text-gray-700 mb-1">Ano de Referência (YYYY):</label>
                <input type="number" id="ano_referencia" name="ano_referencia" value="<?php echo htmlspecialchars($dados_formulario['ano_referencia'] ?? date('Y')); ?>" required min="2000" max="2100"
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
            </div>
            <div>
                <label for="filial_id" class="block text-sm font-medium text-gray-700 mb-1">Filial (Opcional - ID da Filial):</label>
                <input type="text" id="filial_id" name="filial_id" placeholder="Deixe em branco se for para a empresa toda" value="<?php echo htmlspecialchars($dados_formulario['filial_id'] ?? ''); ?>"
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
                <p class="mt-1 text-xs text-gray-500">// TODO: Substituir por dropdown dinâmico de filiais.</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="numero_titulares_previstos" class="block text-sm font-medium text-gray-700 mb-1">Nº de Titulares Previstos:</label>
                <input type="number" id="numero_titulares_previstos" name="numero_titulares_previstos" value="<?php echo htmlspecialchars($dados_formulario['numero_titulares_previstos'] ?? '0'); ?>" required min="0"
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
            </div>
            <div>
                <label for="numero_suplentes_previstos" class="block text-sm font-medium text-gray-700 mb-1">Nº de Suplentes Previstos:</label>
                <input type="number" id="numero_suplentes_previstos" name="numero_suplentes_previstos" value="<?php echo htmlspecialchars($dados_formulario['numero_suplentes_previstos'] ?? '0'); ?>" required min="0"
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
            </div>
        </div>

        <div>
            <label for="descricao" class="block text-sm font-medium text-gray-700 mb-1">Descrição (Opcional):</label>
            <textarea id="descricao" name="descricao" rows="3"
                      class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm"><?php echo htmlspecialchars($dados_formulario['descricao'] ?? ''); ?></textarea>
        </div>

        <fieldset class="border border-gray-300 p-4 rounded-md">
            <legend class="text-base font-medium text-gray-900 px-2">Cronograma da Eleição:</legend>
            <div class="space-y-4 mt-2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <label for="data_convocacao" class="block text-sm font-medium text-gray-700 mb-1">Data de Convocação:</label>
                        <input type="date" id="data_convocacao" name="data_convocacao" value="<?php echo htmlspecialchars($dados_formulario['data_convocacao'] ?? ''); ?>" required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
                    </div>
                     <div>
                        <label for="data_posse_eleitos" class="block text-sm font-medium text-gray-700 mb-1">Data da Posse dos Eleitos:</label>
                        <input type="date" id="data_posse_eleitos" name="data_posse_eleitos" value="<?php echo htmlspecialchars($dados_formulario['data_posse_eleitos'] ?? ''); ?>" required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
                    </div>
                    <div>
                        <label for="data_inicio_inscricao_candidatos" class="block text-sm font-medium text-gray-700 mb-1">Início das Inscrições:</label>
                        <input type="datetime-local" id="data_inicio_inscricao_candidatos" name="data_inicio_inscricao_candidatos" value="<?php echo htmlspecialchars($dados_formulario['data_inicio_inscricao_candidatos'] ?? ''); ?>" required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
                    </div>
                    <div>
                        <label for="data_fim_inscricao_candidatos" class="block text-sm font-medium text-gray-700 mb-1">Fim das Inscrições:</label>
                        <input type="datetime-local" id="data_fim_inscricao_candidatos" name="data_fim_inscricao_candidatos" value="<?php echo htmlspecialchars($dados_formulario['data_fim_inscricao_candidatos'] ?? ''); ?>" required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
                    </div>
                    <div>
                        <label for="data_inicio_votacao" class="block text-sm font-medium text-gray-700 mb-1">Início da Votação:</label>
                        <input type="datetime-local" id="data_inicio_votacao" name="data_inicio_votacao" value="<?php echo htmlspecialchars($dados_formulario['data_inicio_votacao'] ?? ''); ?>" required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
                    </div>
                    <div>
                        <label for="data_fim_votacao" class="block text-sm font-medium text-gray-700 mb-1">Fim da Votação:</label>
                        <input type="datetime-local" id="data_fim_votacao" name="data_fim_votacao" value="<?php echo htmlspecialchars($dados_formulario['data_fim_votacao'] ?? ''); ?>" required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
                    </div>
                </div>
                <div>
                    <label for="data_apuracao" class="block text-sm font-medium text-gray-700 mb-1">Data da Apuração:</label>
                    <input type="datetime-local" id="data_apuracao" name="data_apuracao" value="<?php echo htmlspecialchars($dados_formulario['data_apuracao'] ?? ''); ?>" required
                           class="appearance-none block w-full md:w-1/2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
                </div>
            </div>
        </fieldset>

        <div>
            <label for="observacoes_gerais" class="block text-sm font-medium text-gray-700 mb-1">Observações Gerais (Opcional):</label>
            <textarea id="observacoes_gerais" name="observacoes_gerais" rows="4"
                      class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm"><?php echo htmlspecialchars($dados_formulario['observacoes_gerais'] ?? ''); ?></textarea>
        </div>

        <div class="pt-3">
            <button type="submit"
                    class="w-full sm:w-auto flex justify-center py-3 px-6 border border-transparent rounded-lg shadow-md text-base font-medium text-white bg-roxo-principal hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-roxo-principal">
                Salvar Eleição
            </button>
        </div>
    </form>
</div>

<?php
require_once 'includes/footer_painel.php';
?>

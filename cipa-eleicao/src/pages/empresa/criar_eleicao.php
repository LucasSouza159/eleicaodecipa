<?php
require_once '../../scripts/auth_empresa.php';
require_once '../../scripts/db_connection.php';

$empresa_id = $_SESSION['empresa_id'];

$erros = $_SESSION['erros_criar_eleicao'] ?? [];
$dados_formulario = $_SESSION['dados_formulario_eleicao'] ?? [];

unset($_SESSION['erros_criar_eleicao']);
unset($_SESSION['dados_formulario_eleicao']);

// TODO: Popular dinamicamente o dropdown de filiais
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Nova Eleição - CIPA Fácil</title>
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
            <h2 class="text-2xl font-semibold text-cinza-chumbo">Criar Nova Eleição</h2>
            <a href="gerenciar_eleicoes.php" class="text-roxo-principal hover:text-purple-700">&larr; Voltar para Gerenciar Eleições</a>
        </div>

        <div class="bg-white p-8 rounded-lg shadow-lg">
            <?php if (!empty($erros)): ?>
                <div class="p-4 mb-6 text-sm text-red-700 bg-red-100 border border-red-400 rounded-lg" role="alert">
                    <p class="font-bold">Foram encontrados os seguintes erros:</p>
                    <ul class="list-disc pl-5 mt-2">
                        <?php foreach ($erros as $erro): ?>
                            <li><?php echo htmlspecialchars($erro); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php
            if (isset($_SESSION['mensagem_erro_db'])) { // Erro vindo do processa_criar_eleicao.php
                echo "<div class='p-4 mb-6 text-sm text-red-700 bg-red-100 border border-red-400 rounded-lg' role='alert'>" . htmlspecialchars($_SESSION['mensagem_erro_db']) . "</div>";
                unset($_SESSION['mensagem_erro_db']);
            }
            ?>

            <form action="processa_criar_eleicao.php" method="POST" class="space-y-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="titulo_eleicao" class="block text-sm font-medium text-gray-700">Título da Eleição:</label>
                        <input type="text" id="titulo_eleicao" name="titulo_eleicao" value="<?php echo htmlspecialchars($dados_formulario['titulo_eleicao'] ?? ''); ?>" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal">
                    </div>
                    <div>
                        <label for="ano_referencia" class="block text-sm font-medium text-gray-700">Ano de Referência (YYYY):</label>
                        <input type="number" id="ano_referencia" name="ano_referencia" value="<?php echo htmlspecialchars($dados_formulario['ano_referencia'] ?? date('Y')); ?>" required min="2000" max="2100"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal">
                    </div>
                </div>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="numero_titulares_previstos" class="block text-sm font-medium text-gray-700">Nº de Titulares Previstos:</label>
                        <input type="number" id="numero_titulares_previstos" name="numero_titulares_previstos" value="<?php echo htmlspecialchars($dados_formulario['numero_titulares_previstos'] ?? '0'); ?>" required min="0"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal">
                    </div>
                    <div>
                        <label for="numero_suplentes_previstos" class="block text-sm font-medium text-gray-700">Nº de Suplentes Previstos:</label>
                        <input type="number" id="numero_suplentes_previstos" name="numero_suplentes_previstos" value="<?php echo htmlspecialchars($dados_formulario['numero_suplentes_previstos'] ?? '0'); ?>" required min="0"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal">
                    </div>
                </div>

                <div>
                    <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição (Opcional):</label>
                    <textarea id="descricao" name="descricao" rows="3"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal"><?php echo htmlspecialchars($dados_formulario['descricao'] ?? ''); ?></textarea>
                </div>

                <div>
                    <label for="filial_id" class="block text-sm font-medium text-gray-700">Filial (Opcional - ID da Filial):</label>
                    <input type="text" id="filial_id" name="filial_id" placeholder="Deixe em branco se for para a empresa toda" value="<?php echo htmlspecialchars($dados_formulario['filial_id'] ?? ''); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal">
                    <p class="text-xs text-gray-500 mt-1">// TODO: Substituir por dropdown dinâmico de filiais.</p>
                </div>

                <hr class="my-8">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Datas Importantes:</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="data_convocacao" class="block text-sm font-medium text-gray-700">Data de Convocação:</label>
                        <input type="date" id="data_convocacao" name="data_convocacao" value="<?php echo htmlspecialchars($dados_formulario['data_convocacao'] ?? ''); ?>" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal">
                    </div>
                     <div>
                        <label for="data_posse_eleitos" class="block text-sm font-medium text-gray-700">Data da Posse dos Eleitos:</label>
                        <input type="date" id="data_posse_eleitos" name="data_posse_eleitos" value="<?php echo htmlspecialchars($dados_formulario['data_posse_eleitos'] ?? ''); ?>" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="data_inicio_inscricao_candidatos" class="block text-sm font-medium text-gray-700">Início das Inscrições:</label>
                        <input type="datetime-local" id="data_inicio_inscricao_candidatos" name="data_inicio_inscricao_candidatos" value="<?php echo htmlspecialchars($dados_formulario['data_inicio_inscricao_candidatos'] ?? ''); ?>" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal">
                    </div>
                    <div>
                        <label for="data_fim_inscricao_candidatos" class="block text-sm font-medium text-gray-700">Fim das Inscrições:</label>
                        <input type="datetime-local" id="data_fim_inscricao_candidatos" name="data_fim_inscricao_candidatos" value="<?php echo htmlspecialchars($dados_formulario['data_fim_inscricao_candidatos'] ?? ''); ?>" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="data_inicio_votacao" class="block text-sm font-medium text-gray-700">Início da Votação:</label>
                        <input type="datetime-local" id="data_inicio_votacao" name="data_inicio_votacao" value="<?php echo htmlspecialchars($dados_formulario['data_inicio_votacao'] ?? ''); ?>" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal">
                    </div>
                    <div>
                        <label for="data_fim_votacao" class="block text-sm font-medium text-gray-700">Fim da Votação:</label>
                        <input type="datetime-local" id="data_fim_votacao" name="data_fim_votacao" value="<?php echo htmlspecialchars($dados_formulario['data_fim_votacao'] ?? ''); ?>" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal">
                    </div>
                </div>

                <div>
                    <label for="data_apuracao" class="block text-sm font-medium text-gray-700">Data da Apuração:</label>
                    <input type="datetime-local" id="data_apuracao" name="data_apuracao" value="<?php echo htmlspecialchars($dados_formulario['data_apuracao'] ?? ''); ?>" required
                           class="mt-1 block w-full md:w-1/2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal">
                </div>

                <hr class="my-8">

                <div>
                    <label for="observacoes_gerais" class="block text-sm font-medium text-gray-700">Observações Gerais (Opcional):</label>
                    <textarea id="observacoes_gerais" name="observacoes_gerais" rows="4"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal"><?php echo htmlspecialchars($dados_formulario['observacoes_gerais'] ?? ''); ?></textarea>
                </div>

                <div class="pt-5">
                    <button type="submit"
                            class="w-full md:w-auto flex justify-center py-3 px-6 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-roxo-principal hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-roxo-principal">
                        Salvar Eleição
                    </button>
                </div>
            </form>
        </div>
    </main>
    <footer class="text-center p-4 mt-8 text-sm text-gray-500">
        &copy; <?php echo date("Y"); ?> CIPA Fácil Online.
    </footer>
</body>
</html>

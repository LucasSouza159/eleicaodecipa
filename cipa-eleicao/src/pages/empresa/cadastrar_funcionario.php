<?php
$titulo_pagina = "Cadastrar Funcionário";
require_once 'includes/header_painel.php';
// auth_empresa e db_connection já incluídos em header_painel.php
// $empresa_id já está disponível

$erros_cadastro_funcionario = $_SESSION['erros_cadastro_funcionario'] ?? [];
$dados_formulario_funcionario = $_SESSION['dados_formulario_funcionario'] ?? [];

unset($_SESSION['erros_cadastro_funcionario']);
unset($_SESSION['dados_formulario_funcionario']);

// TODO: Popular dinamicamente o dropdown de filiais
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl sm:text-3xl font-bold text-cinza-chumbo">
        Cadastrar Novo Funcionário
    </h1>
    <a href="gerenciar_funcionarios.php" class="text-roxo-principal hover:text-purple-700 font-medium">
        &larr; Voltar para Gerenciar Funcionários
    </a>
</div>

<div class="bg-white p-6 sm:p-8 rounded-xl shadow-2xl max-w-3xl mx-auto">
    <?php if (!empty($erros_cadastro_funcionario)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
            <p class="font-bold">Foram encontrados os seguintes erros:</p>
            <ul class="list-disc pl-5 mt-2 text-sm">
                <?php foreach ($erros_cadastro_funcionario as $erro): ?>
                    <li><?php echo htmlspecialchars($erro); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="processa_cadastrar_funcionario.php" method="POST" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="nome_completo" class="block text-sm font-medium text-gray-700 mb-1">Nome Completo:</label>
                <input type="text" id="nome_completo" name="nome_completo" value="<?php echo htmlspecialchars($dados_formulario_funcionario['nome_completo'] ?? ''); ?>" required
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
            </div>
            <div>
                <label for="cpf" class="block text-sm font-medium text-gray-700 mb-1">CPF:</label>
                <input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($dados_formulario_funcionario['cpf'] ?? ''); ?>" required placeholder="XXX.XXX.XXX-XX"
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
                       <!-- TODO: Adicionar máscara de CPF -->
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="data_nascimento" class="block text-sm font-medium text-gray-700 mb-1">Data de Nascimento (Opcional):</label>
                <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($dados_formulario_funcionario['data_nascimento'] ?? ''); ?>"
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email (Opcional):</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($dados_formulario_funcionario['email'] ?? ''); ?>"
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="matricula" class="block text-sm font-medium text-gray-700 mb-1">Matrícula (Opcional):</label>
                <input type="text" id="matricula" name="matricula" value="<?php echo htmlspecialchars($dados_formulario_funcionario['matricula'] ?? ''); ?>"
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
            </div>
            <div>
                <label for="data_admissao" class="block text-sm font-medium text-gray-700 mb-1">Data de Admissão:</label>
                <input type="date" id="data_admissao" name="data_admissao" value="<?php echo htmlspecialchars($dados_formulario_funcionario['data_admissao'] ?? ''); ?>" required
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="cargo" class="block text-sm font-medium text-gray-700 mb-1">Cargo (Opcional):</label>
                <input type="text" id="cargo" name="cargo" value="<?php echo htmlspecialchars($dados_formulario_funcionario['cargo'] ?? ''); ?>"
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
            </div>
            <div>
                <label for="departamento" class="block text-sm font-medium text-gray-700 mb-1">Departamento (Opcional):</label>
                <input type="text" id="departamento" name="departamento" value="<?php echo htmlspecialchars($dados_formulario_funcionario['departamento'] ?? ''); ?>"
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="filial_id" class="block text-sm font-medium text-gray-700 mb-1">Filial (ID - Opcional):</label>
                <input type="text" id="filial_id" name="filial_id" placeholder="ID da Filial" value="<?php echo htmlspecialchars($dados_formulario_funcionario['filial_id'] ?? ''); ?>"
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
                <p class="mt-1 text-xs text-gray-500">// TODO: Substituir por dropdown dinâmico de filiais.</p>
            </div>
            <div>
                <label for="status_funcionario" class="block text-sm font-medium text-gray-700 mb-1">Status do Funcionário:</label>
                <select id="status_funcionario" name="status_funcionario" required
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal sm:text-sm">
                    <option value="Ativo" <?php echo (isset($dados_formulario_funcionario['status_funcionario']) && $dados_formulario_funcionario['status_funcionario'] == 'Ativo') ? 'selected' : ''; ?>>Ativo</option>
                    <option value="Inativo" <?php echo (isset($dados_formulario_funcionario['status_funcionario']) && $dados_formulario_funcionario['status_funcionario'] == 'Inativo') ? 'selected' : ''; ?>>Inativo</option>
                    <option value="Demitido" <?php echo (isset($dados_formulario_funcionario['status_funcionario']) && $dados_formulario_funcionario['status_funcionario'] == 'Demitido') ? 'selected' : ''; ?>>Demitido</option>
                    <option value="Afastado" <?php echo (isset($dados_formulario_funcionario['status_funcionario']) && $dados_formulario_funcionario['status_funcionario'] == 'Afastado') ? 'selected' : ''; ?>>Afastado</option>
                </select>
            </div>
        </div>

        <div class="pt-2">
            <label for="permite_votar" class="flex items-center text-sm font-medium text-gray-700">
                <input type="checkbox" id="permite_votar" name="permite_votar" value="1"
                       class="h-4 w-4 text-roxo-principal border-gray-300 rounded focus:ring-roxo-principal mr-2"
                       <?php echo (!isset($dados_formulario_funcionario['permite_votar']) || !empty($dados_formulario_funcionario['permite_votar'])) ? 'checked' : ''; ?>>
                Permite Votar nas eleições CIPA
            </label>
        </div>

        <div class="pt-5">
            <button type="submit"
                    class="w-full sm:w-auto flex justify-center py-3 px-6 border border-transparent rounded-lg shadow-md text-base font-medium text-white bg-roxo-principal hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-roxo-principal">
                Cadastrar Funcionário
            </button>
        </div>
    </form>
</div>

<?php
require_once 'includes/footer_painel.php';
?>

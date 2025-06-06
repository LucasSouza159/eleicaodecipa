<?php
require_once '../../scripts/auth_empresa.php';
require_once '../../scripts/db_connection.php';

$empresa_id = $_SESSION['empresa_id'];

$erros_cadastro_funcionario = $_SESSION['erros_cadastro_funcionario'] ?? [];
$dados_formulario_funcionario = $_SESSION['dados_formulario_funcionario'] ?? [];

unset($_SESSION['erros_cadastro_funcionario']);
unset($_SESSION['dados_formulario_funcionario']);

// TODO: Popular dinamicamente o dropdown de filiais
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Funcionário - CIPA Fácil</title>
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
            <h2 class="text-2xl font-semibold text-cinza-chumbo">Cadastrar Novo Funcionário</h2>
            <a href="gerenciar_funcionarios.php" class="text-roxo-principal hover:text-purple-700">&larr; Voltar para Gerenciar Funcionários</a>
        </div>

        <div class="bg-white p-8 rounded-lg shadow-lg">
            <?php if (!empty($erros_cadastro_funcionario)): ?>
                <div class="p-4 mb-6 text-sm text-red-700 bg-red-100 border border-red-400 rounded-lg" role="alert">
                    <p class="font-bold">Foram encontrados os seguintes erros:</p>
                    <ul class="list-disc pl-5 mt-2">
                        <?php foreach ($erros_cadastro_funcionario as $erro): ?>
                            <li><?php echo htmlspecialchars($erro); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="processa_cadastrar_funcionario.php" method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nome_completo" class="block text-sm font-medium text-gray-700">Nome Completo:</label>
                        <input type="text" id="nome_completo" name="nome_completo" value="<?php echo htmlspecialchars($dados_formulario_funcionario['nome_completo'] ?? ''); ?>" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal">
                    </div>
                    <div>
                        <label for="cpf" class="block text-sm font-medium text-gray-700">CPF:</label>
                        <input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($dados_formulario_funcionario['cpf'] ?? ''); ?>" required placeholder="XXX.XXX.XXX-XX"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal">
                               <!-- TODO: Adicionar máscara de CPF -->
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="data_nascimento" class="block text-sm font-medium text-gray-700">Data de Nascimento:</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($dados_formulario_funcionario['data_nascimento'] ?? ''); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email (Opcional):</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($dados_formulario_funcionario['email'] ?? ''); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="matricula" class="block text-sm font-medium text-gray-700">Matrícula (Opcional):</label>
                        <input type="text" id="matricula" name="matricula" value="<?php echo htmlspecialchars($dados_formulario_funcionario['matricula'] ?? ''); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal">
                    </div>
                    <div>
                        <label for="data_admissao" class="block text-sm font-medium text-gray-700">Data de Admissão:</label>
                        <input type="date" id="data_admissao" name="data_admissao" value="<?php echo htmlspecialchars($dados_formulario_funcionario['data_admissao'] ?? ''); ?>" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="cargo" class="block text-sm font-medium text-gray-700">Cargo (Opcional):</label>
                        <input type="text" id="cargo" name="cargo" value="<?php echo htmlspecialchars($dados_formulario_funcionario['cargo'] ?? ''); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="departamento" class="block text-sm font-medium text-gray-700">Departamento (Opcional):</label>
                        <input type="text" id="departamento" name="departamento" value="<?php echo htmlspecialchars($dados_formulario_funcionario['departamento'] ?? ''); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal">
                    </div>
                    <div>
                        <label for="filial_id" class="block text-sm font-medium text-gray-700">Filial (ID - Opcional):</label>
                        <input type="text" id="filial_id" name="filial_id" placeholder="ID da Filial" value="<?php echo htmlspecialchars($dados_formulario_funcionario['filial_id'] ?? ''); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal">
                        <p class="text-xs text-gray-500 mt-1">// TODO: Substituir por dropdown dinâmico de filiais.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="status_funcionario" class="block text-sm font-medium text-gray-700">Status do Funcionário:</label>
                        <select id="status_funcionario" name="status_funcionario" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-roxo-principal focus:border-roxo-principal">
                            <option value="Ativo" <?php echo (isset($dados_formulario_funcionario['status_funcionario']) && $dados_formulario_funcionario['status_funcionario'] == 'Ativo') ? 'selected' : ''; ?>>Ativo</option>
                            <option value="Inativo" <?php echo (isset($dados_formulario_funcionario['status_funcionario']) && $dados_formulario_funcionario['status_funcionario'] == 'Inativo') ? 'selected' : ''; ?>>Inativo</option>
                            <option value="Demitido" <?php echo (isset($dados_formulario_funcionario['status_funcionario']) && $dados_formulario_funcionario['status_funcionario'] == 'Demitido') ? 'selected' : ''; ?>>Demitido</option>
                            <option value="Afastado" <?php echo (isset($dados_formulario_funcionario['status_funcionario']) && $dados_formulario_funcionario['status_funcionario'] == 'Afastado') ? 'selected' : ''; ?>>Afastado</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <label for="permite_votar" class="flex items-center text-sm font-medium text-gray-700">
                            <input type="checkbox" id="permite_votar" name="permite_votar" value="1"
                                   class="h-4 w-4 text-roxo-principal border-gray-300 rounded focus:ring-roxo-principal mr-2"
                                   <?php echo (!isset($dados_formulario_funcionario['permite_votar']) || !empty($dados_formulario_funcionario['permite_votar'])) ? 'checked' : ''; ?>>
                            Permite Votar
                        </label>
                    </div>
                </div>

                <div class="pt-5">
                    <button type="submit"
                            class="w-full md:w-auto flex justify-center py-3 px-6 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-roxo-principal hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-roxo-principal">
                        Cadastrar Funcionário
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

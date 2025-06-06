<?php
$titulo_pagina_comissao = "Editar Candidatura";
require_once 'includes/header_painel_comissao.php';
// auth_comissao, db_connection, $comissao_eleicao_id_logado, $comissao_eleicao_titulo_logado já incluídos/definidos

$eleicao_id_comissao = $comissao_eleicao_id_logado;
$candidato_id = filter_input(INPUT_GET, 'candidato_id', FILTER_VALIDATE_INT);
$candidatura_db_data = null; // Renomeado para clareza
$funcionario_info = null;

if (!$candidato_id) {
    // Usar a chave de sessão global para erros se o header já foi incluído
    $_SESSION['mensagem_erro_comissao_global'] = "ID da candidatura inválido.";
    header("Location: gerenciar_candidatos.php");
    exit();
}

$erros_editar_candidatura = $_SESSION['erros_editar_candidatura'] ?? [];
$dados_formulario_edicao_candidato = $_SESSION['dados_formulario_edicao_candidato'] ?? [];
unset($_SESSION['erros_editar_candidatura'], $_SESSION['dados_formulario_edicao_candidato']);

if(isset($pdo)){
    try {
        $stmt = $pdo->prepare(
            "SELECT c.*, f.nome_completo AS funcionario_nome
             FROM candidatos c
             JOIN funcionarios f ON c.funcionario_id = f.id
             WHERE c.id = ? AND c.eleicao_id = ?"
        );
        $stmt->execute([$candidato_id, $eleicao_id_comissao]);
        $candidatura_db_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$candidatura_db_data) {
            $_SESSION['mensagem_erro_comissao_global'] = "Candidatura não encontrada ou não pertence à sua eleição.";
            header("Location: gerenciar_candidatos.php");
            exit();
        }
        if (empty($dados_formulario_edicao_candidato)) {
            $dados_formulario_edicao_candidato = $candidatura_db_data;
        }
        $funcionario_info = ['nome_completo' => $candidatura_db_data['funcionario_nome']];

    } catch (PDOException $e) {
        error_log("Erro ao buscar candidatura para edição: " . $e->getMessage());
        $_SESSION['mensagem_erro_comissao_global'] = "Erro ao carregar dados da candidatura para edição.";
        header("Location: gerenciar_candidatos.php");
        exit();
    }
}
$status_candidatura_permitidos = ['Inscrito', 'Aprovado', 'Reprovado', 'Eleito', 'Suplente', 'Não Eleito'];
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl sm:text-3xl font-bold text-cinza-chumbo">
        Editar Candidatura
    </h1>
    <a href="gerenciar_candidatos.php" class="text-azul-cipa hover:text-blue-700 font-medium">
        &larr; Voltar para Gerenciar Candidatos
    </a>
</div>

<div class="bg-white p-6 sm:p-8 rounded-xl shadow-2xl max-w-2xl mx-auto">
    <?php if ($funcionario_info): ?>
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
            <h2 class="text-xl font-semibold text-azul-cipa">Candidato: <?php echo htmlspecialchars($funcionario_info['nome_completo']); ?></h2>
            <p class="text-sm text-gray-600">Eleição: <?php echo htmlspecialchars($comissao_eleicao_titulo_logado); ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($erros_editar_candidatura)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
            <p class="font-bold">Foram encontrados os seguintes erros:</p>
            <ul class="list-disc pl-5 mt-2 text-sm">
                <?php foreach ($erros_editar_candidatura as $erro): ?>
                    <li><?php echo htmlspecialchars($erro); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="processa_editar_candidatura.php" method="POST" class="space-y-6">
        <input type="hidden" name="candidato_id" value="<?php echo $candidato_id; ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="numero_candidato" class="block text-sm font-medium text-gray-700 mb-1">Número do Candidato (opcional):</label>
                <input type="number" id="numero_candidato" name="numero_candidato"
                       value="<?php echo htmlspecialchars($dados_formulario_edicao_candidato['numero_candidato'] ?? ''); ?>"
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-azul-cipa focus:border-azul-cipa sm:text-sm">
            </div>
            <div>
                <label for="nome_urna" class="block text-sm font-medium text-gray-700 mb-1">Nome na Urna (opcional):</label>
                <input type="text" id="nome_urna" name="nome_urna"
                       value="<?php echo htmlspecialchars($dados_formulario_edicao_candidato['nome_urna'] ?? ''); ?>"
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-azul-cipa focus:border-azul-cipa sm:text-sm">
            </div>
        </div>

        <div>
            <label for="plataforma_propostas" class="block text-sm font-medium text-gray-700 mb-1">Plataforma/Propostas (opcional):</label>
            <textarea id="plataforma_propostas" name="plataforma_propostas" rows="4"
                      class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-azul-cipa focus:border-azul-cipa sm:text-sm"><?php echo htmlspecialchars($dados_formulario_edicao_candidato['plataforma_propostas'] ?? ''); ?></textarea>
        </div>

        <div>
            <label for="status_candidatura" class="block text-sm font-medium text-gray-700 mb-1">Status da Candidatura:</label>
            <select id="status_candidatura" name="status_candidatura" required
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-azul-cipa focus:border-azul-cipa sm:text-sm">
                <?php foreach ($status_candidatura_permitidos as $status): ?>
                    <option value="<?php echo $status; ?>"
                        <?php echo (isset($dados_formulario_edicao_candidato['status_candidatura']) && $dados_formulario_edicao_candidato['status_candidatura'] == $status) ? 'selected' : ''; ?>>
                        <?php echo $status; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="pt-5">
            <button type="submit"
                    class="w-full sm:w-auto flex justify-center py-3 px-6 border border-transparent rounded-lg shadow-md text-base font-medium text-white bg-azul-cipa hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-azul-cipa">
                Salvar Alterações
            </button>
        </div>
    </form>
</div>

<?php
require_once 'includes/footer_painel_comissao.php';
?>

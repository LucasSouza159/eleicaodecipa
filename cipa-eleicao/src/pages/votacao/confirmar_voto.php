<?php
require_once '../../scripts/auth_votacao.php'; // Garante autenticação e define variáveis de sessão
require_once '../../scripts/db_connection.php';

$eleicao_id = $votacao_eleicao_id_logado;
$nome_funcionario = $votacao_nome_funcionario_logado;

$candidato_id = filter_input(INPUT_GET, 'candidato_id', FILTER_VALIDATE_INT);
$voto_especial = filter_input(INPUT_GET, 'voto_especial', FILTER_SANITIZE_STRING);

$eleicao_info = null;
$candidato_info = null;
$tipo_voto_display = '';
$erro_confirmacao = null;

try {
    // Buscar informações da eleição
    $stmt_eleicao = $pdo->prepare("SELECT titulo_eleicao, status_eleicao FROM eleicoes WHERE id = ?");
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


    if ($candidato_id) {
        $stmt_candidato = $pdo->prepare(
            "SELECT c.id, c.nome_urna, c.numero_candidato, f.nome_completo AS funcionario_nome
             FROM candidatos c
             JOIN funcionarios f ON c.funcionario_id = f.id
             WHERE c.id = ? AND c.eleicao_id = ? AND c.status_candidatura = 'Aprovado'"
        );
        $stmt_candidato->execute([$candidato_id, $eleicao_id]);
        $candidato_info = $stmt_candidato->fetch(PDO::FETCH_ASSOC);

        if (!$candidato_info) {
            throw new Exception("Candidato inválido ou não pertence a esta eleição.");
        }
        $tipo_voto_display = "Candidato: " . htmlspecialchars($candidato_info['nome_urna'] ?? $candidato_info['funcionario_nome']) .
                             " (Número: " . htmlspecialchars($candidato_info['numero_candidato'] ?? '--') . ")";
    } elseif ($voto_especial === 'branco') {
        $tipo_voto_display = "VOTO EM BRANCO";
    } elseif ($voto_especial === 'nulo') {
        $tipo_voto_display = "VOTO NULO";
    } else {
        throw new Exception("Opção de voto inválida.");
    }

} catch (Exception $e) {
    error_log("Erro na confirmação de voto (Eleição ID: $eleicao_id, Funcionário ID: $votacao_funcionario_id_logado): " . $e->getMessage());
    $erro_confirmacao = "Ocorreu um erro: " . $e->getMessage() . " Por favor, tente novamente.";
    // Destruir sessão de votação em caso de erro grave para forçar novo login
    unset($_SESSION['votacao_funcionario_id'], $_SESSION['votacao_eleicao_id'], $_SESSION['votacao_nome_funcionario']);
    $_SESSION['erro_login_votacao'] = $erro_confirmacao;
     header("Location: login_votacao.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Voto - CIPA Fácil</title>
    <link href="../../src/styles/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-lg shadow-xl max-w-lg w-full">
        <header class="text-center mb-6">
            <h1 class="text-2xl font-bold text-roxo-principal">Confirmação de Voto</h1>
            <?php if ($eleicao_info): ?>
                <p class="text-lg text-cinza-chumbo"><?php echo htmlspecialchars($eleicao_info['titulo_eleicao']); ?></p>
            <?php endif; ?>
            <p class="text-md text-gray-700 mt-1">Eleitor(a): <strong class="font-semibold"><?php echo htmlspecialchars($nome_funcionario); ?></strong></p>
        </header>

        <?php if ($erro_confirmacao): ?>
            <div class="p-4 mb-6 text-center text-red-700 bg-red-100 border border-red-400 rounded-lg" role="alert">
                <?php echo htmlspecialchars($erro_confirmacao); ?>
                <p class="mt-2"><a href="urna.php" class="font-medium text-red-700 hover:text-red-900">Voltar à Urna</a></p>
            </div>
        <?php else: ?>
            <div class="text-center mb-8 p-6 bg-yellow-50 border border-yellow-300 rounded-md">
                <p class="text-lg font-medium text-gray-800">Você selecionou:</p>
                <p class="text-2xl font-bold text-roxo-principal my-3"><?php echo $tipo_voto_display; ?></p>
                <p class="text-lg font-medium text-gray-800">Confirma seu voto?</p>
            </div>

            <form action="processa_voto.php" method="POST" class="space-y-4">
                <?php if ($candidato_id): ?>
                    <input type="hidden" name="candidato_id" value="<?php echo $candidato_id; ?>">
                <?php elseif ($voto_especial): ?>
                    <input type="hidden" name="voto_especial" value="<?php echo $voto_especial; ?>">
                <?php endif; ?>

                <div class="flex flex-col sm:flex-row justify-around gap-4">
                    <button type="submit" name="confirmar" value="1"
                            class="w-full sm:w-auto bg-verde-cipa hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg shadow-md text-lg transition-colors">
                        CONFIRMAR
                    </button>
                    <a href="urna.php"
                       class="w-full sm:w-auto bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-8 rounded-lg shadow-md text-lg transition-colors text-center">
                        CORRIGIR
                    </a>
                </div>
            </form>
        <?php endif; ?>
         <div class="mt-8 text-center">
            <a href="logout_votacao.php" class="text-sm text-gray-600 hover:text-roxo-principal">Cancelar Votação e Sair</a>
        </div>
    </div>
</body>
</html>

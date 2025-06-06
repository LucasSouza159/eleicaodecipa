<?php
require_once '../../scripts/auth_votacao.php';
require_once '../../scripts/db_connection.php';

$eleicao_id = $votacao_eleicao_id_logado;
$nome_funcionario = $votacao_nome_funcionario_logado;

$candidato_id = filter_input(INPUT_GET, 'candidato_id', FILTER_VALIDATE_INT);
$voto_especial = filter_input(INPUT_GET, 'voto_especial', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

$eleicao_info = null;
$candidato_info = null;
$tipo_voto_display = '';
$foto_candidato_placeholder = '<div class="w-32 h-32 bg-gray-300 rounded-full mx-auto mb-4 flex items-center justify-center"><svg class="w-20 h-20 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg></div>';

if(isset($pdo)){
    try {
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
                throw new Exception("Candidato inválido ou não aprovado para esta eleição.");
            }
            $tipo_voto_display = "<div class='mb-3'>".$foto_candidato_placeholder."</div>".
                                 "<p class='text-3xl font-bold text-roxo-principal'>" . htmlspecialchars($candidato_info['numero_candidato'] ?? '--') . "</p>" .
                                 "<p class='text-2xl font-semibold text-cinza-chumbo'>" . htmlspecialchars($candidato_info['nome_urna'] ?? $candidato_info['funcionario_nome']) . "</p>" .
                                 "<p class='text-sm text-gray-600'>(" . htmlspecialchars($candidato_info['funcionario_nome']) . ")</p>";
        } elseif ($voto_especial === 'branco') {
            $tipo_voto_display = "<p class='text-3xl font-bold text-gray-700'>VOTO EM BRANCO</p>";
        } elseif ($voto_especial === 'nulo') {
            $tipo_voto_display = "<p class='text-3xl font-bold text-red-600'>VOTO NULO</p>";
        } else {
            throw new Exception("Opção de voto inválida.");
        }

    } catch (Exception $e) {
        error_log("Erro na confirmação (Eleição ID: $eleicao_id, Funcionário ID: $votacao_funcionario_id_logado): " . $e->getMessage());
        $_SESSION['erro_urna'] = "Erro ao preparar confirmação: " . $e->getMessage();
        header("Location: urna.php");
        exit();
    }
} else {
     $_SESSION['erro_urna'] = "Erro crítico de conexão. Tente novamente.";
     header("Location: urna.php");
     exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Voto | CIPA Fácil</title>
    <link href="../../src/styles/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 sm:p-10 rounded-xl shadow-2xl text-center max-w-lg w-full">
        <header class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-roxo-principal">Confirmação de Voto</h1>
            <?php if ($eleicao_info): ?>
                <p class="text-lg text-cinza-chumbo mt-1"><?php echo htmlspecialchars($eleicao_info['titulo_eleicao']); ?></p>
            <?php endif; ?>
            <p class="text-md text-gray-700 mt-2">Eleitor(a): <strong class="font-semibold"><?php echo htmlspecialchars($nome_funcionario); ?></strong></p>
        </header>

        <div class="mb-8 p-6 bg-yellow-50 border-2 border-yellow-400 rounded-lg min-h-[200px] flex flex-col justify-center items-center">
            <p class="text-xl font-medium text-gray-800 mb-3">Você selecionou:</p>
            <div class="text-center">
                <?php echo $tipo_voto_display; // Contém HTML, já sanitizado na criação ?>
            </div>
        </div>

        <p class="text-xl font-semibold text-gray-800 mb-8">Confirma seu voto?</p>

        <form action="processa_voto.php" method="POST" class="space-y-4">
            <?php if ($candidato_id): ?>
                <input type="hidden" name="candidato_id" value="<?php echo $candidato_id; ?>">
            <?php elseif ($voto_especial): ?>
                <input type="hidden" name="voto_especial" value="<?php echo $voto_especial; ?>">
            <?php endif; ?>

            <div class="flex flex-col sm:flex-row justify-center gap-4 sm:gap-6">
                <button type="submit" name="confirmar" value="1"
                        class="w-full sm:w-auto order-1 sm:order-2 bg-verde-cipa hover:bg-green-700 text-white font-bold py-3 px-10 rounded-lg shadow-md text-lg transition-colors duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    CONFIRMAR
                </button>
                <a href="urna.php"
                   class="w-full sm:w-auto order-2 sm:order-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-10 rounded-lg shadow-md text-lg transition-colors duration-300 ease-in-out text-center focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    CORRIGIR
                </a>
            </div>
        </form>
         <div class="mt-10 text-center">
            <a href="logout_votacao.php" class="text-sm text-gray-500 hover:text-roxo-principal underline">
                Cancelar Votação e Sair
            </a>
        </div>
    </div>
</body>
</html>

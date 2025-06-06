<?php
require_once '../../scripts/auth_comissao.php'; // Garante autenticação e define $comissao_eleicao_id_logado
require_once '../../scripts/db_connection.php';

$eleicao_id_comissao = $comissao_eleicao_id_logado;
$erros = [];
$dados_formulario = $_POST;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $candidato_id = filter_input(INPUT_POST, 'candidato_id', FILTER_VALIDATE_INT);

    if (!$candidato_id) {
        $_SESSION['mensagem_erro_candidato'] = "ID da candidatura inválido para edição.";
        header("Location: gerenciar_candidatos.php");
        exit();
    }

    // Validar se a candidatura pertence à eleição da comissão logada
    $candidatura_original = null;
    try {
        $stmt_check_owner = $pdo->prepare("SELECT * FROM candidatos WHERE id = ? AND eleicao_id = ?");
        $stmt_check_owner->execute([$candidato_id, $eleicao_id_comissao]);
        $candidatura_original = $stmt_check_owner->fetch(PDO::FETCH_ASSOC);
        if ($candidatura_original === false) {
            $_SESSION['mensagem_erro_candidato'] = "Candidatura não encontrada ou não pertence à sua eleição (falha na edição).";
            header("Location: gerenciar_candidatos.php");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Erro ao verificar propriedade da candidatura: " . $e->getMessage());
        $_SESSION['mensagem_erro_candidato'] = "Erro de banco de dados ao verificar candidatura.";
        header("Location: gerenciar_candidatos.php");
        exit();
    }

    $numero_candidato_raw = trim(filter_input(INPUT_POST, 'numero_candidato'));
    $numero_candidato = ($numero_candidato_raw === '') ? null : filter_var($numero_candidato_raw, FILTER_VALIDATE_INT);
    $nome_urna = trim(filter_input(INPUT_POST, 'nome_urna'));
    $nome_urna = empty($nome_urna) ? null : $nome_urna;
    $plataforma_propostas = trim(filter_input(INPUT_POST, 'plataforma_propostas'));
    $plataforma_propostas = empty($plataforma_propostas) ? null : $plataforma_propostas;
    $status_candidatura = trim(filter_input(INPUT_POST, 'status_candidatura'));

    // Validações
    if ($numero_candidato_raw !== '' && $numero_candidato === false) {
        $erros[] = "Número do candidato deve ser um valor numérico.";
    } elseif ($numero_candidato !== null && $numero_candidato <= 0) {
        $erros[] = "Número do candidato deve ser positivo.";
    }

    $status_permitidos = ['Inscrito', 'Aprovado', 'Reprovado', 'Eleito', 'Suplente', 'Não Eleito'];
    if (empty($status_candidatura) || !in_array($status_candidatura, $status_permitidos)) {
        $erros[] = "Status da candidatura inválido.";
    }

    // Verificar unicidade do número do candidato (se alterado e fornecido)
    if ($numero_candidato !== null && $numero_candidato != $candidatura_original['numero_candidato']) {
        try {
            $stmt_check_num = $pdo->prepare("SELECT id FROM candidatos WHERE eleicao_id = ? AND numero_candidato = ? AND id != ?");
            $stmt_check_num->execute([$eleicao_id_comissao, $numero_candidato, $candidato_id]);
            if ($stmt_check_num->fetch()) {
                $erros[] = "Este número de candidato já está em uso nesta eleição por outro candidato.";
            }
        } catch (PDOException $e) {
            error_log("Erro ao verificar unicidade do número do candidato (edição): " . $e->getMessage());
            $erros[] = "Erro de banco de dados ao verificar número do candidato.";
        }
    }

    if (empty($erros)) {
        try {
            $sql = "UPDATE candidatos SET
                        numero_candidato = ?,
                        nome_urna = ?,
                        plataforma_propostas = ?,
                        status_candidatura = ?
                    WHERE id = ? AND eleicao_id = ?";

            $stmt_update = $pdo->prepare($sql);
            $stmt_update->execute([
                $numero_candidato,
                $nome_urna,
                $plataforma_propostas,
                $status_candidatura,
                $candidato_id,
                $eleicao_id_comissao
            ]);

            $_SESSION['mensagem_sucesso_candidato'] = "Candidatura atualizada com sucesso!";
            header("Location: gerenciar_candidatos.php");
            exit();

        } catch (PDOException $e) {
            error_log("Erro ao editar candidatura: " . $e->getMessage());
             if ($e->getCode() == '23000') {
                 $erros[] = "Erro: Número de candidato duplicado (verificação dupla).";
            } else {
                $erros[] = "Erro ao atualizar candidatura no banco de dados. Detalhe: " . $e->getMessage();
            }
            $_SESSION['erros_editar_candidatura'] = $erros;
            $_SESSION['dados_formulario_edicao_candidato'] = $dados_formulario;
            header("Location: editar_candidatura.php?candidato_id=" . $candidato_id);
            exit();
        }
    } else {
        $_SESSION['erros_editar_candidatura'] = $erros;
        $_SESSION['dados_formulario_edicao_candidato'] = $dados_formulario; // Mantém os dados que o usuário tentou submeter
        header("Location: editar_candidatura.php?candidato_id=" . $candidato_id);
        exit();
    }

} else {
    header("Location: gerenciar_candidatos.php");
    exit();
}
?>

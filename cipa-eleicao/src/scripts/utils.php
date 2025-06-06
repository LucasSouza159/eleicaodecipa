<?php

/**
 * Valida um número de CNPJ.
 *
 * @param string $cnpj O CNPJ para validar.
 * @return bool True se o CNPJ é válido, False caso contrário.
 */
function validarCNPJ($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);

    // Verifica se o CNPJ tem 14 dígitos
    if (strlen($cnpj) != 14) {
        return false;
    }

    // Verifica se todos os dígitos são iguais (ex: 00.000.000/0000-00), o que é inválido
    if (preg_match('/(\d)\1{13}/', $cnpj)) {
        return false;
    }

    // Cálculo do primeiro dígito verificador
    $soma = 0;
    $multiplicador = 5;
    for ($i = 0; $i < 12; $i++) {
        $soma += $cnpj[$i] * $multiplicador;
        $multiplicador = ($multiplicador == 2) ? 9 : $multiplicador - 1;
    }
    $resto = $soma % 11;
    $dv1 = ($resto < 2) ? 0 : 11 - $resto;

    if ($cnpj[12] != $dv1) {
        return false;
    }

    // Cálculo do segundo dígito verificador
    $soma = 0;
    $multiplicador = 6;
    for ($i = 0; $i < 13; $i++) {
        $soma += $cnpj[$i] * $multiplicador;
        $multiplicador = ($multiplicador == 2) ? 9 : $multiplicador - 1;
    }
    $resto = $soma % 11;
    $dv2 = ($resto < 2) ? 0 : 11 - $resto;

    if ($cnpj[13] != $dv2) {
        return false;
    }

    return true;
}

/**
 * Registra um log no banco de dados.
 *
 * @param PDO $pdo A conexão PDO com o banco de dados.
 * @param string $nivel Nível do log (INFO, WARNING, ERROR, CRITICAL, AUDIT).
 * @param string $acao Ação realizada.
 * @param array $detalhes Detalhes adicionais (serão convertidos para JSON).
 * @param int|null $usuario_id ID do usuário.
 * @param string|null $tipo_usuario Tipo do usuário (Empresa, Comissao, Funcionario, Admin, Sistema).
 * @return bool True se o log foi registrado, False em caso de falha.
 */
function registrarLog(PDO $pdo, $nivel, $acao, $detalhes = [], $usuario_id = null, $tipo_usuario = null) {
    try {
        $endereco_ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $detalhes_json = !empty($detalhes) ? json_encode($detalhes) : null;

        $sql = "INSERT INTO logs (nivel_log, usuario_id, tipo_usuario, acao_realizada, detalhes_log, endereco_ip)
                VALUES (:nivel_log, :usuario_id, :tipo_usuario, :acao_realizada, :detalhes_log, :endereco_ip)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nivel_log', $nivel);
        $stmt->bindParam(':usuario_id', $usuario_id, $usuario_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindParam(':tipo_usuario', $tipo_usuario, $tipo_usuario === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(':acao_realizada', $acao);
        $stmt->bindParam(':detalhes_log', $detalhes_json, $detalhes_json === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(':endereco_ip', $endereco_ip, $endereco_ip === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

        return $stmt->execute();
    } catch (PDOException $e) {
        // Em um cenário de produção, logar este erro em um arquivo, mas não quebrar a aplicação.
        error_log("Falha ao registrar log no banco de dados: " . $e->getMessage());
        return false;
    }
}


/**
 * Esboço de função para enviar emails.
 * Requer a instalação e configuração de uma biblioteca de e-mail como PHPMailer.
 *
 * @param string $destinatario_email Email do destinatário.
 * @param string $destinatario_nome Nome do destinatário.
 * @param string $assunto Assunto do email.
 * @param string $corpo_html Corpo do email em HTML.
 * @param string $corpo_texto Corpo do email em texto plano (alternativo).
 * @return bool True se o email foi enviado (simulado), False caso contrário.
 */
function enviarEmail($destinatario_email, $destinatario_nome, $assunto, $corpo_html, $corpo_texto = '') {
    // // Exemplo com PHPMailer (requer instalação via Composer: composer require phpmailer/phpmailer)
    // use PHPMailer\PHPMailer\PHPMailer;
    // use PHPMailer\PHPMailer\SMTP;
    // use PHPMailer\PHPMailer\Exception;

    // // Incluir o autoload do Composer se estiver usando-o para gerenciar dependências
    // // require_once __DIR__ . '/../../vendor/autoload.php';
    // // Ou, se não estiver usando Composer para PHPMailer, inclua os arquivos manualmente:
    // // require_once 'path/to/PHPMailer/src/Exception.php';
    // // require_once 'path/to/PHPMailer/src/PHPMailer.php';
    // // require_once 'path/to/PHPMailer/src/SMTP.php';

    // // Carregar variáveis de ambiente para configuração de SMTP (exemplo com Dotenv)
    // // if (file_exists(__DIR__ . '/../../.env')) {
    // //     $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
    // //     $dotenv->load();
    // // }
    // // $smtp_host = $_ENV['SMTP_HOST'] ?? 'smtp.example.com';
    // // $smtp_port = $_ENV['SMTP_PORT'] ?? 587;
    // // $smtp_user = $_ENV['SMTP_USER'] ?? 'user@example.com';
    // // $smtp_pass = $_ENV['SMTP_PASS'] ?? 'secret';
    // // $smtp_secure = $_ENV['SMTP_SECURE'] ?? PHPMailer::ENCRYPTION_STARTTLS; // tls ou ssl
    // // $email_remetente = $_ENV['EMAIL_REMETENTE_SISTEMA'] ?? 'nao-responda@cipafacil.com';
    // // $nome_remetente = $_ENV['NOME_REMETENTE_SISTEMA'] ?? 'Sistema CIPA Fácil';


    // $mail = new PHPMailer(true); // Passar `true` habilita exceções

    // try {
    //     // Configurações do servidor SMTP
    //     // $mail->SMTPDebug = SMTP::DEBUG_OFF; // SMTP::DEBUG_SERVER para debug detalhado
    //     $mail->isSMTP();
    //     $mail->Host       = $smtp_host;
    //     $mail->SMTPAuth   = true;
    //     $mail->Username   = $smtp_user;
    //     $mail->Password   = $smtp_pass;
    //     $mail->SMTPSecure = $smtp_secure;
    //     $mail->Port       = (int)$smtp_port;
    //     $mail->CharSet    = 'UTF-8';

    //     // Remetente e Destinatários
    //     $mail->setFrom($email_remetente, $nome_remetente);
    //     $mail->addAddress($destinatario_email, $destinatario_nome);
    //     // $mail->addReplyTo('info@example.com', 'Information');
    //     // $mail->addCC('cc@example.com');
    //     // $mail->addBCC('bcc@example.com');

    //     // Conteúdo do Email
    //     $mail->isHTML(true); // Define o formato do email para HTML
    //     $mail->Subject = $assunto;
    //     $mail->Body    = $corpo_html;
    //     $mail->AltBody = empty($corpo_texto) ? strip_tags($corpo_html) : $corpo_texto;

    //     $mail->send();
    //     // registrarLog($GLOBALS['pdo'] ?? null, 'INFO', 'EMAIL_ENVIADO_SUCESSO', ['assunto' => $assunto, 'destinatario' => $destinatario_email]);
    //     return true;
    // } catch (Exception $e) {
    //     // A variável $pdo pode não estar disponível globalmente aqui, precisaria ser passada ou gerenciada de outra forma para o log.
    //     // Idealmente, a função registrarLog teria uma forma de obter $pdo ou seria uma classe com $pdo injetado.
    //     // error_log("PHPMailer Erro: {$mail->ErrorInfo}. Destinatario: $destinatario_email, Assunto: $assunto");
    //     // registrarLog($GLOBALS['pdo'] ?? null, 'ERROR', 'FALHA_ENVIO_EMAIL', ['erro' => $mail->ErrorInfo, 'destinatario' => $destinatario_email, 'assunto' => $assunto]);
    //     return false;
    // }

    // Simulação de envio de email para este esboço
    error_log("Simulação de envio de email para: $destinatario_email, Assunto: $assunto");
    return true; // Retorna true para simular sucesso por enquanto
}

?>

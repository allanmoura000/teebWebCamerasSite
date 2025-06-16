<?php
// Ativa todos os tipos de erro para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclui os arquivos necessários
require_once 'conexao.php';
require_once 'logger.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Função para garantir que a saída seja JSON válido
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Captura qualquer saída não intencional
ob_start();

try {
    // Lê o JSON do corpo da requisição
    $json = file_get_contents('php://input');
    Logger::log("JSON recebido: " . $json, 'DEBUG');
    
    if (empty($json)) {
        throw new Exception("Nenhum dado recebido");
    }
    
    // Decodifica o JSON
    $data = json_decode($json, true);
    Logger::log("Dados decodificados: " . print_r($data, true), 'DEBUG');
    
    if (!$data) {
        throw new Exception("JSON inválido: " . json_last_error_msg());
    }

    // Extrai e sanitiza os dados
    $userId = filter_var($data['userId'], FILTER_VALIDATE_INT);
    $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
    Logger::log("Email sanitizado: " . $email, 'DEBUG');

    if (!$userId || !$email) {
        throw new Exception("Dados inválidos ou incompletos");
    }

    // Gera o código de verificação
    $codigo = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    Logger::log("Código gerado: " . $codigo, 'DEBUG');

    // Salva o código no banco
    $stmt = $conexao->prepare("INSERT INTO codigos_verificacao (user_id, codigo, expira_em, usado, criado_em) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 15 MINUTE), 0, NOW())");
    if (!$stmt) {
        throw new Exception("Erro ao preparar statement: " . $conexao->error);
    }

    $stmt->bind_param("is", $userId, $codigo);
    if (!$stmt->execute()) {
        throw new Exception("Erro ao salvar código: " . $stmt->error);
    }

    // Configura o PHPMailer
    $mail = new PHPMailer(true);
    Logger::log("Iniciando envio de código", 'INFO');

    try {
        // Configurações do servidor
        $mail->isSMTP();
        $mail->Host = 'localhost';
        $mail->SMTPAuth = true;
        $mail->Username = 'allanmoura@teebweb.com.br';
        $mail->Password = 'otgwsugf123';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Configurações do email
        $mail->setFrom('allanmoura@teebweb.com.br', 'Sistema de Câmeras');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Código de Verificação - Sistema de Câmeras';
        $mail->Body = "
            <h2>Código de Verificação</h2>
            <p>Seu código de verificação é: <strong style='font-size: 24px; color: #f1c400;'>{$codigo}</strong></p>
            <p>Este código expirará em 15 minutos.</p>
            <p>Se você não solicitou este código, por favor ignore este e-mail.</p>
            <hr>
            <p style='color: #666; font-size: 12px;'>Este é um e-mail automático, por favor não responda.</p>
        ";
        
        $mail->AltBody = "Seu código de verificação é: {$codigo}. Este código expirará em 15 minutos.";

        // Envia o email
        if (!$mail->send()) {
            throw new Exception("Erro ao enviar email: " . $mail->ErrorInfo);
        }

        // Limpa qualquer saída anterior
        ob_clean();
        
        // Retorna sucesso
        sendJsonResponse([
            'success' => true,
            'message' => 'Código enviado com sucesso',
            'userId' => $userId
        ]);

    } catch (Exception $e) {
        Logger::log("Erro no PHPMailer: " . $e->getMessage(), 'ERROR');
        throw new Exception("Erro ao enviar email: " . $e->getMessage());
    }

} catch (Exception $e) {
    Logger::log("Erro: " . $e->getMessage(), 'ERROR');
    
    // Limpa qualquer saída anterior
    ob_clean();
    
    // Retorna erro
    sendJsonResponse([
        'success' => false,
        'error' => $e->getMessage()
    ], 500);
}

// Limpa o buffer de saída
ob_end_flush();
?>
<?php
// Ativa todos os tipos de erro para debug

error_reporting(E_ALL);
ini_set('display_errors', 0); // Desativa exibição de erros no output
ini_set('log_errors', 1); // Ativa log de erros
ini_set('error_log', __DIR__ . '/logs/php_errors.log'); // Define arquivo de log

// Inclui os arquivos necessários
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/vendor/autoload.php';

// Limpa qualquer cache do PHP
if (function_exists('opcache_reset')) {
    opcache_reset();
}
if (function_exists('apc_clear_cache')) {
    apc_clear_cache();
}

use Google\Client as GoogleClient;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;

// Função para garantir que a saída seja JSON válido
function sendJsonResponse($data, $statusCode = 200) {
    if (!headers_sent()) {
        http_response_code($statusCode);
header('Content-Type: application/json');
        // Adiciona headers CORS necessários
        header('Access-Control-Allow-Origin: https://teebweb.com.br');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
        header('Access-Control-Allow-Credentials: true');
    }
    echo json_encode($data);
    exit;
}

// Função para criar o cliente Gmail API
function getGmailClient() {
    $config = require 'gmail_api_config.php';
    
    $client = new GoogleClient();
    $client->setClientId($config['client_id']);
    $client->setClientSecret($config['client_secret']);
    $client->setScopes($config['scopes']);
    $client->setAccessType($config['access_type']);
    
    // Tenta carregar o token salvo
    $token_file = __DIR__ . '/gmail_token.json';
    if (file_exists($token_file)) {
        $token_data = json_decode(file_get_contents($token_file), true);
        
        // Verifica se o token expirou
        if (isset($token_data['created']) && isset($token_data['expires_in'])) {
            $expires_at = $token_data['created'] + $token_data['expires_in'];
            if (time() >= $expires_at && isset($token_data['refresh_token'])) {
                try {
                    $client->setAccessToken($token_data);
                    if ($client->isAccessTokenExpired()) {
                        $new_token = $client->fetchAccessTokenWithRefreshToken($token_data['refresh_token']);
                        $token_data = array_merge($token_data, $new_token);
                        $token_data['created'] = time();
                        file_put_contents($token_file, json_encode($token_data));
                    }
                } catch (Exception $e) {
                    Logger::error("Erro ao atualizar token: " . $e->getMessage());
                    unlink($token_file);
                    throw $e;
                }
            }
        }
        
        $client->setAccessToken($token_data);
        return $client;
    } else {
        // Se não há token, retorna URL de autenticação
        $auth_url = $client->createAuthUrl();
        Logger::info("Token não encontrado, redirecionando para autenticação");
        
        // Verifica se é uma requisição AJAX
        $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        if ($is_ajax) {
            // Para desktop app, retornamos a URL para o frontend abrir em uma nova janela
            sendJsonResponse([
                'success' => false,
                'auth_required' => true,
                'auth_url' => $auth_url,
                'auth_type' => 'popup',
                'message' => 'Autenticação necessária'
            ]);
        } else {
            // Se não for AJAX, redireciona normalmente
            header('Location: ' . $auth_url);
            exit;
        }
    }
}

// Função para enviar email via Gmail API
function sendEmailViaGmail($to, $subject, $htmlBody, $textBody = '') {
    try {
        $client = getGmailClient();
        $service = new Gmail($client);
        
        // Prepara o email
        $message = new Message();
        
        // Codifica o email em base64
        $email = "To: $to\r\n";
        $email .= "Subject: $subject\r\n";
        $email .= "MIME-Version: 1.0\r\n";
        $email .= "Content-Type: multipart/alternative; boundary=boundary\r\n\r\n";
        
        // Parte em texto plano
        $email .= "--boundary\r\n";
        $email .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
        $email .= $textBody . "\r\n\r\n";
        
        // Parte HTML
        $email .= "--boundary\r\n";
        $email .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
        $email .= $htmlBody . "\r\n\r\n";
        
        $email .= "--boundary--";
        
        // Codifica o email em base64url
        $base64Email = rtrim(strtr(base64_encode($email), '+/', '-_'), '=');
        $message->setRaw($base64Email);

        // Envia o email
        $sent = $service->users_messages->send('me', $message);
        
        return $sent;
    } catch (Exception $e) {
        Logger::error("Erro ao enviar email via Gmail API: " . $e->getMessage());
        throw $e;
    }
}

// Captura qualquer saída não intencional
ob_start();

// Trata requisições OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: https://teebweb.com.br');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400'); // 24 horas
    exit(0);
}

try {
    Logger::info("Iniciando processamento de envio de código");
    
    // Lê o JSON do corpo da requisição
    $json = file_get_contents('php://input');
    Logger::debug("JSON recebido: " . $json);
    
    if (empty($json)) {
        throw new Exception("Nenhum dado recebido");
    }
    
    // Decodifica o JSON
    $data = json_decode($json, true);
    Logger::debug("Dados decodificados: " . print_r($data, true));
    
    if (!$data) {
        throw new Exception("JSON inválido: " . json_last_error_msg());
    }

    // Extrai e sanitiza os dados
    $userId = filter_var($data['userId'], FILTER_VALIDATE_INT);
    $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
    Logger::debug("Email sanitizado: " . $email);

    if (!$userId || !$email) {
        throw new Exception("Dados inválidos ou incompletos");
    }

    // Gera o código de verificação
    $codigo = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    Logger::debug("Código gerado: " . $codigo);

    // Salva o código no banco
    $stmt = $conexao->prepare("INSERT INTO codigos_verificacao (user_id, codigo, expira_em, usado, criado_em) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 15 MINUTE), 0, NOW())");
    if (!$stmt) {
        throw new Exception("Erro ao preparar statement: " . $conexao->error);
    }

    $stmt->bind_param("is", $userId, $codigo);
    if (!$stmt->execute()) {
        throw new Exception("Erro ao salvar código: " . $stmt->error);
    }

    Logger::info("Código salvo no banco com sucesso");

    // Prepara o conteúdo do email
    $subject = 'Código de Verificação - Sistema de Câmeras';
    
    $htmlBody = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #f1c400; color: #000; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .code { font-size: 32px; font-weight: bold; color: #f1c400; text-align: center; padding: 20px; background-color: #fff; border: 2px solid #f1c400; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; font-size: 12px; color: #666; margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Código de Verificação</h1>
                </div>
                <div class='content'>
                    <p>Olá,</p>
                    <p>Você solicitou um código de verificação para o Sistema de Câmeras.</p>
                    <div class='code'>{$codigo}</div>
                    <p>Este código expirará em 15 minutos.</p>
                    <p>Se você não solicitou este código, por favor ignore este e-mail.</p>
                </div>
                <div class='footer'>
                    <p>Este é um e-mail automático, por favor não responda.</p>
                    <p>Sistema de Câmeras - Teeb Segurança</p>
                </div>
            </div>
        </body>
        </html>
    ";
    
    $textBody = "Seu código de verificação é: {$codigo}\n\nEste código expirará em 15 minutos.\n\nSe você não solicitou este código, por favor ignore este e-mail.\n\nSistema de Câmeras - Teeb Segurança";

    Logger::debug("Tentando enviar email para: " . $email);
    
    // Envia o email via Gmail API
    $sent = sendEmailViaGmail($email, $subject, $htmlBody, $textBody);
    
    Logger::info("Email enviado com sucesso para: " . $email);

    // Limpa qualquer saída anterior
    ob_clean();
    
    // Retorna sucesso
    sendJsonResponse([
        'success' => true,
        'message' => 'Código enviado com sucesso',
        'userId' => $userId
    ]);
    
} catch (Exception $e) {
    Logger::error("Erro geral: " . $e->getMessage());
    Logger::error("Stack trace: " . $e->getTraceAsString());
    
    // Limpa qualquer saída anterior
    ob_clean();
    
    // Retorna erro
    sendJsonResponse([
        'success' => false,
        'message' => 'Erro ao enviar código: ' . $e->getMessage()
    ], 500);
}

// Limpa o buffer de saída
ob_end_flush();
?>
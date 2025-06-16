<?php

use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;

// Define o diretório base
define('BASE_DIR', __DIR__);

// Função para verificar e carregar arquivos
function requireFile($file) {
    $path = BASE_DIR . DIRECTORY_SEPARATOR . $file;
    if (!file_exists($path)) {
        error_log("Arquivo não encontrado: $path");
        throw new Exception("Arquivo não encontrado: $file");
    }
    require_once $path;
}

// Carrega as configurações
try {
    requireFile('config.php');
    requireFile('vendor/autoload.php');
    requireFile('logger.php');
} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao carregar configurações: ' . $e->getMessage()
    ]);
    exit;
}

// Função para retornar JSON e encerrar
function returnJson($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
    echo json_encode($data);
    exit;
}

// Função para registrar erro e retornar JSON
function handleError($message, $error = null, $statusCode = 500) {
    $errorData = [
        'success' => false,
        'message' => $message
    ];
    
    if ($error) {
        $errorData['debug_info'] = [
            'error' => $error->getMessage(),
            'file' => $error->getFile(),
            'line' => $error->getLine()
        ];
        error_log("Erro: $message - " . $error->getMessage() . " em " . $error->getFile() . ":" . $error->getLine());
    } else {
        error_log("Erro: $message");
    }
    
    returnJson($errorData, $statusCode);
}

// Trata requisições OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
    header('Access-Control-Max-Age: 86400');
    exit(0);
}

try {
    // Verifica se é uma requisição POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        handleError('Método não permitido', null, 405);
    }

    // Lê o corpo da requisição
    $raw_input = file_get_contents('php://input');
    if ($raw_input === false) {
        handleError('Erro ao ler dados da requisição');
    }
    
    error_log("Input recebido: " . $raw_input);
    
    $input = json_decode($raw_input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        handleError('Erro ao decodificar JSON: ' . json_last_error_msg());
    }

    $userId = $input['userId'] ?? null;
    $email = $input['email'] ?? null;

    error_log("Dados recebidos - userId: $userId, email: $email");

    if (!$userId || !$email) {
        handleError('Dados incompletos - userId e email são obrigatórios', null, 400);
    }

    // Gera código de verificação
    $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    error_log("Código gerado: $codigo");
    
    // Conecta ao banco de dados
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        handleError("Erro na conexão com o banco de dados", new Exception($conn->connect_error));
    }
    error_log("Conexão com banco estabelecida");
    
    // Verifica se o usuário existe
    $checkStmt = $conn->prepare("SELECT id FROM cadastro_simples WHERE id = ?");
    if (!$checkStmt) {
        handleError("Erro ao preparar verificação de usuário", new Exception($conn->error));
    }
    
    $checkStmt->bind_param("i", $userId);
    if (!$checkStmt->execute()) {
        handleError("Erro ao verificar usuário", new Exception($checkStmt->error));
    }
    
    $result = $checkStmt->get_result();
    if ($result->num_rows === 0) {
        handleError("Usuário não encontrado", null, 404);
    }
    $checkStmt->close();
    
    // Atualiza o código no banco
    $stmt = $conn->prepare("UPDATE cadastro_simples SET codigo_verificacao = ?, data_envio_codigo = NOW() WHERE id = ?");
    if (!$stmt) {
        handleError("Erro ao preparar statement", new Exception($conn->error));
    }
    
    $stmt->bind_param("si", $codigo, $userId);
    if (!$stmt->execute()) {
        handleError("Erro ao atualizar código", new Exception($stmt->error));
    }
    error_log("Código atualizado no banco");
    
    // Configura a Brevo
    $config = require BASE_DIR . '/brevo_config.php';
    error_log("Configuração Brevo carregada");
    
    try {
        $apiConfig = Configuration::getDefaultConfiguration()->setApiKey('api-key', $config['api_key']);
        $apiInstance = new TransactionalEmailsApi(null, $apiConfig);
        
        // Prepara o email
        $sendSmtpEmail = new SendSmtpEmail();
        $sendSmtpEmail->setTo([['email' => $email]]);
        $sendSmtpEmail->setTemplateId($config['template_id']);
        $sendSmtpEmail->setParams([
            'codigo' => $codigo,
            'nome' => $email
        ]);
        
        error_log("Tentando enviar email via Brevo para: $email");
        
        // Envia o email
        $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
        
        error_log("Email enviado com sucesso. Message ID: " . $result->getMessageId());
        
        // Resposta de sucesso
        returnJson([
            'success' => true,
            'message' => 'Código enviado com sucesso',
            'debug_info' => [
                'email' => $email,
                'message_id' => $result->getMessageId()
            ]
        ]);
        
    } catch (Exception $e) {
        handleError("Erro ao enviar email via Brevo", $e);
    }
    
} catch (Exception $e) {
    handleError("Erro ao processar requisição", $e);
} catch (Error $e) {
    handleError("Erro fatal", $e);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($checkStmt)) $checkStmt->close();
    if (isset($conn)) $conn->close();
}
?> 
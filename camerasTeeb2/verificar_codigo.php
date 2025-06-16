<?php
require_once __DIR__ . '/init.php';

ini_set('display_errors', 0);
error_reporting(E_ALL);

// Configura o log de erros
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

header('Content-Type: application/json');

// Função para log
function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, __DIR__ . '/logs/php_errors.log');
}

try {
    $raw_input = file_get_contents('php://input');
    logError("Input recebido: " . $raw_input);
    
    $data = json_decode($raw_input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Erro ao decodificar JSON: " . json_last_error_msg());
    }

    if (!$data || !isset($data['userId']) || !isset($data['code'])) {
        throw new Exception("Dados inválidos - userId e code são obrigatórios");
    }

    $userId = $data['userId'];
    $code = $data['code'];
    
    logError("Dados processados - userId: $userId, code: $code");

    // Busca o código salvo no banco
    $stmt = $conexao->prepare("SELECT codigo_verificacao FROM cadastro_simples WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Erro ao preparar statement: " . $conexao->error);
    }
    
    $stmt->bind_param("i", $userId);
    if (!$stmt->execute()) {
        throw new Exception("Erro ao executar query: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Usuário não encontrado");
    }
    
    $row = $result->fetch_assoc();
    $storedCode = $row['codigo_verificacao'];
    
    // Verifica se o código armazenado é nulo
    if ($storedCode === null) {
        throw new Exception("Código de verificação não encontrado. Por favor, solicite um novo código.");
    }
    
    logError("Código armazenado: " . $storedCode);
    
    // Remove espaços e caracteres especiais do código
    $cleanCode = preg_replace('/[^0-9]/', '', $code);
    $cleanStoredCode = preg_replace('/[^0-9]/', '', $storedCode);
    
    logError("Códigos limpos - Recebido: $cleanCode, Armazenado: $cleanStoredCode");

    if ($cleanStoredCode === $cleanCode) {
        // Marca como verificado
        $update = $conexao->prepare("UPDATE cadastro_simples SET is_verified = 1 WHERE id = ?");
        if (!$update) {
            throw new Exception("Erro ao preparar update: " . $conexao->error);
        }
        
        $update->bind_param("i", $userId);
        if (!$update->execute()) {
            throw new Exception("Erro ao atualizar status: " . $update->error);
        }
        
        logError("Usuário verificado com sucesso");
        echo json_encode(['success' => true]);
    } else {
        logError("Código inválido - Não corresponde");
        echo json_encode(['success' => false, 'error' => 'Código inválido']);
    }
} catch (Exception $e) {
    logError("Erro: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}
?>
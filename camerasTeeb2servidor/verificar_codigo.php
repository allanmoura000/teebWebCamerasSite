<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

include 'conexao.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['userId']) || !isset($data['code'])) {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
    exit;
}

$userId = $data['userId'];
$code = $data['code'];

try {
    // Busca o código salvo no banco - ATUALIZE O NOME DA COLUNA PARA 'verification_code'
    $stmt = $conexao->prepare("SELECT verification_code FROM cadastro_simples WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Usuário não encontrado");
    }
    
    $row = $result->fetch_assoc();
    $storedCode = $row['verification_code'];
    
    // Remove espaços e caracteres especiais do código
    $cleanCode = preg_replace('/[^0-9]/', '', $code);
    $cleanStoredCode = preg_replace('/[^0-9]/', '', $storedCode);

    if ($cleanStoredCode === $cleanCode) {
        // Marca como verificado
        $update = $conexao->prepare("UPDATE cadastro_simples SET is_verified = 1 WHERE id = ?");
        $update->bind_param("i", $userId);
        
        if ($update->execute()) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception("Erro ao atualizar status de verificação");
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Código inválido']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

include 'conexao.php';

$name     = trim($_POST['name']     ?? '');
$cpf_raw  = trim($_POST['cpf']      ?? '');
$email    = trim($_POST['email']    ?? '');
$phone    = trim($_POST['phone']    ?? '');
$password = trim($_POST['password'] ?? '');

if (!$name || !$cpf_raw || !$email || !$phone || !$password) {
    http_response_code(400);
    echo json_encode(["error" => "Preencha todos os campos obrigatórios."]);
    exit;
}

$cpf = preg_replace('/\D+/', '', $cpf_raw);
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    // Verificar se o CPF já existe
    $checkStmt = $conexao->prepare("SELECT id FROM cadastro_simples WHERE cpf = ?");
    $checkStmt->bind_param('s', $cpf);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        // Atualizar registro existente
        $row = $result->fetch_assoc();
        $userId = $row['id'];
        
        $updateStmt = $conexao->prepare("
            UPDATE cadastro_simples 
            SET name = ?, email = ?, phone = ?, password = ?
            WHERE id = ?
        ");
        $updateStmt->bind_param('ssssi', $name, $email, $phone, $hashedPassword, $userId);
        $updateStmt->execute();
    } else {
        // Criar novo registro
        $insertStmt = $conexao->prepare("
            INSERT INTO cadastro_simples (cpf, name, email, phone, password)
            VALUES (?, ?, ?, ?, ?)
        ");
        $insertStmt->bind_param('sssss', $cpf, $name, $email, $phone, $hashedPassword);
        $insertStmt->execute();
        $userId = $insertStmt->insert_id;
    }

    // Retornar ID do usuário SEM gerar código aqui
    echo json_encode(['id' => $userId]);

} catch (mysqli_sql_exception $sqlEx) {
    http_response_code(500);
    echo json_encode(['error' => $sqlEx->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
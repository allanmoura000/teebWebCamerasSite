<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

include 'conexao.php';

// Receber os dados do POST
$name = trim($_POST['name'] ?? '');
$cpf = trim($_POST['cpf'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validar dados
if (!$name || !$cpf || !$email || !$phone || !$password) {
    http_response_code(400);
    echo json_encode(["error" => "Preencha todos os campos obrigatórios."]);
    exit;
}

// Criptografar a senha
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    // Verificar se CPF já existe
    $sqlCheck = "SELECT id FROM cadastro_simples WHERE cpf = ?";
    $stmtCheck = $conexao->prepare($sqlCheck);
    $stmtCheck->bind_param('s', $cpf);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result();

    if ($result->num_rows > 0) {
        // Atualizar registro existente
        $row = $result->fetch_assoc();
        $userId = $row['id'];
        
        $sqlUpdate = "UPDATE cadastro_simples 
                      SET name = ?, email = ?, phone = ?, password = ? 
                      WHERE id = ?";
        $stmtUpdate = $conexao->prepare($sqlUpdate);
        $stmtUpdate->bind_param('ssssi', $name, $email, $phone, $hashedPassword, $userId);
        
        if ($stmtUpdate->execute()) {
            echo json_encode(['id' => $userId]);
        } else {
            throw new Exception("Erro ao atualizar registro: " . $stmtUpdate->error);
        }
    } else {
        // Inserir novo registro
        $sqlInsert = "INSERT INTO cadastro_simples (name, cpf, email, phone, password) 
                      VALUES (?, ?, ?, ?, ?)";
        $stmtInsert = $conexao->prepare($sqlInsert);
        $stmtInsert->bind_param('sssss', $name, $cpf, $email, $phone, $hashedPassword);
        
        if ($stmtInsert->execute()) {
            $insertedId = $stmtInsert->insert_id;
            echo json_encode(['id' => $insertedId]);
        } else {
            throw new Exception("Erro ao inserir registro: " . $stmtInsert->error);
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
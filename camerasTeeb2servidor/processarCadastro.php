<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Dados de conexão com o banco de dados
$host    = "191.7.32.22";
$usuario = "estagio";
$senha   = "Kgbe1771@";
$banco   = "estagio_cameras";

// Conectar ao banco de dados
$conn = new mysqli($host, $usuario, $senha, $banco);
if ($conn->connect_error) {
    die(json_encode(["error" => "Erro na conexão com o banco de dados: " . $conn->connect_error]));
}

// Receber os dados do POST
$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validar os dados recebidos
if (!$name || !$email || !$password) {
    die(json_encode(["error" => "Preencha todos os campos obrigatórios."]));
}

// Verificar se o e-mail já existe no banco
$sqlCheck = "SELECT id FROM usuarios WHERE email = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param('s', $email);
$stmtCheck->execute();
$result = $stmtCheck->get_result();

if ($result->num_rows > 0) {
    // Atualizar o registro existente
    $row = $result->fetch_assoc();
    $userId = $row['id'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sqlUpdate = "UPDATE usuarios SET name = ?, password = ? WHERE id = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param('ssi', $name, $hashedPassword, $userId);
    if ($stmtUpdate->execute()) {
        echo json_encode(['id' => $userId, 'message' => 'Registro atualizado com sucesso!']);
    } else {
        die(json_encode(['error' => "Erro ao atualizar o registro: " . $stmtUpdate->error]));
    }
    $stmtUpdate->close();
} else {
    // Inserir um novo registro
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sqlInsert = "INSERT INTO usuarios (name, email, password) VALUES (?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param('sss', $name, $email, $hashedPassword);
    if ($stmtInsert->execute()) {
        $insertedId = $stmtInsert->insert_id;
        echo json_encode(['id' => $insertedId, 'message' => 'Registro inserido com sucesso!']);
    } else {
        die(json_encode(['error' => "Erro ao inserir o registro: " . $stmtInsert->error]));
    }
    $stmtInsert->close();
}

$stmtCheck->close();
$conn->close();
?>

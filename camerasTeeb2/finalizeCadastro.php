<?php
// finalizeCadastro.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

session_start();

// Verifica se os dados da verificação existem na sessão
if (!isset($_SESSION['verification'])) {
    die(json_encode(["error" => "Sessão expirada. Tente novamente."]));
}

$cadastroData = $_SESSION['verification'];
$name = trim($cadastroData['name']);
$email = trim($cadastroData['email']);
$phone = trim($cadastroData['phone']);
$password = trim($cadastroData['password']);

// Conectar ao banco de dados (substitua pelos dados reais do seu ambiente)
$host = "191.7.32.22";
$usuario = "estagio";
$senha = "Kgbe1771@";
$banco = "estagio_cameras";

$conn = new mysqli($host, $usuario, $senha, $banco);
if ($conn->connect_error) {
    die(json_encode(["error" => "Erro na conexão com o banco de dados: " . $conn->connect_error]));
}

// Validação adicional se necessário
if (!$name || !$email || !$password) {
    die(json_encode(["error" => "Dados insuficientes para cadastro."]));
}

// Verificar se o e-mail já existe
$sqlCheck = "SELECT id FROM usuarios WHERE email = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param('s', $email);
$stmtCheck->execute();
$result = $stmtCheck->get_result();

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

if ($result->num_rows > 0) {
    // Atualiza o registro existente
    $row = $result->fetch_assoc();
    $userId = $row['id'];

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
    // Insere um novo registro
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

// Limpa a sessão da verificação
unset($_SESSION['verification']);
?>

<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

$host = "191.7.32.22";
$usuario = "estagio";
$senha = "Kgbe1771@";
$banco = "estagio_cameras";

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die(json_encode(["error" => "Erro ao conectar ao banco de dados."]));
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (!$email || !$password) {
    die(json_encode(["error" => "Preencha todos os campos."]));
}

$sql = "SELECT id, password FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['userId'] = $user['id']; // Define a sessão para autenticação
        echo json_encode(["success" => true, "id" => $user['id']]);
    } else {
        echo json_encode(["error" => "Senha incorreta."]);
    }
} else {
    echo json_encode(["error" => "Usuário não encontrado. Cadastre-se."]);
}

$stmt->close();
$conn->close();
?>

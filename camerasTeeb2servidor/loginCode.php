<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Use a mesma conexão do cadastro
include 'conexao.php'; // Supondo que este arquivo já exista

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (!$email || !$password) {
    die(json_encode(["error" => "Preencha todos os campos."]));
}

// Consulta na mesma tabela do cadastro
$sql = "SELECT id, password FROM cadastro_simples WHERE email = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['userId'] = $user['id']; // Cria sessão
        echo json_encode(["success" => true, "id" => $user['id']]);
    } else {
        echo json_encode(["error" => "Senha incorreta."]);
    }
} else {
    echo json_encode(["error" => "Usuário não encontrado."]);
}

$stmt->close();
$conexao->close();
?>
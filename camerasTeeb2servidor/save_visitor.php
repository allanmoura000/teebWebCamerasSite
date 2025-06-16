<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

$host = "191.7.32.22";
$usuario = "estagio";
$senha = "Kgbe1771@";
$banco = "estagio_cameras";

// Conectar ao banco de dados
$conn = new mysqli($host, $usuario, $senha, $banco);
if ($conn->connect_error) {
    die(json_encode(["error" => "Erro ao conectar ao banco de dados: " . $conn->connect_error]));
}

// Receber dados JSON enviados pelo JavaScript
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    die(json_encode(["error" => "Nenhum dado recebido."]));
}

// Extrair dados
$name = $data['name'] ?? 'Desconhecido';
$email = $data['email'] ?? 'Desconhecido';
$userAgent = $data['userAgent'] ?? 'Não identificado';
$deviceType = $data['deviceType'] ?? 'Não identificado';
$browser = $data['browser'] ?? 'Não identificado';
$androidVersion = $data['androidVersion'] ?? '';

// Inserir no banco de dados
$sql = "INSERT INTO visitors (name, email, userAgent, deviceType, browser, androidVersion, visitTime) VALUES (?, ?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $name, $email, $userAgent, $deviceType, $browser, $androidVersion);

if ($stmt->execute()) {
    echo json_encode(["message" => "Dados do visitante salvos com sucesso."]);
} else {
    echo json_encode(["error" => "Erro ao salvar os dados: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>

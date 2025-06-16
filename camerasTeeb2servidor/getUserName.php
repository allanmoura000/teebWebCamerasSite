<?php
include 'conexao.php'; // Arquivo de conexão com o banco de dados

header('Content-Type: application/json');

$userId = $_GET['userId'] ?? '';

if (!empty($userId)) {
    $sql = "SELECT name FROM cadastro_simples WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(["name" => $row['name']]);
    } else {
        echo json_encode(["name" => "Usuário Desconhecido"]);
    }

    $stmt->close();
} else {
    echo json_encode(["name" => "Usuário Desconhecido"]);
}

$conexao->close();
?>

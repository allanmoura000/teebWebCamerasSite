<?php
include 'conexao.php';

$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['userId'];
$code = $data['code'];

$stmt = $conexao->prepare("SELECT verification_code FROM cadastro_simples WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row && $row['verification_code'] === $code) {
    // Marca como verificado
    $update = $conexao->prepare("UPDATE cadastro_simples SET is_verified = 1 WHERE id = ?");
    $update->bind_param("i", $userId);
    $update->execute();
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
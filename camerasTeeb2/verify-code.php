<?php
include 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['userId'];
$code = $data['code'];

$stmt = $conn->prepare("SELECT code_expiry FROM cadastro_simples WHERE id = ? AND verification_code = ?");
$stmt->bind_param('is', $userId, $code);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if(strtotime($row['code_expiry']) > time()) {
        // Código válido
        $conn->query("UPDATE cadastro_simples SET verified = 1 WHERE id = $userId");
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Código expirado']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Código inválido']);
}
?>
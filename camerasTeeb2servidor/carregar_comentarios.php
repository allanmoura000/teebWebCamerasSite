<?php
include 'conexao.php';

$cameraId = isset($_GET['camera_id']) ? intval($_GET['camera_id']) : 0;

if ($cameraId <= 0) {
    die('Câmera inválida');
}

$sql = "SELECT c.*, u.name AS nome, DATE_FORMAT(c.data_hora, '%d/%m/%Y %H:%i') AS data 
        FROM comentarios c
        JOIN cadastro_simples u ON c.user_id = u.id
        WHERE c.camera_id = ?
        ORDER BY c.data_hora DESC";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $cameraId);
$stmt->execute();
$result = $stmt->get_result();

$comentarios = [];
while ($row = $result->fetch_assoc()) {
    $comentarios[] = [
        'nome' => htmlspecialchars($row['nome']),
        'comentario' => htmlspecialchars($row['comentario']),
        'data' => $row['data']
    ];
}

echo json_encode($comentarios);
?>
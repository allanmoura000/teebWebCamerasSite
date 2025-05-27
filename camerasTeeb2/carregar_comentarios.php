<?php
include 'conexao.php'; // Conexão com o banco de dados

header('Content-Type: application/json');

$camera_id = $_GET['camera_id'];

$sql = "SELECT usuario, comentario, data FROM comentarios WHERE camera_id = ? ORDER BY data DESC";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $camera_id);
$stmt->execute();
$resultado = $stmt->get_result();

$comentarios = [];
while ($row = $resultado->fetch_assoc()) {
    $comentarios[] = $row;
}

echo json_encode($comentarios);
?>

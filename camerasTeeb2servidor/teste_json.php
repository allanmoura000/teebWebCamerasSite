<?php
include 'conexao.php';

$sql = "SELECT id, nome, imagem, latitude, longitude FROM cad_cameras WHERE latitude IS NOT NULL AND longitude IS NOT NULL";
$resultado = $conexao->query($sql);

$camerasMapa = [];

if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $camerasMapa[] = [
            'id' => $row['id'],
            'nome' => $row['nome'],
            'imagem' => $row['imagem'],
            'latitude' => floatval($row['latitude']),
            'longitude' => floatval($row['longitude'])
        ];
    }
}

// Retorna os dados em formato JSON
header('Content-Type: application/json');
echo json_encode($camerasMapa, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>

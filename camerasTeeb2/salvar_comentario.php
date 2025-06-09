<?php
session_start();
include 'conexao.php';

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['userId'])) {
    $response['message'] = 'Usuário não logado';
    echo json_encode($response);
    exit;
}

$userId = $_SESSION['userId'];
$cameraId = isset($_POST['camera_id']) ? intval($_POST['camera_id']) : 0;
$comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';

if ($cameraId <= 0) {
    $response['message'] = 'Câmera inválida';
    echo json_encode($response);
    exit;
}

if (empty($comentario)) {
    $response['message'] = 'Comentário não pode ser vazio';
    echo json_encode($response);
    exit;
}

try {
    $stmt = $conexao->prepare("INSERT INTO comentarios (camera_id, user_id, comentario) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $cameraId, $userId, $comentario);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $response['success'] = true;
        $response['message'] = 'Comentário salvo com sucesso';
    } else {
        $response['message'] = 'Falha ao salvar comentário';
    }
} catch (Exception $e) {
    $response['message'] = 'Erro: ' . $e->getMessage();
}

echo json_encode($response);
?>
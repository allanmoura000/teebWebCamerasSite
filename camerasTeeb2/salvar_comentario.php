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

// Verificar se já existe um comentário idêntico recente do mesmo usuário para esta câmera
try {
    $checkStmt = $conexao->prepare("SELECT id FROM comentarios WHERE camera_id = ? AND user_id = ? AND comentario = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)");
    $checkStmt->bind_param("iis", $cameraId, $userId, $comentario);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        $response['message'] = 'Você já enviou este comentário recentemente';
        echo json_encode($response);
        exit;
    }
} catch (Exception $e) {
    $response['message'] = 'Erro ao verificar comentários duplicados: ' . $e->getMessage();
    echo json_encode($response);
    exit;
}

try {
    $stmt = $conexao->prepare("INSERT INTO comentarios (camera_id, user_id, comentario, created_at) VALUES (?, ?, ?, NOW())");
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
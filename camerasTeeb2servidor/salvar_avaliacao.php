<?php
session_start();
include 'conexao.php';

header('Content-Type: application/json');

if (!isset($_SESSION['userId'])) {
    echo json_encode(["success" => false, "message" => "Você precisa estar logado para avaliar."]);
    exit;
}

$user_id = intval($_SESSION['userId']);
$camera_id = intval($_POST['camera_id'] ?? 0);
$nota = intval($_POST['nota'] ?? 0);

if ($camera_id > 0 && $nota >= 1 && $nota <= 5) {
    // Verifica se o usuário já avaliou a câmera
    $stmt_check = $conexao->prepare("SELECT id FROM avaliacoes WHERE camera_id = ? AND user_id = ?");
    $stmt_check->bind_param("ii", $camera_id, $user_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        // Atualiza a avaliação existente
        $stmt_update = $conexao->prepare("UPDATE avaliacoes SET nota = ? WHERE camera_id = ? AND user_id = ?");
        $stmt_update->bind_param("iii", $nota, $camera_id, $user_id);
        $stmt_update->execute();
        $stmt_update->close();
    } else {
        // Insere nova avaliação
        $stmt_insert = $conexao->prepare("INSERT INTO avaliacoes (camera_id, nota, user_id) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("iii", $camera_id, $nota, $user_id);
        $stmt_insert->execute();
        $stmt_insert->close();
    }
    $stmt_check->close();

    // Recalcula a média e o total de avaliações
    $result = $conexao->query("SELECT AVG(nota) as media, COUNT(*) as total FROM avaliacoes WHERE camera_id = $camera_id");
    $dados = $result->fetch_assoc();

    echo json_encode([
        "success" => true,
        "media"   => number_format($dados['media'], 1),
        "total"   => $dados['total'],
        "user_rating" => $nota
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Dados inválidos"]);
}
?>

<?php
include 'conexao.php';

$cpf = $_GET['cpf'] ?? '';
$cpf_clean = preg_replace('/[^0-9]/', '', $cpf);

$sql = "SELECT COUNT(*) as total FROM cadastro_simples WHERE REPLACE(REPLACE(cpf, '.', ''), '-', '') = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param('s', $cpf_clean);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode(['exists' => $row['total'] > 0]);
?>
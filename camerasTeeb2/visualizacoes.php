<?php
ob_start(); // Inicia o buffer de saída para evitar envio prematuro de headers
ini_set('display_errors', 0); // Desativa exibição de erros no frontend
error_reporting(E_ALL); // Mantém registro completo de erros
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log'); // Arquivo de log personalizado

include 'conexao.php'; // Conexão com o banco de dados

$camera_id = isset($_GET['camera_id']) ? (int)$_GET['camera_id'] : 0;
$acao = isset($_GET['acao']) ? $_GET['acao'] : '';

// Validação básica
if ($camera_id <= 0) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(["error" => "ID de câmera inválido"]);
    exit;
}

// Garante registro inicial
try {
    $stmt = $conexao->prepare(
        "INSERT INTO visualizacoes (camera_id, total, online) 
        VALUES (?, 0, 0) 
        ON DUPLICATE KEY UPDATE camera_id = VALUES(camera_id)"
    );
    $stmt->bind_param("i", $camera_id);
    $stmt->execute();
} catch (Exception $e) {
    error_log("Erro ao criar registro: " . $e->getMessage());
}

// Processa ações
switch ($acao) {
    case 'abrir':
        $conexao->query("UPDATE visualizacoes SET total = total + 1 WHERE camera_id = $camera_id");
        $conexao->query("UPDATE visualizacoes SET online = online + 1 WHERE camera_id = $camera_id");
        break;
        
    case 'fechar':
        $conexao->query(
            "UPDATE visualizacoes SET online = 
            CASE WHEN online > 0 THEN online - 1 ELSE 0 END 
            WHERE camera_id = $camera_id"
        );
        break;
}

// Obtém dados atualizados
$result = $conexao->query(
    "SELECT total, online FROM visualizacoes 
    WHERE camera_id = $camera_id"
);
$data = $result->fetch_assoc() ?: ['total' => 0, 'online' => 0];

// Limpa buffer e envia resposta
ob_end_clean();
header('Content-Type: application/json');
echo json_encode($data);
exit;
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$host = "191.7.32.22";  // Servidor do banco
$usuario = "estagio";    // Usuário do banco
$senha = "Kgbe1771@";    // Senha do banco
$banco = "estagio_cameras"; // Nome do banco de dados

// Conexão com o banco
$conexao = new mysqli($host, $usuario, $senha, $banco); 

// Verificação de erro na conexão
if ($conexao->connect_error) {
    die(json_encode(["error" => "Falha na conexão: " . $conexao->connect_error]));
}

// Obtém o IP e a sessão do usuário
$ip = $_SERVER['REMOTE_ADDR'];
$session_id = session_id();

// Registra a visita
$sql = "INSERT INTO visitas (ip, session_id) VALUES ('$ip', '$session_id') 
        ON DUPLICATE KEY UPDATE data_acesso=NOW()";
$conexao->query($sql);

// Contador de visitantes totais
$result_total = $conexao->query("SELECT COUNT(*) AS total FROM visitas");
$total_visitas = $result_total ? $result_total->fetch_assoc()['total'] : 0;

// Contador de visitantes online (últimos 5 minutos)
$tempo_online = 5;
$result_online = $conexao->query("SELECT COUNT(DISTINCT session_id) AS total 
                                  FROM visitas WHERE data_acesso > NOW() - INTERVAL $tempo_online MINUTE");
$visitantes_online = $result_online ? $result_online->fetch_assoc()['total'] : 0;

// Fecha a conexão
$conexao->close();

// Retorna os dados em JSON
header('Content-Type: application/json');
echo json_encode(["total" => $total_visitas, "online" => $visitantes_online]);
?>

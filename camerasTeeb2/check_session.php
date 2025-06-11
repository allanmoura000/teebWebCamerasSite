<?php
session_start();
include 'conexao.php';

$response = ['valid' => false];

if(isset($_GET['userId']) && isset($_SESSION['userId'])) {
    $storedId = intval($_GET['userId']);
    $sessionId = intval($_SESSION['userId']);
    
    $response['valid'] = ($storedId === $sessionId);
}

header('Content-Type: application/json');
echo json_encode($response);
?>
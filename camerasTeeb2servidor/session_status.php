<?php
session_start();
header('Content-Type: application/json');

echo json_encode([
    "logged_in" => isset($_SESSION['userId']) && $_SESSION['userId'] > 0,
    "user_id" => $_SESSION['userId'] ?? null
]);
?>

<?php
session_start();

$email = trim($_POST['email'] ?? '');
$codeInput = trim($_POST['code'] ?? '');

if (!$email || !$codeInput) {
    die(json_encode(["error" => "Dados insuficientes para verificação."]));
}

if (!isset($_SESSION['verification'])) {
    die(json_encode(["error" => "Código expirado ou não gerado."]));
}

$verification = $_SESSION['verification'];

// Você também pode implementar uma verificação de tempo (por exemplo, o código expira em 10 minutos)
if ((time() - $verification['timestamp']) > 600) {
    unset($_SESSION['verification']);
    die(json_encode(["error" => "Código expirado, por favor solicite um novo."]));
}

if ($verification['email'] === $email && $verification['code'] == $codeInput) {
    echo json_encode(["message" => "Código verificado com sucesso!"]);
} else {
    echo json_encode(["error" => "Código inválido."]);
}
?>

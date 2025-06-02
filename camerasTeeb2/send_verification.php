<?php
// send_verification.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
include 'conexao.php';

$email = trim($_POST['email'] ?? '');
if (!$email) {
    http_response_code(400);
    echo json_encode(['error' => 'E-mail é obrigatório.']);
    exit;
}

// Verifica se o usuário existe
$stmt = $conexao->prepare("SELECT id FROM cadastro_simples WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Usuário não encontrado.']);
    exit;
}
$user = $res->fetch_assoc();
$userId = $user['id'];

// Gera um código de 6 dígitos
$code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

// Salva no banco
$upd = $conexao->prepare("
    UPDATE cadastro_simples
       SET verification_code = ?, is_verified = 0
     WHERE id = ?
");
$upd->bind_param('si', $code, $userId);
$upd->execute();

// Envia o e-mail (ajuste headers conforme seu servidor)
$to      = $email;
$subject = 'Seu código de verificação';
$message = "Olá,\n\nSeu código de verificação é: $code\n\nDigite-o no site para confirmar seu cadastro.";
$headers = 'From: no-reply@seudominio.com' . "\r\n" .
           'Reply-To: no-reply@seudominio.com' . "\r\n" .
           'X-Mailer: PHP/' . phpversion();

if (mail($to, $subject, $message, $headers)) {
    echo json_encode(['success' => 'Código enviado para ' . $email]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Falha ao enviar e-mail.']);
}
$upd->close();
$stmt->close();
$conexao->close();

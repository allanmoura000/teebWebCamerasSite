<?php
include 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['userId'];

// Gerar código de 6 dígitos
$code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
$expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

$stmt = $conn->prepare("UPDATE cadastro_simples SET verification_code = ?, code_expiry = ? WHERE id = ?");
$stmt->bind_param('ssi', $code, $expiry, $userId);

if($stmt->execute()) {
    // Obter dados do usuário
    $result = $conn->query("SELECT name, email FROM cadastro_simples WHERE id = $userId");
    $user = $result->fetch_assoc();
    
    // Configurações do e-mail
    $to = $user['email'];
    $subject = '🔐 Código de Verificação - Teeb Web';
    $fromEmail = 'allanmouraoficial2@gmail.com';
    $fromName = 'Teeb Web Security';
    $replyTo = 'allanmouraoficial2@gmail.com';

    // Corpo do e-mail em HTML
    $messageHTML = '
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
            .code { font-size: 24px; color: #fdd835; font-weight: bold; margin: 20px 0; }
            .footer { margin-top: 20px; color: #666; }
        </style>
    </head>
    <body>
        <div class="container">
            <h2>Olá, '.$user['name'].'!</h2>
            <p>Seu código de verificação para acesso ao sistema é:</p>
            <div class="code">'.$code.'</div>
            <p>Este código é válido por 10 minutos ⏳</p>
            <div class="footer">
                <p>Atenciosamente,<br>
                Equipe Teeb Web Security<br>
                <small>http://localhost/camerasteeb2/camerasTeeb2/conta.php</small></p>
            </div>
        </div>
    </body>
    </html>
    ';

    // Headers do e-mail
    $headers = "From: $fromName <$fromEmail>\r\n";
    $headers .= "Reply-To: $replyTo\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // Envio do e-mail
    if(mail($to, $subject, $messageHTML, $headers)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Falha no envio do e-mail']);
    }
    
} else {
    echo json_encode(['success' => false]);
}
?>
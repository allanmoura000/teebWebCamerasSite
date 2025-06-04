<?php
// Ativar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ajustar caminho do autoloader
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// Incluir conexão com banco de dados
include 'conexao.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['userId']) || !isset($data['email'])) {
        throw new Exception("Dados inválidos na requisição");
    }
    
    $userId = $data['userId'];
    $email = $data['email'];

    // Gera código de 6 dígitos
    $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // Salva no banco - ATUALIZE O NOME DA COLUNA PARA 'verification_code'
    $stmt = $conexao->prepare("UPDATE cadastro_simples SET verification_code = ? WHERE id = ?");
    $stmt->bind_param("si", $code, $userId);
    
    if (!$stmt->execute()) {
        throw new Exception("Erro ao salvar código no banco: " . $stmt->error);
    }

    // Configura PHPMailer
    $mail = new PHPMailer(true);

    // Configurações do servidor SMTP (ATUALIZE COM SEUS DADOS)
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'allanmouraoficial2@gmail.com'; // Seu e-mail REAL
    $mail->Password = 'zttj mzej zvcv dgps';     // Sua senha REAL
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';

    // Remetente
    $mail->setFrom('allanmouraoficial2@gmail.com', 'Sistema de Verificação');
    $mail->addAddress($email);

    // Conteúdo
    $mail->isHTML(true);
    $mail->Subject = 'Seu Código de Verificaçãoooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo';
    $mail->Body    = "Seu código de verificação é: <b>$code</b>";
    $mail->AltBody = "Seu código de verificação é: $code";

    $mail->send();
    echo json_encode(['success' => true]);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    
    // Log adicional para depuração
    error_log("Erro em enviar_codigo.php: " . $e->getMessage());
}
?>
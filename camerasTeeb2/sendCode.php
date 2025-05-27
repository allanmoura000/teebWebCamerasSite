<?php
session_start();

// Inclua os arquivos do PHPMailer (ajuste os caminhos conforme sua estrutura de pastas)
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Função para registrar logs em um arquivo
$logFile = __DIR__ . '/email_log.txt';
function gravarLog($mensagem) {
    global $logFile;
    $data = date('Y-m-d H:i:s');
    $linha = "[$data] $mensagem" . PHP_EOL;
    file_put_contents($logFile, $linha, FILE_APPEND);
}

// Recebe os dados do POST
$email = trim($_POST['email'] ?? '');
$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validação simples de e-mail
if (!$email) {
    $msg = "Erro: E-mail não fornecido.";
    error_log($msg);
    gravarLog($msg);
    die(json_encode(["error" => "E-mail é obrigatório para envio do código."]));
}

// Gera um código de 6 dígitos
$code = rand(100000, 999999);

// Armazena o código e os dados do usuário na sessão
$_SESSION['verification'] = [
    'email'     => $email,
    'name'      => $name,
    'phone'     => $phone,
    'password'  => $password,
    'code'      => $code,
    'timestamp' => time()
];

// Cria uma instância do PHPMailer
$mail = new PHPMailer(true);

try {
    // Configurações do servidor SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.seudominio.com';      // Substitua pelo SMTP do seu domínio
    $mail->SMTPAuth   = true;
    $mail->Username   = 'seuemail@seudominio.com';    // E-mail válido cadastrado da empresa
    $mail->Password   = 'suasenha';                   // Senha do e-mail
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
    $mail->Port       = 587;                          // Ajuste a porta conforme necessário

    // Configuração do remetente e destinatário
    $mail->setFrom('no-reply@seudominio.com', 'Sua Empresa');
    $mail->addAddress($email); // E-mail do usuário que receberá o código

    // Conteúdo do e-mail
    $mail->isHTML(true);
    $mail->Subject = 'Seu código de verificação';
    $mail->Body    = "Olá, seu código de verificação é: <strong>{$code}</strong>";

    // Envia o e-mail
    $mail->send();

    $msg = "E-mail enviado para {$email} com sucesso. Código: {$code}";
    error_log($msg);
    gravarLog($msg);
    echo json_encode(["message" => "Código enviado com sucesso!"]);

} catch (Exception $e) {
    $msg = "Falha ao enviar e-mail para {$email}. Erro: " . $mail->ErrorInfo;
    error_log($msg);
    gravarLog($msg);
    echo json_encode(["error" => "Falha ao enviar o código. Erro: " . $mail->ErrorInfo]);
}
?>

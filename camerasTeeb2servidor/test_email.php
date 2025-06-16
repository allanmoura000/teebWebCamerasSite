<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    $mail = new PHPMailer(true);
    
    // Configurações do servidor
    $mail->isSMTP();
    $mail->Host = '191-7-32-15.cprapid.com'; // Host correto do servidor SMTP
    $mail->SMTPAuth = true;
    $mail->Username = 'allanmoura@teebweb.com.br';
    $mail->Password = 'otgwsugf123';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    
    // Configurações de SSL/TLS
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    
    // Configurações do e-mail
    $mail->setFrom('allanmoura@teebweb.com.br', 'Sistema de Câmeras');
    $mail->addAddress('allanmouraoficial2@gmail.com', 'Allan Moura');
    $mail->Subject = 'Teste de Envio - Sistema de Câmeras';
    
    // Corpo do e-mail
    $mail->isHTML(true);
    $mail->Body = '
        <h2>Teste de Envio de E-mail</h2>
        <p>Este é um e-mail de teste do sistema de câmeras.</p>
        <p>Se você está recebendo este e-mail, significa que:</p>
        <ul>
            <li>O PHPMailer está instalado corretamente</li>
            <li>As configurações SMTP estão funcionando</li>
            <li>O sistema pode enviar e-mails de verificação</li>
        </ul>
        <p>Data e hora do teste: ' . date('d/m/Y H:i:s') . '</p>
    ';
    
    $mail->AltBody = 'Teste de envio de e-mail do sistema de câmeras. Data e hora: ' . date('d/m/Y H:i:s');
    
    // Debug
    $mail->SMTPDebug = 2; // Habilita debug detalhado
    $mail->Debugoutput = function($str, $level) {
        echo "Debug: $str\n";
    };
    
    // Envia o e-mail
    $mail->send();
    echo "E-mail de teste enviado com sucesso! Por favor, verifique sua caixa de entrada (e spam).";
    
} catch (Exception $e) {
    echo "Erro ao enviar e-mail de teste: {$mail->ErrorInfo}";
}
?> 
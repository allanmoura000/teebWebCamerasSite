<?php
require 'logger.php';
require 'vendor/autoload.php';
require 'conexao.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

$tests = [
    'database' => false,
    'table' => false,
    'phpmailer' => false,
    'smtp' => false
];

$errors = [];

try {
    // Teste 1: Conexão com o banco de dados
    Logger::info("Testando conexão com o banco de dados");
    if ($conexao->connect_error) {
        throw new Exception("Erro na conexão com o banco: " . $conexao->connect_error);
    }
    $tests['database'] = true;
    Logger::info("Conexão com o banco de dados OK");

    // Teste 2: Verificar tabela
    Logger::info("Verificando tabela codigos_verificacao");
    $check_table = $conexao->query("SHOW TABLES LIKE 'codigos_verificacao'");
    if ($check_table->num_rows == 0) {
        throw new Exception("Tabela codigos_verificacao não existe");
    }
    $tests['table'] = true;
    Logger::info("Tabela codigos_verificacao existe");

    // Teste 3: PHPMailer
    Logger::info("Testando PHPMailer");
    $mail = new PHPMailer(true);
    $tests['phpmailer'] = true;
    Logger::info("PHPMailer carregado com sucesso");

    // Teste 4: Conexão SMTP
    Logger::info("Testando conexão SMTP");
    $mail->isSMTP();
    $mail->Host = '191-7-32-15.cprapid.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'allanmoura@teebweb.com.br';
    $mail->Password = 'otgwsugf123';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    // Tenta conectar ao SMTP
    if (!$mail->smtpConnect()) {
        throw new Exception("Não foi possível conectar ao servidor SMTP");
    }
    $tests['smtp'] = true;
    Logger::info("Conexão SMTP OK");

    // Se chegou aqui, todos os testes passaram
    echo json_encode([
        'success' => true,
        'message' => 'Todos os testes passaram com sucesso!',
        'tests' => $tests,
        'logs' => Logger::getLogs()
    ]);

} catch (Exception $e) {
    Logger::error($e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Erro nos testes do sistema',
        'tests' => $tests,
        'error' => $e->getMessage(),
        'logs' => Logger::getLogs()
    ]);
}
?> 
<?php
// Ativar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar se o autoloader existe
$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die("Erro: O arquivo autoload.php não foi encontrado em: $autoloadPath\n" .
        "Por favor, execute 'composer require phpmailer/phpmailer' no diretório do projeto.");
}

// Tentar carregar o autoloader
try {
    require $autoloadPath;
    echo "Autoloader carregado com sucesso.\n";
} catch (Exception $e) {
    die("Erro ao carregar o autoloader: " . $e->getMessage());
}

// Verificar se a classe PHPMailer existe
if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    die("Erro: A classe PHPMailer não foi encontrada.\n" .
        "Verifique se o PHPMailer está instalado corretamente via Composer.");
}

echo "PHPMailer está instalado corretamente.\n";

// Testar configurações básicas
try {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = function($str, $level) {
        echo "Debug: $str\n";
    };
    
    $mail->isSMTP();
    $mail->Host = 'localhost';  // Usa o servidor SMTP local do cPanel
    $mail->SMTPAuth = true;
    $mail->Username = 'allanmoura@teebweb.com.br';  // Email do cPanel
    $mail->Password = 'otgwsugf123';  // Senha do email do cPanel
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    // Configurações adicionais para melhor compatibilidade
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    
    echo "Configurações SMTP testadas com sucesso.\n";
    
    // Testar conexão SMTP
    if ($mail->smtpConnect()) {
        echo "Conexão SMTP testada com sucesso.\n";
    } else {
        echo "Erro ao conectar ao servidor SMTP.\n";
    }
    
} catch (Exception $e) {
    die("Erro ao testar PHPMailer: " . $e->getMessage());
}

echo "Todos os testes concluídos com sucesso!\n";
?> 
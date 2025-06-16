<?php
// Ativa todos os tipos de erro para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Instalação do PHPMailer</h1>";

// Função para baixar arquivo usando cURL
function downloadFile($url, $destination) {
    if (!function_exists('curl_init')) {
        die("<p style='color: red;'>cURL não está disponível neste servidor</p>");
    }

    $ch = curl_init($url);
    $fp = fopen($destination, 'wb');

    if ($fp === false) {
        die("<p style='color: red;'>Não foi possível criar o arquivo: $destination</p>");
    }

    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300); // 5 minutos de timeout

    $success = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);
    fclose($fp);

    if (!$success) {
        die("<p style='color: red;'>Erro ao baixar arquivo: $error</p>");
    }

    if ($httpCode !== 200) {
        die("<p style='color: red;'>Erro HTTP ao baixar arquivo: $httpCode</p>");
    }

    return true;
}

// Verifica se o composer.phar existe
if (!file_exists('composer.phar')) {
    echo "<p>Baixando composer.phar...</p>";
    
    // URL alternativa do composer.phar
    $composerUrl = 'https://getcomposer.org/composer.phar';
    
    if (downloadFile($composerUrl, 'composer.phar')) {
        // Torna o arquivo executável
        chmod('composer.phar', 0755);
        echo "<p style='color: green;'>composer.phar baixado com sucesso!</p>";
    }
}

// Verifica se o composer.json existe
if (!file_exists('composer.json')) {
    echo "<p>Criando composer.json...</p>";
    $composerJson = [
        'require' => [
            'phpmailer/phpmailer' => '^6.8'
        ]
    ];
    
    if (file_put_contents('composer.json', json_encode($composerJson, JSON_PRETTY_PRINT)) === false) {
        die("<p style='color: red;'>Erro ao criar composer.json</p>");
    }
    
    echo "<p style='color: green;'>composer.json criado com sucesso!</p>";
}

// Verifica se o PHP CLI está disponível
echo "<p>Verificando PHP CLI...</p>";
$phpVersion = shell_exec('php -v 2>&1');
if ($phpVersion === null) {
    echo "<p style='color: red;'>PHP CLI não está disponível ou não pode ser executado</p>";
    echo "<p>Tentando método alternativo...</p>";
    
    // Método alternativo: baixar PHPMailer diretamente
    echo "<p>Baixando PHPMailer manualmente...</p>";
    
    $phpmailerFiles = [
        'src/PHPMailer.php' => 'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/PHPMailer.php',
        'src/SMTP.php' => 'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/SMTP.php',
        'src/Exception.php' => 'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/Exception.php'
    ];
    
    // Cria os diretórios necessários
    $dirs = ['vendor/phpmailer/phpmailer/src'];
    foreach ($dirs as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    
    // Baixa cada arquivo
    foreach ($phpmailerFiles as $path => $url) {
        $fullPath = 'vendor/phpmailer/phpmailer/' . $path;
        echo "<p>Baixando $path...</p>";
        if (downloadFile($url, $fullPath)) {
            echo "<p style='color: green;'>✓ $path baixado com sucesso</p>";
        }
    }
    
    // Cria um autoloader simples
    $autoloadContent = <<<'PHP'
<?php
spl_autoload_register(function ($class) {
    $prefix = 'PHPMailer\\PHPMailer\\';
    $base_dir = __DIR__ . '/phpmailer/src/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});
PHP;
    
    if (file_put_contents('vendor/autoload.php', $autoloadContent)) {
        echo "<p style='color: green;'>✓ Autoloader criado com sucesso</p>";
    } else {
        echo "<p style='color: red;'>✗ Erro ao criar autoloader</p>";
    }
    
} else {
    echo "<p style='color: green;'>PHP CLI disponível:</p>";
    echo "<pre>$phpVersion</pre>";
    
    // Executa o composer install
    echo "<p>Executando composer install...</p>";
    $output = [];
    $returnVar = 0;
    exec('php composer.phar install 2>&1', $output, $returnVar);
    
    echo "<pre>";
    if ($returnVar !== 0) {
        echo "<p style='color: red;'>Erro ao executar composer install:</p>";
        echo implode("\n", $output);
    } else {
        echo "<p style='color: green;'>PHPMailer instalado com sucesso!</p>";
        echo implode("\n", $output);
    }
    echo "</pre>";
}

// Verifica se a instalação foi bem sucedida
if (file_exists('vendor/phpmailer/phpmailer/src/PHPMailer.php')) {
    echo "<p style='color: green;'>✓ PHPMailer encontrado em vendor/phpmailer/phpmailer/src/PHPMailer.php</p>";
} else {
    echo "<p style='color: red;'>✗ PHPMailer não encontrado após a instalação</p>";
}

// Testa o autoloader
echo "<p>Testando autoloader...</p>";
try {
    require 'vendor/autoload.php';
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        echo "<p style='color: green;'>✓ Autoloader funcionando corretamente</p>";
    } else {
        echo "<p style='color: red;'>✗ Classe PHPMailer não encontrada após carregar autoloader</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Erro ao carregar autoloader: " . $e->getMessage() . "</p>";
}
?> 
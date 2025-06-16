<?php
require 'logger.php';

// Check PHP environment
echo "PHP Version: " . phpversion() . "\n";
echo "PHP SAPI: " . php_sapi_name() . "\n";
echo "PHP ini location: " . php_ini_loaded_file() . "\n\n";

// Check SMTP-related environment variables
$smtp_vars = [
    'SMTP',
    'SMTP_PORT',
    'SMTP_HOST',
    'SMTP_USERNAME',
    'SMTP_PASSWORD',
    'MAIL_HOST',
    'MAIL_PORT',
    'MAIL_USERNAME',
    'MAIL_PASSWORD'
];

echo "Environment Variables:\n";
foreach ($smtp_vars as $var) {
    echo "$var: " . (getenv($var) ? getenv($var) : 'not set') . "\n";
}

// Check PHP mail settings
echo "\nPHP Mail Settings:\n";
echo "mail.add_x_header: " . ini_get('mail.add_x_header') . "\n";
echo "mail.log: " . ini_get('mail.log') . "\n";
echo "sendmail_path: " . ini_get('sendmail_path') . "\n";
echo "SMTP: " . ini_get('SMTP') . "\n";
echo "smtp_port: " . ini_get('smtp_port') . "\n";

// Check loaded extensions
echo "\nLoaded Extensions:\n";
$required_extensions = ['openssl', 'sockets', 'mbstring'];
foreach ($required_extensions as $ext) {
    echo "$ext: " . (extension_loaded($ext) ? 'loaded' : 'not loaded') . "\n";
}

// Check if we can resolve Gmail's SMTP server
echo "\nDNS Resolution:\n";
$gmail_smtp = gethostbyname('smtp.gmail.com');
echo "smtp.gmail.com resolves to: $gmail_smtp\n";

// Check if we can connect to Gmail's SMTP port
echo "\nPort Connection Test:\n";
$connection = @fsockopen('smtp.gmail.com', 587, $errno, $errstr, 5);
echo "Can connect to smtp.gmail.com:587: " . ($connection ? 'yes' : 'no') . "\n";
if ($connection) {
    fclose($connection);
}
?> 
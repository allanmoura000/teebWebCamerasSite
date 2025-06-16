<?php
// Previne saída de erros antes dos headers
ob_start();

// Configurações do banco de dados
define('DB_HOST', '191.7.32.22');
define('DB_USER', 'estagio');
define('DB_PASS', 'Kgbe1771@');
define('DB_NAME', 'estagio_cameras');

// Configurações de log
define('LOG_DIR', __DIR__ . '/logs');
if (!file_exists(LOG_DIR)) {
    @mkdir(LOG_DIR, 0777, true);
}

// Configurações de erro
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', LOG_DIR . '/php_errors.log');

// Limpa o buffer de saída
ob_end_clean();
?> 
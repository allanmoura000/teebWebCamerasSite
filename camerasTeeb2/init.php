<?php
// Initialize error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

// Include required files
require_once __DIR__ . '/logger.php';
require_once __DIR__ . '/conexao.php';

// Initialize logger
Logger::init();
?> 
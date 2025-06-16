<?php
class Logger {
    private static $initialized = false;
    
    public static function init() {
        if (self::$initialized) {
            return true;
        }
        
        // Usa o diretório de logs definido no config.php
        if (defined('LOG_DIR')) {
            $logDir = LOG_DIR;
        } else {
            // Fallback para um diretório padrão
            $logDir = __DIR__ . DIRECTORY_SEPARATOR . 'logs';
        }
        
        // Garante que o diretório existe
        if (!file_exists($logDir)) {
            if (!@mkdir($logDir, 0777, true)) {
                error_log("Não foi possível criar o diretório de logs: $logDir");
                return false;
            }
        }
        
        self::$initialized = true;
        return true;
    }
    
    private static function log($level, $message) {
        if (!self::init()) {
            // Se não conseguir inicializar o logger, usa o error_log padrão do PHP
            error_log("[$level] " . $message);
            return;
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$level] " . $message;
        
        // Usa o error_log padrão do PHP, que é mais confiável
        error_log($logMessage);
    }
    
    public static function info($message) {
        self::log('INFO', $message);
    }
    
    public static function error($message) {
        self::log('ERROR', $message);
    }
    
    public static function debug($message) {
        self::log('DEBUG', $message);
    }
    
    public static function getLogs() {
        if (file_exists(self::$logFile)) {
            return file_get_contents(self::$logFile);
        }
        return "Nenhum log encontrado.";
    }
    
    public static function clear() {
        try {
            self::init();
            if (file_exists(self::$logFile)) {
                @unlink(self::$logFile);
            }
        } catch (Exception $e) {
            error_log("Erro ao limpar log: " . $e->getMessage());
        }
    }
} 
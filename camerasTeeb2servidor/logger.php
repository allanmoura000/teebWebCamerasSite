<?php
class Logger {
    private static $logFile = 'system.log';
    
    public static function log($message, $type = 'INFO') {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp][$type] $message" . PHP_EOL;
        file_put_contents(self::$logFile, $logMessage, FILE_APPEND);
    }
    
    public static function error($message) {
        self::log($message, 'ERROR');
    }
    
    public static function info($message) {
        self::log($message, 'INFO');
    }
    
    public static function debug($message) {
        self::log($message, 'DEBUG');
    }
    
    public static function getLogs() {
        if (file_exists(self::$logFile)) {
            return file_get_contents(self::$logFile);
        }
        return "Nenhum log encontrado.";
    }
    
    public static function clearLogs() {
        if (file_exists(self::$logFile)) {
            unlink(self::$logFile);
        }
    }
}
?> 
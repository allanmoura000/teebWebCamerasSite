<?php
header('Content-Type: application/json');

$logFiles = [
    __DIR__ . '/logs/app.log',
    __DIR__ . '/php_errors.log',
    __DIR__ . '/error_log'
];

$results = [];

foreach ($logFiles as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            $results[$file] = 'Log limpo com sucesso';
        } else {
            $results[$file] = 'Erro ao limpar log';
        }
    } else {
        $results[$file] = 'Arquivo não encontrado';
    }
}

echo json_encode([
    'success' => true,
    'message' => 'Logs limpos com sucesso',
    'details' => $results
]);
?> 
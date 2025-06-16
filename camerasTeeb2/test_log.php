<?php
require_once 'Logger.php';

// Tenta escrever um log de teste
try {
    Logger::info("Teste de log - " . date('Y-m-d H:i:s'));
    echo "Log escrito com sucesso\n";
} catch (Exception $e) {
    echo "Erro ao escrever log: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
}

// Verifica permissões do diretório e arquivo
$logDir = __DIR__ . '/logs';
$logFile = $logDir . '/app.log';

echo "\nVerificando permissões:\n";
echo "Diretório de logs existe: " . (file_exists($logDir) ? 'Sim' : 'Não') . "\n";
echo "Diretório de logs é gravável: " . (is_writable($logDir) ? 'Sim' : 'Não') . "\n";
echo "Arquivo de log existe: " . (file_exists($logFile) ? 'Sim' : 'Não') . "\n";
echo "Arquivo de log é gravável: " . (is_writable($logFile) ? 'Sim' : 'Não') . "\n";

// Tenta escrever diretamente no arquivo
try {
    file_put_contents($logFile, "Teste direto - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    echo "\nEscrita direta no arquivo bem sucedida\n";
} catch (Exception $e) {
    echo "\nErro na escrita direta: " . $e->getMessage() . "\n";
}

// Mostra o conteúdo atual do arquivo
echo "\nConteúdo atual do arquivo de log:\n";
if (file_exists($logFile)) {
    echo file_get_contents($logFile);
} else {
    echo "Arquivo não existe\n";
}
?> 
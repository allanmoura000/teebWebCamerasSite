<?php
// Desativa exibição de erros no output
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Inclui o logger
require_once __DIR__ . '/logger.php';

// Função para limpar o conteúdo do log
function clearLog($logFile) {
    if (file_exists($logFile)) {
        unlink($logFile);
        return true;
    }
    return false;
}

// Função para ler o conteúdo do log de forma segura
function readLog($logFile) {
    if (!file_exists($logFile)) {
        return "Arquivo de log não encontrado.";
    }
    
    $content = file_get_contents($logFile);
    if ($content === false) {
        return "Erro ao ler o arquivo de log.";
    }
    
    // Escapa caracteres especiais para HTML
    $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
    
    // Converte quebras de linha para <br>
    $content = nl2br($content);
    
    return $content;
}

// Processa ações
$action = $_GET['action'] ?? '';
$logType = $_GET['type'] ?? 'system';

$systemLogFile = __DIR__ . '/logs/system.log';
$phpLogFile = __DIR__ . '/logs/php_errors.log';

$message = '';
$content = '';

switch ($action) {
    case 'clear':
        if ($logType === 'system') {
            if (clearLog($systemLogFile)) {
                $message = "Log do sistema limpo com sucesso.";
            }
        } elseif ($logType === 'php') {
            if (clearLog($phpLogFile)) {
                $message = "Log de erros PHP limpo com sucesso.";
            }
        }
        break;
}

// Lê o conteúdo do log solicitado
if ($logType === 'system') {
    $content = readLog($systemLogFile);
    $title = "Log do Sistema";
} else {
    $content = readLog($phpLogFile);
    $title = "Log de Erros PHP";
}

require_once 'Logger.php';

header('Content-Type: application/json');

function checkLogs() {
    $logs = [];
    $logFiles = [
        'app' => __DIR__ . '/logs/app.log',
        'php_errors' => __DIR__ . '/php_errors.log',
        'error_log' => __DIR__ . '/error_log'
    ];

    foreach ($logFiles as $type => $file) {
        if (file_exists($file)) {
            $logs[$type] = [
                'exists' => true,
                'size' => filesize($file),
                'last_modified' => date('Y-m-d H:i:s', filemtime($file)),
                'content' => file_get_contents($file)
            ];
        } else {
            $logs[$type] = [
                'exists' => false,
                'message' => "Arquivo de log não encontrado: $file"
            ];
        }
    }

    // Verifica permissões do diretório de logs
    $logDir = __DIR__ . '/logs';
    if (!file_exists($logDir)) {
        mkdir($logDir, 0777, true);
    }

    $logs['permissions'] = [
        'logs_dir' => [
            'exists' => file_exists($logDir),
            'writable' => is_writable($logDir),
            'permissions' => substr(sprintf('%o', fileperms($logDir)), -4)
        ],
        'app_log' => [
            'exists' => file_exists($logFiles['app']),
            'writable' => file_exists($logFiles['app']) ? is_writable($logFiles['app']) : false,
            'permissions' => file_exists($logFiles['app']) ? substr(sprintf('%o', fileperms($logFiles['app'])), -4) : 'N/A'
        ]
    ];

    // Tenta escrever um log de teste
    try {
        Logger::info("Teste de log - " . date('Y-m-d H:i:s'));
        $logs['test_write'] = [
            'success' => true,
            'message' => 'Log de teste escrito com sucesso'
        ];
    } catch (Exception $e) {
        $logs['test_write'] = [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }

    return $logs;
}

// Verifica se é uma requisição AJAX
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($isAjax) {
    echo json_encode(checkLogs());
} else {
    // Interface HTML para visualização dos logs
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Verificação de Logs</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .log-section { margin-bottom: 20px; padding: 10px; border: 1px solid #ccc; }
            .log-content { white-space: pre-wrap; background: #f5f5f5; padding: 10px; }
            .error { color: red; }
            .success { color: green; }
            .warning { color: orange; }
            button { padding: 10px; margin: 5px; }
        </style>
    </head>
    <body>
        <h1>Verificação de Logs do Sistema</h1>
        <div id="logs"></div>
        <button onclick="checkLogs()">Atualizar Logs</button>
        <button onclick="clearLogs()">Limpar Logs</button>

        <script>
        function checkLogs() {
            fetch('check_logs.php', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                const logsDiv = document.getElementById('logs');
                logsDiv.innerHTML = '';

                // Permissões
                const permsSection = document.createElement('div');
                permsSection.className = 'log-section';
                permsSection.innerHTML = `
                    <h2>Permissões</h2>
                    <p>Diretório de Logs: ${data.permissions.logs_dir.exists ? '✅' : '❌'} 
                       (${data.permissions.logs_dir.writable ? 'Gravável' : 'Não gravável'})</p>
                    <p>Arquivo app.log: ${data.permissions.app_log.exists ? '✅' : '❌'} 
                       (${data.permissions.app_log.writable ? 'Gravável' : 'Não gravável'})</p>
                `;
                logsDiv.appendChild(permsSection);

                // Logs
                for (const [type, log] of Object.entries(data)) {
                    if (type === 'permissions' || type === 'test_write') continue;
                    
                    const section = document.createElement('div');
                    section.className = 'log-section';
                    
                    let content = `<h2>${type}</h2>`;
                    if (log.exists) {
                        content += `
                            <p>Tamanho: ${(log.size / 1024).toFixed(2)} KB</p>
                            <p>Última modificação: ${log.last_modified}</p>
                            <div class="log-content">${log.content}</div>
                        `;
                    } else {
                        content += `<p class="error">${log.message}</p>`;
                    }
                    
                    section.innerHTML = content;
                    logsDiv.appendChild(section);
                }

                // Teste de escrita
                const testSection = document.createElement('div');
                testSection.className = 'log-section';
                testSection.innerHTML = `
                    <h2>Teste de Escrita</h2>
                    <p class="${data.test_write.success ? 'success' : 'error'}">
                        ${data.test_write.message || data.test_write.error}
                    </p>
                `;
                logsDiv.appendChild(testSection);
            })
            .catch(error => {
                console.error('Erro ao verificar logs:', error);
                document.getElementById('logs').innerHTML = `
                    <div class="log-section error">
                        <h2>Erro</h2>
                        <p>Erro ao verificar logs: ${error.message}</p>
                    </div>
                `;
            });
        }

        function clearLogs() {
            if (confirm('Tem certeza que deseja limpar todos os logs?')) {
                fetch('clear_logs.php', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    checkLogs();
                })
                .catch(error => {
                    console.error('Erro ao limpar logs:', error);
                    alert('Erro ao limpar logs: ' + error.message);
                });
            }
        }

        // Verifica logs ao carregar a página
        checkLogs();
        </script>
    </body>
    </html>
    <?php
}
?> 
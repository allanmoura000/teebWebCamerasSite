<?php
ob_start(); // Inicia o buffer de saída para evitar envio prematuro de headers
ini_set('display_errors', 0); // Desativa exibição de erros no frontend
error_reporting(E_ALL); // Mantém registro completo de erros
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log'); // Arquivo de log personalizado

include 'conexao.php'; // Conexão com o banco de dados

// Função para log
function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, 'visualizacoes_errors.log');
}

$camera_id = isset($_GET['camera_id']) ? (int)$_GET['camera_id'] : 0;
$acao = isset($_GET['acao']) ? $_GET['acao'] : '';

// Log da requisição
logError("Requisição recebida - Camera ID: $camera_id, Ação: $acao");

// Validação básica
if ($camera_id <= 0) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(["error" => "ID de câmera inválido"]);
    logError("ID de câmera inválido: $camera_id");
    exit;
}

// Inicia transação para garantir consistência
$conexao->begin_transaction();

try {
    // Verifica se o registro existe e obtém os valores atuais
    $stmt = $conexao->prepare("
        SELECT total, online 
        FROM visualizacoes 
        WHERE camera_id = ?
        FOR UPDATE
    ");
    $stmt->bind_param("i", $camera_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current = $result->fetch_assoc();
    $stmt->close();

    if (!$current) {
        // Se não existe, cria o registro
        $stmt = $conexao->prepare("
            INSERT INTO visualizacoes (camera_id, total, online, ultima_atualizacao) 
            VALUES (?, 0, 0, NOW())
        ");
        $stmt->bind_param("i", $camera_id);
        $stmt->execute();
        $stmt->close();
        $current = ['total' => 0, 'online' => 0];
        logError("Novo registro criado para câmera $camera_id");
    } else {
        logError("Registro existente encontrado para câmera $camera_id - Total: {$current['total']}, Online: {$current['online']}");
    }

    // Processa ações usando prepared statements
    switch ($acao) {
        case 'abrir':
            // Incrementa total e online
            $stmt = $conexao->prepare("
                UPDATE visualizacoes 
                SET total = total + 1,
                    online = online + 1,
                    ultima_atualizacao = NOW()
                WHERE camera_id = ?
            ");
            $stmt->bind_param("i", $camera_id);
            $stmt->execute();
            $affected = $stmt->affected_rows;
            $stmt->close();
            
            if ($affected > 0) {
                $current['total']++;
                $current['online']++;
                logError("Ação 'abrir' executada com sucesso para câmera $camera_id - Novo total: {$current['total']}, Novo online: {$current['online']}");
            } else {
                logError("Ação 'abrir' não afetou nenhuma linha para câmera $camera_id");
            }
            break;
            
        case 'fechar':
            // Decrementa apenas online
            if ($current['online'] > 0) {
                $stmt = $conexao->prepare("
                    UPDATE visualizacoes 
                    SET online = online - 1,
                        ultima_atualizacao = NOW()
                    WHERE camera_id = ?
                    AND online > 0
                ");
                $stmt->bind_param("i", $camera_id);
                $stmt->execute();
                $affected = $stmt->affected_rows;
                $stmt->close();
                
                if ($affected > 0) {
                    $current['online']--;
                    logError("Ação 'fechar' executada com sucesso para câmera $camera_id - Novo online: {$current['online']}");
                } else {
                    logError("Ação 'fechar' não afetou nenhuma linha para câmera $camera_id");
                }
            } else {
                logError("Ação 'fechar' ignorada para câmera $camera_id - Online já está em 0");
            }
            break;

        case 'ping':
            // Apenas atualiza o timestamp
            $stmt = $conexao->prepare("
                UPDATE visualizacoes 
                SET ultima_atualizacao = NOW()
                WHERE camera_id = ?
                AND online > 0
            ");
            $stmt->bind_param("i", $camera_id);
            $stmt->execute();
            $affected = $stmt->affected_rows;
            $stmt->close();
            logError("Ação 'ping' executada para câmera $camera_id - Linhas afetadas: $affected");
            break;

        case 'status':
            // Limpa visualizações online antigas
            $stmt = $conexao->prepare("
                UPDATE visualizacoes 
                SET online = 0 
                WHERE camera_id = ? 
                AND ultima_atualizacao < DATE_SUB(NOW(), INTERVAL 5 MINUTE)
            ");
            $stmt->bind_param("i", $camera_id);
            $stmt->execute();
            $affected = $stmt->affected_rows;
            $stmt->close();
            
            if ($affected > 0) {
                $current['online'] = 0;
                logError("Ação 'status' limpou visualizações antigas para câmera $camera_id");
            }
            break;

        default:
            logError("Ação desconhecida: $acao");
            break;
    }

    // Commit da transação
    $conexao->commit();
    logError("Transação commitada para câmera $camera_id - Total final: {$current['total']}, Online final: {$current['online']}");

    // Retorna os dados atualizados
    $data = [
        'total' => (int)$current['total'],
        'online' => (int)$current['online']
    ];

} catch (Exception $e) {
    // Rollback em caso de erro
    $conexao->rollback();
    logError("Erro na operação: " . $e->getMessage());
    $data = ['total' => 0, 'online' => 0];
}

// Limpa buffer e envia resposta
ob_end_clean();
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
echo json_encode($data);
exit;
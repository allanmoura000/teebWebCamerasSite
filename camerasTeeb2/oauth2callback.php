<?php
require 'vendor/autoload.php';
require 'logger.php';

use Google\Client as GoogleClient;
use Google\Service\Gmail;

session_start();

try {
    $config = require 'gmail_api_config.php';
    
    $client = new GoogleClient();
    $client->setClientId($config['client_id']);
    $client->setClientSecret($config['client_secret']);
    $client->setRedirectUri($config['redirect_uri']);
    $client->setScopes($config['scopes']);
    $client->setAccessType($config['access_type']);
    $client->setPrompt($config['prompt']);
    
    if (isset($_GET['code'])) {
        // Troca o código de autorização por tokens
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        
        if (isset($token['error'])) {
            throw new Exception('Erro ao obter token: ' . $token['error']);
        }
        
        // Salva os tokens
        $_SESSION['access_token'] = $token['access_token'];
        if (isset($token['refresh_token'])) {
            $_SESSION['refresh_token'] = $token['refresh_token'];
        }
        
        // Salva os tokens em arquivo para uso futuro
        $token_data = [
            'access_token' => $token['access_token'],
            'refresh_token' => $token['refresh_token'] ?? null,
            'created' => time(),
            'expires_in' => $token['expires_in'] ?? 3600
        ];
        
        file_put_contents(__DIR__ . '/gmail_token.json', json_encode($token_data));
        
        Logger::info("Tokens Gmail API salvos com sucesso");
        
        // Redireciona de volta para a página principal
        header('Location: index.php');
        exit;
    } else {
        // Se não há código, inicia o processo de autorização
        $auth_url = $client->createAuthUrl();
        header('Location: ' . $auth_url);
        exit;
    }
} catch (Exception $e) {
    Logger::error("Erro no OAuth2: " . $e->getMessage());
    echo "Erro na autenticação: " . $e->getMessage();
}
?> 
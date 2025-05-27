<?php
// Configurações de conexão com o banco de dados
$host = "191.7.32.22";  // Servidor do banco
$usuario = "estagio";    // Usuário do banco
$senha = "Kgbe1771@";          // Senha do banco
$banco = "estagio_cameras"; // Nome do banco de dados

// Conexão com o banco
$conexao = new mysqli($host, $usuario, $senha, $banco); 

// Verificação de erro na conexão
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}
?>

<?php
include 'conexao.php'; // Arquivo de conexão com o banco

header('Content-Type: application/json');

/**
 * Filtra palavras ofensivas do comentário.
 * Substitui palavras proibidas por "***"
 */
function filtrarComentario($comentario) {
    $palavrasProibidas = ['palavrão1', 'palavrão2', 'palavrão3']; // Liste as palavras proibidas aqui
    return preg_replace('/\b(' . implode('|', $palavrasProibidas) . ')\b/i', '***', $comentario);
}

// Verifica se o comentário foi enviado via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $camera_id = intval($_POST['camera_id']);
    $usuario = trim($_POST['usuario']) ?: "Anônimo";
    $comentario = trim($_POST['comentario']);

    if (!empty($comentario)) {
        $comentarioFiltrado = filtrarComentario($comentario);

        // Insere no banco de dados
        $stmt = $conexao->prepare("INSERT INTO comentarios (camera_id, usuario, comentario, data) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $camera_id, $usuario, $comentarioFiltrado);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "comentario" => $comentarioFiltrado, "usuario" => $usuario]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao salvar comentário."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "O comentário não pode estar vazio."]);
    }
}

$conexao->close();
?>

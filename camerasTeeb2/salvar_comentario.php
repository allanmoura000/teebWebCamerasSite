<?php
include 'conexao.php'; // Conexão com o banco de dados
header('Content-Type: application/json');

/**
 * Filtra o comentário para:
 * - Substituir os palavrões definidos por '***'
 * - Remover os caracteres '***', espaços e pontuações para verificar se sobra texto válido
 * Se o comentário ficar vazio após a limpeza, retorna false.
 */
function filtrarComentario($comentario) {
    // Lista de palavrões a serem censurados (adicione os termos reais)
    $palavroes = ['palavrao1', 'palavrao2', 'palavrao3'];
    
    // Substitui os palavrões por '***', ignorando maiúsculas/minúsculas
    $comentarioFiltrado = str_ireplace($palavroes, '***', $comentario);
    
    // Remove as ocorrências de '***' e depois elimina tudo que não seja letra ou número.
    // Essa etapa remove espaços, pontuação e outros caracteres.
    $comentarioLimpo = preg_replace('/[^\p{L}\p{N}]+/u', '', str_ireplace('***', '', $comentarioFiltrado));
    
    // Se não sobrar nenhum caractere válido, o comentário é considerado inválido.
    if ($comentarioLimpo === '') {
        return false;
    }
    
    return $comentarioFiltrado;
}

// Verifica se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $camera_id = $_POST['camera_id'];
    $usuario   = $_POST['usuario'];
    $comentario = $_POST['comentario'];

    // Verifica se os campos foram preenchidos
    if (!empty($camera_id) && !empty($usuario) && !empty($comentario)) {
        $comentarioFiltrado = filtrarComentario($comentario);

        // Se o comentário for composto apenas por palavrões, bloqueia a inserção
        if ($comentarioFiltrado === false) {
            echo json_encode(["success" => false, "message" => "Comentário não permitido."]);
            exit;
        }

        // Prepara e executa a inserção do comentário filtrado no banco de dados
        $sql = "INSERT INTO comentarios (camera_id, usuario, comentario, data) VALUES (?, ?, ?, NOW())";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("iss", $camera_id, $usuario, $comentarioFiltrado);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Comentário salvo!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao salvar comentário."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Preencha todos os campos."]);
    }
}

$conexao->close();
?>

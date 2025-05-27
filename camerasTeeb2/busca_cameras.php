<?php
// Inclui a conexão com o banco de dados
include 'conexao.php';

if (isset($_GET['query'])) {
    $search = trim($_GET['query']);

    // Busca no banco de dados
    $sql = "SELECT nome FROM cad_cameras WHERE nome LIKE ? LIMIT 5";
    $stmt = $conexao->prepare($sql);
    $searchTerm = "%$search%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $sugestoes = [];
    while ($row = $resultado->fetch_assoc()) {
        $sugestoes[] = $row['nome'];
    }

    echo json_encode($sugestoes);
}
?>

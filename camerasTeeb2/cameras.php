<?php
@session_start();

include 'conexao.php';

$user_id = isset($_SESSION['userId']) ? intval($_SESSION['userId']) : 0;

/**
 * Função que obtém um resumo da história do local a partir da API do Wikipedia.
 * (Mesma função do código anterior)
 */
function getWikipediaHistory($localName)
{
  // ... (mesmo código de antes)
}

/**
 * Função que atualiza o campo 'historia' da câmera no banco, caso esteja vazio.
 */
function updateCameraHistory($conexao, $id, $nome)
{
  // ... (mesmo código de antes)
}

// Verifica se há um termo de busca
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
  $sql = "SELECT id, nome, iframe, imagem, historia FROM cad_cameras WHERE nome LIKE ?";
  $stmt = $conexao->prepare($sql);
  $searchTerm = "%$search%";
  $stmt->bind_param("s", $searchTerm);
  $stmt->execute();
  $resultado = $stmt->get_result();
} else {
  $sql = "SELECT id, nome, iframe, imagem, historia FROM cad_cameras";
  $resultado = $conexao->query($sql);
}

echo '<section class="camera-section" id="cameras">';
echo '<div class="cameracamera">';
echo '<h1>Câmeras</h1>';
echo '<div class="camera-videos">';

if ($resultado->num_rows > 0) {
  while ($row = $resultado->fetch_assoc()) {

    $id = $row['id'];
    $nome = $row['nome'];
    $imagem = $row['imagem'];
    $iframe = $row['iframe'];
    $historia = $row['historia'];

    if (empty($historia)) {
      $historia = updateCameraHistory($conexao, $id, $nome);
    }

    // CONSULTA DAS AVALIAÇÕES
    $sql_rating = "SELECT AVG(nota) as media, COUNT(*) as total FROM avaliacoes WHERE camera_id = ?";
    $stmt_rating = $conexao->prepare($sql_rating);
    $stmt_rating->bind_param("i", $id);
    $stmt_rating->execute();
    $result_rating = $stmt_rating->get_result();
    $row_rating = $result_rating->fetch_assoc();
    $media = ($row_rating['media']) ? number_format($row_rating['media'], 1) : '0.0';
    $total = ($row_rating['total']) ? $row_rating['total'] : '0';

    // Recupera a avaliação do usuário (se logado)
    $userRating = 0;
    if ($user_id > 0) {
      $stmt_userRating = $conexao->prepare("SELECT nota FROM avaliacoes WHERE camera_id = ? AND user_id = ?");
      $stmt_userRating->bind_param("ii", $id, $user_id);
      $stmt_userRating->execute();
      $result_userRating = $stmt_userRating->get_result();
      if ($result_userRating->num_rows > 0) {
        $row_userRating = $result_userRating->fetch_assoc();
        $userRating = intval($row_userRating['nota']);
      }
      $stmt_userRating->close();
    }

    // Arredonda a média para exibir as estrelas
    $roundedRating = round($media);

    // CONSULTA DAS VISUALIZAÇÕES (executada antes de montar o card)
    $sql_views = "SELECT total, online FROM visualizacoes WHERE camera_id = ?";
    $stmt_views = $conexao->prepare($sql_views);
    $stmt_views->bind_param("i", $id);
    $stmt_views->execute();
    $result_views = $stmt_views->get_result();
    $row_views = $result_views->fetch_assoc();
    $totalViews = isset($row_views['total']) ? $row_views['total'] : 0;
    $onlineViews = isset($row_views['online']) ? $row_views['online'] : 0;

    // Exibe o card da câmera
    echo '<div class="video-card" data-id="' . $id . '">';
    echo '<div class="card-image-container">';
    echo '<img src="' . $imagem . '" alt="' . $nome . '">';
    echo '<div class="card-overlay">';
    // Cabeçalho com o nome da câmera
    echo '<div class="camera-header">';
    echo '<h3 class="camera-name">' . $nome . '</h3>';
    echo '</div>'; // .camera-header

    // Seção que agrupa avaliação e estatísticas
    echo '<div class="camera-rating-info">';
    // Avaliação
    echo '<div class="camera-rating">';
    echo '<div class="star-rating">';
    $starsToFill = ($userRating > 0) ? $userRating : $roundedRating;
    for ($i = 1; $i <= 5; $i++) {
      if ($i <= $starsToFill) {
        echo "<span class='star filled'>&#9733;</span>";
      } else {
        echo "<span class='star'>&#9734;</span>";
      }
    }
    echo '</div>'; // .star-rating
    echo '<div class="rating-text">';
    echo '<span class="rating-average">' . $media . '</span> de <span class="rating-count">' . $total . '</span> avaliações';
    echo '</div>';
    echo '</div>'; // .camera-rating

    // Estatísticas de visualizações
    echo '<div class="camera-stats">';
    echo '<span class="stats-views"><i class="fa fa-eye"></i> ' . $totalViews . ' visualizações</span>';
    echo '<span class="stats-live"><i class="fa fa-circle"></i> ' . $onlineViews . ' ao vivo</span>';
    echo '</div>'; // .camera-stats
    echo '</div>'; // .camera-rating-info
    echo '</div>'; // .card-overlay
    echo '</div>'; // .card-image-container
    echo '</div>'; // .video-card

    // Prepara o iframe com proxy
    $iframe_proxy = str_replace("http://", "https://corsproxy.io/?", $iframe);

    // Cria o popup para exibição da câmera
    echo "<div id='popup-{$id}' class='popup'>"; // Notação mais clara com chaves
    echo "  <div class='popup-content'>";
    echo "    <div class='video-container'>";
    echo "      <span class='close' data-popup-id='popup-{$id}'>&times;</span>";
    echo "      <div id='video-container-{$id}' style='position: relative;'>"; // ID único
    echo "        <iframe id='iframe-{$id}' data-src='{$iframe_proxy}' title='{$nome}' frameborder='0' allow='autoplay; encrypted-media; fullscreen; picture-in-picture' allowfullscreen playsinline webkit-playsinline style='width:100%; pointer-events: none;'></iframe>"; // ID único
    echo "        <div style='position: absolute; bottom: 10px; right: 10px; pointer-events: auto; z-index: 10000;'>";
    echo "          <button onclick=\"enterFullScreen('video-container-{$id}')\" style='background-color: rgb(241, 196, 0); color: black; border: none; padding: 6px; border-radius: 12%; display: block;'>"; // Passa o ID correto
    echo "            <i class='fa fa-expand' aria-hidden='true'></i>";
    echo "          </button>";
    echo "          <button onclick='exitFullScreen()' style='background-color: rgb(241, 196, 0); color: black; border: none; padding: 6px; border-radius: 12%; display: none;'>";
    echo "            <i class='fa fa-compress' aria-hidden='true'></i>";
    echo "          </button>";
    echo "        </div>";
    echo "      </div>";
    // Popup com avaliação e visualizações
    echo "      <div id='video-description-$id' class='video-description'>";
    echo '<div class="popup-header">';
    echo '<h2 class="camera-title">' . $nome . '</h2>';
    echo '<div class="rating-container" data-camera-id="' . $id . '">';
    echo '<div class="star-rating">';
    for ($i = 1; $i <= 5; $i++) {
      $class = ($userRating > 0 && $i <= $userRating) ? 'selected' : '';
      echo "<span class='star $class' data-value='{$i}'>&#9733;</span>";
    }
    echo '</div>';
    echo '<div class="rating-info">';
    echo '<span class="rating-average">' . $media . '</span> (de <span class="rating-count">' . $total . '</span> avaliações)';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo "      <div class='views-container'>";
    echo "        <span class='views-item'><i class='fa fa-eye'></i> <span id='total-$id'>" . $totalViews . "</span> visualizações</span>";
    echo "        <span class='views-item live'><i class='fa fa-circle'></i> <span id='online-$id'>" . $onlineViews . "</span> ao vivo</span>";
    echo "      </div>";
    $historiaCurta = substr($historia, 0, 2000);
    echo "      <div class='video-description'>";
    echo "        <p id='historia-$id' class='historia-text'>$historiaCurta...</p>";
    echo "        <button class='ver-mais' onclick='toggleHistoria($id)'>Ver mais</button>";
    echo "      </div>";
    echo "      </div>"; // .video-description (popup)
    echo "    </div>";
    // Seção de comentários
    echo "    <div class='comentarios'>";
    echo "      <div class='comments-list' id='comments-list-$id'>";
    echo "        <h1>Comentários</h1>";
    // Carregar comentários existentes via AJAX
    echo "      </div>";
    echo "      <div class='comment-input-container'>";
    echo "        <textarea id='new-comment-$id' placeholder='Escreva um comentário...'></textarea>";
    echo "        <button id='submit-comment-$id' type='button'>Publicar</button>";
    echo "      </div>";
    echo "    </div>";
    echo "  </div>";
    echo "</div>"; // .popup
  }
} else {
  echo "<p>Nenhuma câmera encontrada.</p>";
}

echo '</div>'; // .camera-videos
echo '</div>'; // .cameracamera

// Card lateral (side card)
echo '<div class="side-card">';
echo '<img src="imagens/postage.png" frameborder="0" width="300" alt="" class="add-img">';
echo '</div>';

echo '</section>';
?>
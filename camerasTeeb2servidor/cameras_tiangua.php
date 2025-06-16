<?php
// Inclui a conexão com o banco de dados
include 'conexao.php';

// Consulta para buscar apenas câmeras de Tianguá
$sql = "SELECT id, nome, iframe, imagem FROM cad_cameras WHERE localizacao = 'Tianguá'";
$resultado = $conexao->query($sql);

// Seção específica para as câmeras de Tianguá
echo '<section class="camera-section" id="cameras-tiangua">';
echo '<div class="cameracamera">';
echo '    <h1>Câmeras de Tianguá</h1>';
echo '    <div class="camera-videos">';

if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $id = $row['id'];
        $nome = $row['nome'];
        $imagem = $row['imagem'];
        $iframe = $row['iframe'];

        echo '<div class="video-card" data-id="' . $id . '">';
        echo '<div class="card-image-container">';
          echo '<img src="' . $imagem . '" alt="' . $nome . '">';
          echo '<div class="card-overlay">';
            echo '<div class="camera-header">';
              echo '<h3 class="camera-name">' . $nome . '</h3>';
            echo '</div>';
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
                echo '</div>'; // star-rating
                echo '<div class="rating-text">';
                  echo '<span class="rating-average">' . $media . '</span> de <span class="rating-count">' . $total . '</span> avaliações';
                echo '</div>';
              echo '</div>'; // camera-rating
      
              // Visualizações
              echo '<div class="camera-stats">';
                echo '<span class="stats-views"><i class="fa fa-eye"></i> ' . $totalViews . ' visualizações</span>';
                echo '<span class="stats-live"><i class="fa fa-circle"></i> ' . $onlineViews . ' ao vivo</span>';
              echo '</div>'; // camera-stats
      
            echo '</div>'; // camera-rating-info
          echo '</div>'; // card-overlay
        echo '</div>'; // card-image-container
      echo '</div>'; // video-card
      

        $iframe_proxy = str_replace("http://", "https://corsproxy.io/?", $iframe);

        echo "<div id='popup-$id' class='popup'>";
        echo "  <div class='popup-content'>";
        echo "    <div class='video-container'>";
        echo "      <span class='close' data-popup-id='popup-$id'>&times;</span>";
        echo "      <div id='video-container-$id'>";
        echo "        <iframe id='iframe-$id' data-src='$iframe_proxy' title='$nome' frameborder='0' allow='autoplay; encrypted-media; fullscreen; picture-in-picture' allowfullscreen playsinline webkit-playsinline></iframe>";
        echo "      </div>";
        echo "      <div id='video-description-$id'>";
        echo "        <h2>$nome</h2>";
        echo "      </div>";
        echo "    </div>";
        echo "  </div>";
        echo "</div>";
    }
} else {
    echo "<p>Nenhuma câmera encontrada em Tianguá.</p>";
}

echo '</div>';
echo '</div>';
echo '</section>';
?>

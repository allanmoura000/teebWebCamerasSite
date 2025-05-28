<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="theme-color" content="#FFFFFF"> 
    <title>CamerasTeeb</title>
    <link rel="shortcut icon" href="/imagens/icon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="newproject.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="mapa.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />


</head>

<body>
    <div class="overlay"></div>

    <header>
        <div class="container">

            <div class="logo">
                <a href="index.php"><img src="imagens/TeebLogo_amarela.png" alt="Logo"></a>
            </div>
            <div class="menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
            <nav id="nav" class="animate__animated">
                <ul id="navLinks">
                    <li>
                        <a href="index.php">
                            <i class="fas fa-home"></i>
                            <span>Início</span>
                        </a>
                    </li>
                    <li class="dropdown">
                        <a href="#cameras" class="dropdown-toggle">
                            <i class="fas fa-video"></i>
                            <span>Câmeras</span>
                        </a>
                    </li>
                    <li>
                        <a href="#map">
                            <i class="fas fa-map-marked-alt"></i>
                            <span>Mapa</span>
                        </a>
                    </li>
                    <li>
                        <a href="#creditos">
                            <i class="fas fa-users"></i>
                            <span>Créditos</span>
                        </a>
                    </li>
                    <li>
                        <a href="#newsStripe">
                            <i class="fas fa-newspaper"></i>
                            <span>Notícias</span>
                        </a>
                    </li>

                    <!-- Ícone ao lado do nome "Entrar" -->
                    <li class="profile-icon">
                        <a href="cadastro.php" id="auth-link">
                            <i class="fas fa-user-circle"></i>
                            <span>Entrar</span>
                        </a>
                    </li>

                    <!-- Botão de modo escuro -->
                    <li class="dark-mode-container">
                        <button id="dark-mode-toggle">
                            <i id="theme-icon" class="fas fa-moon"></i>
                            <span class="dark-mode-text">Modo Escuro</span>
                        </button>
                        <div class="switch-container">
                            <label class="switch">
                                <input type="checkbox" id="dark-mode-switch">
                                <span class="toggle-btn round"></span>
                            </label>
                        </div>
                    </li>



                </ul>

            </nav>
        </div>
    </header>



   <div class="search-bar">
        <form class="search-form" action="cameras_filtro.php" method="GET">
            <input type="text" name="search" id="search-input" class="search-input" placeholder="Buscar Cameras"
                autocomplete="off">
            <button type="submit" class="search-btn" id="search-button">Buscar</button>
        </form>
        <div id="autocomplete-results" class="autocomplete-results"></div>
    </div>

    <div class="slider-container">
        <div class="slider">
            <div class="slide active" id="slide-1" data-camera-id="2">
                <img src="imagens/pracaAdventista.jpeg" alt="Slide 1">
                <h3>Praça do Adventista</h3>
                <p>Local histórico de encontro.</p>
            </div>
            <div class="slide" id="slide-2" data-camera-id="10">
                <img src="imagens/pracacigano.jpeg" alt="Slide 2">
                <h3>Praça do Cigano</h3>
                <p>Cultura e tradições em harmonia.</p>
            </div>
            <div class="slide no-popup" id="slide-ad">
                
                    <picture>
                        <source media="(max-width: 768px)" srcset="imagens/bannerSlider2.png">
                        <source media="(min-width: 769px)" srcset="imagens/bannerSlider.png">
                        <img src="imagens/bannerSlider.png" alt="Propaganda">
                    </picture>
                    <h3>Vem ser TEEB</h3>
                    <p>Confira nossas ofertas!</p>
                
            </div>


<script>
    document.getElementById('slide-ad').addEventListener('click', () => {
  let adicionais = [];

  document.querySelectorAll('.adicional-item').forEach(item => {
    const quantity = parseInt(item.querySelector('.quantity').textContent || "0");
    if (quantity > 0) {
      const name = item.getAttribute('data-name');
      adicionais.push(`${name} x${quantity}`);
    }
  });

  let totalValueElement = document.querySelector('#totalValue');
  let totalValue = totalValueElement ? totalValueElement.textContent : "0.00";

  let mensagem = `Olá! 😊\n\n` +
    `*Pedido de Contratação de Plano*\n` +
    `📡 *Plano*: 200 MEGA\n` +
    `💵 *Preço do Plano*: R$69,99\n\n` +
    `*Adicionais Selecionados:*\n` +
    (adicionais.length ? adicionais.map(item => `➕ ${item}`).join("\n") : "Nenhum adicional selecionado.") +
    `\n\n` +
    `🧾 *Total Geral*: R$69,99\n\n` +
    `Obrigado!`;

  let telefoneVendedor = "+5588993253038";
  let url = `https://api.whatsapp.com/send?phone=${telefoneVendedor}&text=${encodeURIComponent(mensagem)}`;

  window.location.href = url;
});

</script>

            <div class="slide" id="slide-3" data-camera-id="6">
                <img src="imagens/Bosco.jpeg" alt="Slide 3">
                <h3>Sítio do Bosco</h3>
                <p>Uma paisagem deslumbrante.</p>
            </div>
        </div>
        <div class="slider-nav">
            <button class="prev">&#10094;</button>
            <button class="next">&#10095;</button>
        </div>
        <div class="indicators">
            <span class="dot active"></span>
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>
    </div>
    <a href="#nav" class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </a>

    <main>

        <?php include 'cameras.php'; ?>


        <div class="mapa-section" id="mapa">
            <div class="mapamundi">
                <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js"></script>
                <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
                <div class="mapa-section" id="mapa">
                    <div class="mapamundi">
                        <div id="map"></div>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js"></script>
                        <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                // Event delegation para os cards das câmeras (lista e mapa)
                                document.querySelectorAll(".video-card, .map-video-card").forEach(card => {
                                    card.addEventListener("click", function () {
                                        const cameraId = card.getAttribute("data-id") || card.getAttribute("data-camera-id");
                                        if (cameraId) {
                                            abrirPopup(cameraId);
                                            // Se existir um popup do Leaflet aberto, feche-o
                                            if (typeof map !== "undefined" && map.closePopup) {
                                                map.closePopup();
                                            }
                                        } else {
                                            console.error("Identificador da câmera não encontrado no card.");
                                        }
                                    });
                                });
                            });

                            // Função para abrir o popup completo (vídeo, comentários, etc.)
                            function abrirPopup(cameraId) {
                                console.log(`Abrindo popup para câmera ID: ${cameraId}`);
                                const popup = document.getElementById(`popup-${cameraId}`);
                                if (popup) {
                                    popup.style.display = "flex";
                                    document.body.classList.add("no-scroll");
                                    carregarComentarios(cameraId);
                                    const iframe = popup.querySelector("iframe");
                                    if (iframe && !iframe.src) {
                                        const videoSrc = iframe.getAttribute("data-src");
                                        iframe.src = videoSrc;
                                    }
                                } else {
                                    console.error(`Popup com ID popup-${cameraId} não encontrado.`);
                                }
                            }

                            // Função para fechar o popup completo
                            function fecharPopup(cameraId) {
                                console.log(`Fechando popup para câmera ID: ${cameraId}`);
                                const popup = document.getElementById(`popup-${cameraId}`);
                                if (popup) {
                                    // Opcional: remover o src do iframe para descarregar o vídeo
                                    const iframe = popup.querySelector("iframe");
                                    if (iframe) {
                                        iframe.src = "";
                                    }
                                    popup.style.display = "none";
                                    document.body.classList.remove("no-scroll");
                                } else {
                                    console.error(`Popup com ID popup-${cameraId} não encontrado.`);
                                }
                            }

                            // Função para exibir um card dentro do marcador no mapa
                            function exibirCardNoMapa(camera, marker) {
                                console.log(`Exibindo card da câmera ${camera.id} no mapa`);
                                let iframe_proxy = camera.iframe ? camera.iframe.replace("http://", "https://corsproxy.io/?") : "";
                                var cardHtml = `
              <div class="map-video-card" data-id="${camera.id}" onclick="registrarVisualizacao(${camera.id}); abrirPopup(${camera.id});">
                <img src="${camera.imagem}" alt="${camera.nome}">
                <p>${camera.nome}</p>
                <div class="views-container">
           
              </div>
            `;
                                var popup = L.popup({ keepInView: true, autoClose: false, closeOnClick: false })
                                    .setLatLng(marker.getLatLng())
                                    .setContent(cardHtml)
                                    .openOn(map);
                                // Se o popup completo ainda não existe, adiciona-o ao body
                                if (!document.getElementById(`popup-${camera.id}`)) {
                                    let popupHtml = `
                <div id="popup-${camera.id}" class="popup">
                  <div class="popup-content">
                    <div class="video-container">
                      <span class="close" data-popup-id="popup-${camera.id}" onclick="fecharPopup(${camera.id})">&times;</span>
                      <div id="video-container-${camera.id}">
                        ${iframe_proxy ? `<iframe id="iframe-${camera.id}" data-src="${iframe_proxy}" frameborder="0" allow="autoplay; encrypted-media; fullscreen; picture-in-picture" allowfullscreen playsinline webkit-playsinline></iframe>` : "<p>Vídeo não disponível</p>"}
                      </div>
                      <div id="video-description-${camera.id}">
                        <h2>${camera.nome}</h2>
                      </div>
                    </div>
                  </div>
                </div>
              `;
                                    document.body.insertAdjacentHTML("beforeend", popupHtml);
                                }
                            }

                            // Função para carregar as câmeras do banco de dados e adicioná-las ao mapa
                            function carregarCameras() {
                                fetch('teste_json.php')
                                    .then(response => response.json())
                                    .then(cameras => {
                                        console.log("Dados carregados do JSON:", cameras);
                                        if (Array.isArray(cameras) && cameras.length > 0) {
                                            cameras.forEach(camera => {
                                                if (camera.latitude && camera.longitude) {
                                                    let latitude = parseFloat(camera.latitude);
                                                    let longitude = parseFloat(camera.longitude);
                                                    if (!isNaN(latitude) && !isNaN(longitude)) {
                                                        var marker = L.marker([latitude, longitude]).addTo(map);

                                                        // Evento de clique para animar o zoom e exibir o card
                                                        marker.on('click', function () {
                                                            // Fecha o popup aberto anteriormente
                                                            map.closePopup();

                                                            // Desabilita o arraste do mapa para garantir que a animação ocorra sem interferência
                                                            map.dragging.disable();

                                                            // Animação de flyTo para centralizar e aproximar do marcador
                                                            map.flyTo([latitude, longitude], 17, {
                                                                animate: true,
                                                                duration: 1,
                                                                easeLinearity: 0.8
                                                            });

                                                            // Quando a animação terminar, reabilita o arraste e abre o popup
                                                            map.once('moveend', function () {
                                                                map.dragging.enable();
                                                                exibirCardNoMapa(camera, marker);
                                                            });
                                                        });
                                                    } else {
                                                        console.warn(`Coordenadas inválidas para ${camera.nome}: ${camera.latitude}, ${camera.longitude}`);
                                                    }
                                                }
                                            });
                                        } else {
                                            console.error("Erro: Nenhum dado de câmera encontrado.");
                                        }
                                    })
                                    .catch(error => console.error("Erro ao buscar dados do mapa:", error));
                            }

                            // Inicializa o mapa
                            var map = L.map('map').setView([-3.7309, -40.9928], 15);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; OpenStreetMap contributors'
                            }).addTo(map);
                            carregarCameras();

                            // Fecha o card do mapa quando o usuário clicar fora dele
                            map.on('click', function (e) {
                                if (!e.originalEvent.target.closest(".map-video-card")) {
                                    map.closePopup();
                                }
                            });

                            // Função para atualizar os contadores em todos os elementos com os IDs duplicados
                            function atualizarInterface(cameraId, data) {
                                document.querySelectorAll(`[id="total-${cameraId}"]`).forEach(el => {
                                    el.textContent = data.total ?? 0;
                                });
                                document.querySelectorAll(`[id="online-${cameraId}"]`).forEach(el => {
                                    el.textContent = data.online ?? 0;
                                });
                            }

                            // Atualiza os contadores de visualizações tanto nos cards da lista quanto nos do mapa
                            setInterval(() => {
                                document.querySelectorAll('.video-card, .map-video-card').forEach(card => {
                                    const cameraId = card.getAttribute('data-id') || card.getAttribute('data-camera-id');
                                    if (cameraId) {
                                        fetch(`visualizacoes.php?camera_id=${cameraId}&acao=status`)
                                            .then(response => response.json())
                                            .then(data => {
                                                atualizarInterface(cameraId, data);
                                            })
                                            .catch(error => console.error('❌ Erro ao atualizar status:', error));
                                    }
                                });
                            }, 5000);
                        </script>
                    </div>
                </div>
            </div>
        </div>



        <style>
            .dark-mode .slider-nav button {
                background-color: rgb(0 0 0 / 20%);
                border: 1px solid #000;
            }

            .dark-mode .slider-nav button:hover {
                background-color: rgb(0 0 0 / 50%);
                border: 1px solid #000;
            }

            .dark-mode .video-card {
                background-color: #1a1a1a;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.7);
                border: 2px solid #0000006b;
            }

            .dark-mode .noticias-slide .card-slide {
                background-color: #1a1a1a;
                color: white;
                border: 2px solid #00000038;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.7);
            }

            .dark-mode .slider-container {
                background-color: #222;
                border: 2px solid #0000004d;
            }


            .overlayCima-baixo {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 70%;
                background-color: rgba(0, 0, 0, 0.192);
                pointer-events: none;
                z-index: -3;
                /* SUBSTITUIR O CSS JA EXISTENTE */
            }

            .stripe-river-section {
                margin-top: 0%;
                /* NAO ESQUECER DE APAGAR O MARGIN-TOP DO ELEMENTY STYLE */
            }

            #map {
                position: relative;
                margin-top: 0px;
                margin-left: -1.1%;
                height: 300px;
                width: 99%;
                border-radius: 5px;
                box-shadow: 0px 2px 12px rgba(0, 0, 0, 0.136);
            }

            .leaflet-container a.leaflet-popup-close-button:hover {
                color: #d91818;
            }
        </style>
        </div>
        </div>

        </div>
        </div>



        <?php include 'cameras_tiangua.php'; ?>


        <!-- Seção de Notícias -->
        <section id="newsStripe" class="stripe-river-section" style="margin-top: 100px;">
            <h2>Notícias</h2>
            <div id="news-container" class="layout-news">

                <style>
                    .slider-container-baixo {
                        width: 100%;
                        margin: 20px auto;
                        position: relative;
                        overflow: hidden;
                        border: 2px solid #ccc;
                        background-color: #333;
                        border-radius: 4px;
                    }

                    .slider-baixo {
                        display: flex;
                        transition: transform 0.5s ease-in-out;
                        width: 100%;
                        cursor: pointer;
                    }

                    .slide-baixo {
                        min-width: 100%;
                        height: 350px;
                        position: relative;
                    }

                    .slide-baixo img {
                        width: 100%;
                        height: 120%;
                        object-fit: cover;
                        border-radius: 15px;
                    }

                    .slide-baixo h3,
                    .slide-baixo p {
                        position: absolute;
                        color: white;
                        margin: 0;
                        padding: 0;
                        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
                        z-index: 11;
                    }

                    .slide-baixo h3 {
                        top: 310px;
                        left: 20px;
                    }

                    .slide-baixo p {
                        bottom: -15px;
                        left: 20px;
                    }





                    .slider-nav-baixo {
                        pointer-events: none;
                        display: none
                    }

                    .slider-nav-baixo button.prev-baixo,
                    .slider-nav-baixo button.next-baixo {
                        background-color: rgba(0, 0, 0, 0.5);
                        color: white;
                        border: none;
                        padding: 10px;
                        width: 30px;
                        height: 100px;
                        cursor: pointer;
                        font-size: 18px;
                    }

                    .slider-nav-baixo button.prev-baixo {
                        border-radius: 0px 300px 300px 0px;
                    }

                    .slider-nav-baixo button.next-baixo {
                        border-radius: 300px 0px 0px 300px;
                    }

                    .slider-nav-baixo button:hover {
                        background-color: rgba(0, 0, 0, 0.8);
                    }

                    .indicators-baixo {
                        text-align: center;
                        margin-top: 10px;
                    }

                    .dot-baixo {
                        height: 15px;
                        width: 15px;
                        margin: 0 5px;
                        background-color: #bbb;
                        border-radius: 50%;
                        display: inline-block;
                        cursor: pointer;
                    }

                    .dot-baixo.active {
                        background-color: #717171;
                    }



                    .slide-baixo h3 {
                        top: 290px;
                        left: 20px;
                        font-size: 18px;
                    }

                    .slide-baixo p {
                        bottom: -22px;
                        left: 20px;
                        font-size: 14px;
                    }

                    .slide-baixo h1,
                    .slide-baixo h2,
                    .slide-baixo h3,
                    .slide-baixo h4,
                    .slide-baixo h5,
                    .slide-baixo p {
                        z-index: 11;
                    }

                    .overlayCima-baixo {
                        position: absolute;
                        top: 0px;
                        left: 0;
                        width: 100%;
                        height: 0%;
                        background-color: rgba(0, 0, 0, 0.1);
                        pointer-events: none;
                        z-index: 1;
                    }

                    .noticias-slide {
                        display: flex;
                        flex-direction: column;
                        gap: 0px;
                    }

                    @media (max-width: 400px) {
                        #map {
                            margin-left: -7.1%;
                            width: 104%;
                        }
                    }
                </style>

                <!-- HTML -->
                <!-- Slider HTML (substitua o conteúdo estático pelos slides dinâmicos) -->
                <!-- Slider HTML -->
                <div class="slider-container-baixo">
                    <div class="overlayCima-baixo"></div>
                    <div class="slider-baixo" id="slider-baixo">
                        <!-- Slides serão inseridos dinamicamente -->
                    </div>
                    <div class="slider-nav-baixo">
                        <button class="prev-baixo" id="prev-baixo">&#10094;</button>
                        <button class="next-baixo" id="next-baixo">&#10095;</button>
                    </div>
                    <div class="indicators-baixo" id="indicators-baixo">
                        <!-- Indicadores serão inseridos dinamicamente -->
                    </div>
                </div>

                <script>
                    // Função truncate atualizada para usar apenas media queries (max-width)
                    // Ela ignora o parâmetro maxLength para larguras até 900px e utiliza os valores definidos:
                    // - Até 300px: 20 caracteres
                    // - Até 600px: 55 caracteres
                    // - Até 900px: 100 caracteres
                    // Para larguras maiores que 900px, utiliza o valor passado (ex: 70)
                    function truncate(str, maxLength) {
                        let desiredLength;
                        if (window.matchMedia("(min-width: 300px) and (max-width: 400px)").matches) {
                            desiredLength = 45;
                        } else if (window.matchMedia("(min-width: 401px) and (max-width: 600px)").matches) {
                            desiredLength = 60;
                        } else if (window.matchMedia("(min-width: 601) and (max-width: 800px)").matches) {
                            desiredLength = 70;
                        } else if (window.matchMedia("(min-width: 801px) and (max-width: 1100px)").matches) {
                            desiredLength = 90;
                        } else if (window.matchMedia("(min-width: 1101px) and (max-width: 1366px)").matches) {
                            desiredLength = 100;
                        } else if (window.matchMedia("(min-width: 1367px)").matches) {
                            desiredLength = 300;
                        } else {
                            desiredLength = maxLength;
                        }
                        return str.length > desiredLength ? str.substring(0, desiredLength) + "..." : str;
                    }

                    document.addEventListener("DOMContentLoaded", () => {
                        fetch("scraper.php")
                            .then(response => response.json())
                            .then(data => {
                                if (data.error) {
                                    document.querySelector('.slider-container-baixo').innerHTML =
                                        "<p>Erro: " + data.error + "</p>";
                                    return;
                                }

                                // Embaralha os dados e seleciona as 3 primeiras notícias
                                data.sort(() => Math.random() - 0.5);
                                const selectedNews = data.slice(0, 3);

                                const sliderContainer = document.getElementById("slider-baixo");
                                const indicatorsContainer = document.getElementById("indicators-baixo");
                                sliderContainer.innerHTML = "";
                                indicatorsContainer.innerHTML = "";

                                selectedNews.forEach((news, index) => {
                                    const slide = document.createElement("div");
                                    slide.classList.add("slide-baixo");
                                    // Lembre: o texto a ser truncado está dentro do elemento h3
                                    slide.innerHTML = `
            <img src="${news.imagem}" alt="${news.titulo}">
            <h3>${truncate(news.titulo, 70)}</h3>
            <p>Confira a notícia completa</p>
          `;
                                    slide.addEventListener("click", () => {
                                        window.open(news.link, "_blank");
                                    });
                                    sliderContainer.appendChild(slide);

                                    const dot = document.createElement("span");
                                    dot.classList.add("dot-baixo");
                                    if (index === 0) dot.classList.add("active");
                                    dot.addEventListener("click", () => {
                                        currentSlide_baixo = index;
                                        updateSlider();
                                        resetAutoSlide();
                                    });
                                    indicatorsContainer.appendChild(dot);
                                });

                                initSlider();
                            })
                            .catch(error => console.error("Erro ao carregar notícias:", error));

                        let currentSlide_baixo = 0;
                        let autoSlide_baixo;

                        function initSlider() {
                            document.getElementById("next-baixo").addEventListener("click", () => {
                                changeSlide_baixo(1);
                            });
                            document.getElementById("prev-baixo").addEventListener("click", () => {
                                changeSlide_baixo(-1);
                            });
                            autoSlide_baixo = setInterval(() => changeSlide_baixo(1), 5000);
                        }

                        function updateSlider() {
                            const slides = document.querySelectorAll(".slide-baixo");
                            const dots = document.querySelectorAll(".dot-baixo");
                            slides.forEach(slide => slide.classList.remove("active"));
                            dots.forEach(dot => dot.classList.remove("active"));

                            slides[currentSlide_baixo].classList.add("active");
                            dots[currentSlide_baixo].classList.add("active");

                            const slider = document.querySelector(".slider-baixo");
                            slider.style.transform = `translateX(-${currentSlide_baixo * 100}%)`;
                        }

                        function changeSlide_baixo(direction) {
                            const slides = document.querySelectorAll(".slide-baixo");
                            const total = slides.length;
                            currentSlide_baixo = (currentSlide_baixo + direction + total) % total;
                            updateSlider();
                            resetAutoSlide();
                        }

                        function resetAutoSlide() {
                            clearInterval(autoSlide_baixo);
                            autoSlide_baixo = setInterval(() => changeSlide_baixo(1), 5000);
                        }
                    });

                </script>




                <!-- Notícias Menores -->
                <!-- Notícias Menores -->
                <style>
                    .noticias-slide {
                        display: flex;
                        flex-direction: column;
                        /* Organiza as manchetes em coluna */

                        /* Espaçamento entre as manchetes */
                    }

                    .card-slide {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        padding: 15px;
                        border: 1px solid #ddd;
                        border-radius: 8px;
                        background-color: #fff;
                        cursor: pointer;
                        transition: background 0.3s ease-in-out;
                    }

                    .card-slide:hover {
                        background-color: #f5f5f5;
                    }

                    .card-slide img {
                        width: 120px;
                        height: 80px;
                        object-fit: cover;
                        border-radius: 8px;
                    }

                    .text-content {
                        flex: 1;
                        margin-right: 10px;
                    }

                    .text-content h3 {
                        font-size: 18px;
                        margin: 0;
                    }

                    .text-content p {
                        font-size: 14px;
                        color: #666;
                    }

                    @media (max-width: 768px) {
                        .noticias-slide .card-slide {
                            width: 100%;
                        }

                    }
                </style>

                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        // Função para truncar o texto se maior que o limite
                        function truncate(str, maxLength) {
                            return str.length > maxLength ? str.substring(0, maxLength) + "..." : str;
                        }

                        fetch("scraper.php")
                            .then(response => response.json())
                            .then(data => {
                                const container = document.getElementById("noticias");
                                container.innerHTML = "";
                                if (data.error) {
                                    container.innerHTML = "<p>Erro ao carregar notícias: " + data.error + "</p>";
                                    return;
                                }
                                data.sort(() => Math.random() - 0.5);
                                const selectedNews = data.slice(0, 3);
                                selectedNews.forEach(noticia => {
                                    const card = document.createElement("div");
                                    card.classList.add("card-slide");
                                    card.setAttribute("data-link", noticia.link);
                                    card.innerHTML = `
                    <div class="text-content">
                        <h3>${truncate(noticia.titulo, 20)}</h3>
                        <button onclick="window.open('${noticia.link}', '_blank'); event.stopPropagation();">Ver Notícias</button>
                    </div>
                    <img src="${noticia.imagem}" alt="${noticia.titulo}">
                `;
                                    card.addEventListener("click", function () {
                                        window.open(this.getAttribute("data-link"), "_blank");
                                    });
                                    container.appendChild(card);
                                });
                            })
                            .catch(error => console.error("Erro ao carregar notícias:", error));
                    });
                </script>








                <!-- Container das manchetes -->
                <div class="noticias-slide" id="noticias"></div>


        </section>


        <section id="adBanner" class="ad-banner-section">
            
                <img src="imagens/banner_propaganda.png" alt="Imagem de Anúncio" class="img_ad">
           
        </section>
        <script>
    document.getElementById('adBanner').addEventListener('click', () => {
  let adicionais = [];

  document.querySelectorAll('.adicional-item').forEach(item => {
    const quantity = parseInt(item.querySelector('.quantity').textContent || "0");
    if (quantity > 0) {
      const name = item.getAttribute('data-name');
      adicionais.push(`${name} x${quantity}`);
    }
  });

  let totalValueElement = document.querySelector('#totalValue');
  let totalValue = totalValueElement ? totalValueElement.textContent : "0.00";

  let mensagem = `Olá! 😊\n\n` +
    `*Pedido de Contratação de Plano*\n` +
    `📡 *Plano*: 200 MEGA\n` +
    `💵 *Preço do Plano*: R$69,99\n\n` +
    `*Adicionais Selecionados:*\n` +
    (adicionais.length ? adicionais.map(item => `➕ ${item}`).join("\n") : "Nenhum adicional selecionado.") +
    `\n\n` +
    `🧾 *Total Geral*: R$69,99\n\n` +
    `Obrigado!`;

  let telefoneVendedor = "+5588993253038";
  let url = `https://api.whatsapp.com/send?phone=${telefoneVendedor}&text=${encodeURIComponent(mensagem)}`;

  window.location.href = url;
});

</script>

        <p>Visitantes totais: <span id="total"></span></p>
        <p>Visitantes online: <span id="online"></span></p>

        <section class="credits-section" id="creditos">
            <h2>Créditos da Equipe</h2>

            <div class="team-grid">
                <div class="team-card">
                    <img src="imagens/person.jpg" alt="Nome do Funcionário">
                    <h3>Allan</h3>
                    <p>Cargo do Funcionário</p>
                    <p>Desenvolvedor web e de sistemas, Programando projetos da Teeb.</p>
                </div>
                <div class="team-card">
                    <img src="imagens/person.jpg" alt="Nome do Funcionário">
                    <h3>Gamaliel</h3>
                    <p>Cargo do Funcionário</p>
                    <p>Desenvolvedor web e de sistemas, Programando projetos da Teeb.</p>
                </div>


            </div>
        </section>


        <script src="script.js">console.log(`Card clicado com ID: ${id}`);</script>


    </main>
    <footer>
        <div class="footer-container">
            <div class="footer-column">
                <h3>Sobre Nós</h3>
                <p>TeebWeb oferece câmeras ao vivo e informações em tempo real sobre diversos locais no Brasil.</p>
            </div>
            <div class="footer-column">
                <h3>Navegação</h3>
                <ul>
                    <li><a href="#">Início</a></li>
                    <li><a href="#">Câmeras</a></li>
                    <li><a href="#">Mapa</a></li>
                    <li><a href="#">Contato</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Redes Sociais</h3>
                <ul class="social-links">
                    <li><a href="#">Facebook</a></li>
                    <li><a href="#">Instagram</a></li>
                    <li><a href="#">Twitter</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Contato</h3>
                <p>Email: contato@teebweb.com.br</p>
                <p>Telefone: (11) 1234-5678</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 TeebWeb. Todos os direitos reservados.</p>
            <p><a href="#">Política de Privacidade</a> | <a href="#">Termos de Uso</a></p>
        </div>
    </footer>


</body>

</html>
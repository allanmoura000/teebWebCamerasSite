<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CamerasTeeb</title>
    <link rel="shortcut icon" href="/imagens/icon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <link rel="stylesheet" href="newproject.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="mapa.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />

    <style>
        .slide {
            display: none;
        }
    </style>
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
                        <a href="index.php" class="dropdown-toggle">
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
                        <a href="conta.php" id="auth-link">
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
            <div class="slide" id="slide-1">
                <img src="imagens/pracaAdventista.jpeg" alt="Slide 1">
                <h3>Praça do Adventista</h3>
                <p>Local histórico de encontro.</p>
            </div>
            <div class="slide" id="slide-2">
                <img src="imagens/pracacigano.jpeg" alt="Slide 2">
                <h3>Praça do Cigano</h3>
                <p>Cultura e tradições em harmonia.</p>
            </div>
            <div class="slide" id="slide-3">
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
        </div>
    </div>
    <a href="#nav" class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </a>

    <main>

        <?php include 'cameras.php'; ?>


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
                        z-index: 3;
                    }

                    .slide-baixo h3 {
                        top: 310px;
                        left: 20px;
                    }

                    .slide-baixo p {
                        bottom: -15px;
                        left: 20px;
                    }

                    .overlay-baixo {
                        position: absolute;
                        top: 0px;
                        left: 0;
                        width: 100%;
                        height: 0%;
                        background-color: rgba(0, 0, 0, 1.5);
                        pointer-events: none;
                        z-index: 1;
                    }

                    .overlayCima-baixo {
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 70%;
                        background-color: rgba(0, 0, 0, 0.192);
                        pointer-events: none;
                        z-index: 1;
                    }

                    .slider-nav-baixo {
                        position: absolute;
                        top: 50%;
                        width: 100%;
                        display: flex;
                        justify-content: space-between;
                        transform: translateY(-50%);
                        z-index: 2;
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
                </style>



                <!-- HTML -->
                <div class="slider-container-baixo">
                    <div class="overlayCima-baixo"></div>
                    <div class="slider-baixo">
                        <div class="slide-baixo" id="slide-1-baixo">
                            <img src="imagens/pracaAdventista.jpeg" alt="Slide 1">
                            <h3>Praça Adventista</h3>
                            <p>Local histórico de encontro.</p>
                        </div>
                        <div class="slide-baixo" id="slide-2-baixo">
                            <img src="imagens/pracacigano.jpeg" alt="Slide 2">
                            <h3>Praça Cigano</h3>
                            <p>Cultura e tradições em harmonia.</p>
                        </div>
                        <div class="slide-baixo" id="slide-3-baixo">
                            <img src="imagens/montanha.jpg" alt="Slide 3">
                            <h3>Vista da Montanha</h3>
                            <p>Uma paisagem deslumbrante.</p>
                        </div>
                    </div>

                    <div class="slider-nav-baixo">
                        <button class="prev-baixo">&#10094;</button>
                        <button class="next-baixo">&#10095;</button>
                    </div>

                    <div class="indicators-baixo">
                        <span class="dot-baixo active"></span>
                        <span class="dot-baixo"></span>
                        <span class="dot-baixo"></span>
                    </div>
                </div>

                <!-- JavaScript -->
                <script>
                    let currentSlide_baixo = 0;
                    const slides_baixo = document.querySelectorAll('.slide-baixo');
                    const dots_baixo = document.querySelectorAll('.dot-baixo');
                    const totalSlides_baixo = slides_baixo.length;

                    document.querySelector('.next-baixo').addEventListener('click', function () {
                        changeSlide_baixo(1);
                    });

                    document.querySelector('.prev-baixo').addEventListener('click', function () {
                        changeSlide_baixo(-1);
                    });

                    function changeSlide_baixo(direction_baixo) {
                        slides_baixo[currentSlide_baixo].classList.remove('active');
                        dots_baixo[currentSlide_baixo].classList.remove('active');

                        currentSlide_baixo = (currentSlide_baixo + direction_baixo + totalSlides_baixo) % totalSlides_baixo;

                        slides_baixo[currentSlide_baixo].classList.add('active');
                        dots_baixo[currentSlide_baixo].classList.add('active');

                        const slider_baixo = document.querySelector('.slider-baixo');
                        slider_baixo.style.transform = `translateX(-${currentSlide_baixo * 100}%)`;
                    }

                    let autoSlide_baixo = setInterval(() => changeSlide_baixo(1), 5000);

                    dots_baixo.forEach((dot_baixo, index_baixo) => {
                        dot_baixo.addEventListener('click', () => {
                            currentSlide_baixo = index_baixo;
                            changeSlide_baixo(0);
                            clearInterval(autoSlide_baixo);
                            autoSlide_baixo = setInterval(() => changeSlide_baixo(1), 5000);
                        });
                    });
                </script>

                <!-- Notícias Menores -->
                <div class="noticias-slide" id="noticias">
                    <div class="card-slide">
                        <div class="text-content">
                            <h3>Manchete 1</h3>
                            <p>Descrição breve da manchete 1.</p>
                        </div>
                        <img src="/imagens/" alt="Manchete 1">
                    </div>
                    <div class="card-slide">
                        <div class="text-content">
                            <h3>Manchete 2</h3>
                            <p>Descrição breve da manchete 2.</p>
                        </div>
                        <img src="/imagens/espetinhoRodoviaria.jpeg" alt="Manchete 2">
                    </div>
                    <div class="card-slide">
                        <div class="text-content">
                            <h3>Manchete 3</h3>
                            <p>Descrição breve da manchete 3.</p>
                        </div>
                        <img src="/imagens/" alt="Manchete 3">
                    </div>
                </div>

            </div>
        </section>


        <section id="adBanner" class="ad-banner-section">
            <a href="https://isp.teeb.com.br/" target="_blank" aria-label="Anúncio Especial">
                <img src="imagens/banner_propaganda.png" alt="Imagem de Anúncio" class="img_ad">
            </a>
        </section>



        <section class="credits-section" id="creditos">
            <h2>Equipe de Desenvolvimento</h2>
            <div class="team-grid">
                <div class="team-card">
                    <img src="imagens/alan.jpg" alt="Allan" loading="lazy">
                    <h3>Allan</h3>
                    <p>Desenvolvedor Full Stack</p>
                    <p>Desenvolvimento web e sistemas</p>
                </div>
                <div class="team-card">
                    <img src="imagens/gamaliel.jpg" alt="Gamaliel" loading="lazy">
                    <h3>Gamaliel</h3>
                    <p>Designer UX/UI</p>
                    <p>Interface e experiência do usuário</p>
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
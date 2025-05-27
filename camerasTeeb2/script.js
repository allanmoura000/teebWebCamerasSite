let currentSlide = 0;
let heartbeatInterval;
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');
const totalSlides = slides.length;

// Botões de navegação
document.querySelector('.next').addEventListener('click', function () {
    changeSlide(1);
});

document.querySelector('.prev').addEventListener('click', function () {
    changeSlide(-1);
});

// Função para mudar o slide com base na direção ou no índice
function changeSlide(direction) {
    // Remove classe "active" do slide atual e do ponto correspondente
    slides[currentSlide].classList.remove('active');
    dots[currentSlide].classList.remove('active');

    // Calcula o novo slide
    currentSlide = (currentSlide + direction + totalSlides) % totalSlides;

    // Adiciona a classe "active" ao novo slide e ponto correspondente
    slides[currentSlide].classList.add('active');
    dots[currentSlide].classList.add('active');

    // Faz a transição do slider
    const slider = document.querySelector('.slider');
    slider.style.transform = `translateX(-${currentSlide * 100}%)`;
}

// Alteração automática do slide a cada 5 segundos
let autoSlide = setInterval(() => changeSlide(1), 5000);

// Mudança manual ao clicar nas bolinhas e reinício do slide automático
dots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
        currentSlide = index;
        changeSlide(0);
        clearInterval(autoSlide);
        autoSlide = setInterval(() => changeSlide(1), 5000);
    });
});

async function fetchWithRetry(url, options = {}, retries = 3) {
    try {
        const response = await fetch(url, options);
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    } catch (error) {
        if (retries > 0) {
            await new Promise(resolve => setTimeout(resolve, 1000));
            return fetchWithRetry(url, options, retries - 1);
        }
        throw error;
    }
}
document.querySelectorAll('.slide').forEach(slide => {
    slide.addEventListener('click', function () {
        if (this.classList.contains("no-popup")) return;
        
        const cameraId = this.getAttribute('data-camera-id');
        if (!cameraId) return;

        // Registra visualização com retentativa
        fetchWithRetry(`visualizacoes.php?camera_id=${cameraId}&acao=abrir`)
            .then(data => atualizarInterface(cameraId, data))
            .catch(console.error);

        const popup = document.getElementById(`popup-${cameraId}`);
        if (popup) {
            popup.style.display = "flex";
            document.body.classList.add("no-scroll");
            carregarComentarios(cameraId);

            // Inicia heartbeat
            heartbeatInterval = setInterval(() => {
                fetch(`visualizacoes.php?camera_id=${cameraId}&acao=ping`)
                    .catch(console.error);
            }, 30000);

            const iframe = popup.querySelector("iframe");
            if (iframe && !iframe.src) {
                iframe.src = iframe.getAttribute("data-src");
            }
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const menuToggle = document.querySelector(".menu-toggle");
    const menuIcon = menuToggle.querySelector("i");
    const menu = document.getElementById("nav");
    const overlay = document.querySelector(".overlay");
    const navLinks = document.querySelectorAll("#nav a");

    function fecharMenu() {
        if (window.innerWidth < 770) { // Só fecha o menu em telas pequenas
            menu.classList.add("animate__fadeOutRight");
            menu.classList.remove("animate__fadeInRight");

            setTimeout(() => {
                menu.style.display = "none";
                overlay.style.display = "none";
                menuToggle.classList.remove("active");
                menuIcon.classList.remove("fa-times"); // Remove o "X"
                menuIcon.classList.add("fa-bars"); // Volta para três barras
            }, 90);
        }
    }

    // Fecha o menu ao clicar em qualquer link de navegação
    navLinks.forEach(link => {
        link.addEventListener("click", function () {
            fecharMenu();
        });
    });

    // Fecha o menu ao clicar no overlay (fundo escuro)
    overlay.addEventListener("click", function () {
        fecharMenu();
    });

    // Abre ou fecha o menu ao clicar no botão de menu
    menuToggle.addEventListener("click", function (event) {
        if (!menu.classList.contains("animate__fadeInRight")) {
            menu.style.display = "block";
            menu.classList.add("animate__fadeInRight");
            menu.classList.remove("animate__fadeOutRight");
            overlay.style.display = "block";
            menuToggle.classList.add("active");
            menuIcon.classList.remove("fa-bars"); // Remove as três barras
            menuIcon.classList.add("fa-times"); // Mostra o "X"
        } else {
            fecharMenu();
        }
        event.stopPropagation();
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const menuToggle = document.querySelector(".menu-toggle");
    const menuIcon = menuToggle.querySelector("i");

    menuToggle.addEventListener("click", function () {
        menuIcon.classList.toggle("fa-bars");
        menuIcon.classList.toggle("fa-times");
    });
});

// Abre o popup quando o cartão de vídeo é clicado
document.addEventListener("DOMContentLoaded", function () {
    // Função unificada para abrir o popup com a lógica completa
    function abrirPopup(cameraId) {
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
            return;
        }
    }

    // Função unificada para fechar o popup
    function fecharPopup(cameraId) {
        const popup = document.getElementById(`popup-${cameraId}`);
        if (popup) {
            clearInterval(heartbeatInterval);
            const iframe = popup.querySelector("iframe");
            if (iframe) iframe.src = "";
            popup.style.display = "none";
            document.body.classList.remove("no-scroll");
            
            fetchWithRetry(`visualizacoes.php?camera_id=${cameraId}&acao=fechar`)
                .then(data => atualizarInterface(cameraId, data))
                .catch(console.error);
        }
    }
    

    // Torna as funções acessíveis globalmente para que os onclick inline funcionem
    window.abrirPopup = abrirPopup;
    window.fecharPopup = fecharPopup;

    // Event delegation para os cards que já existem no DOM
    document.querySelectorAll(".video-card, .map-video-card").forEach(card => {
        card.addEventListener("click", function () {
            const cameraId = card.getAttribute("data-id") || card.getAttribute("data-camera-id");
            if (cameraId) {
                abrirPopup(cameraId);
                // Fecha o popup do Leaflet, se estiver aberto
                if (typeof map !== "undefined" && map.closePopup) {
                    map.closePopup();
                }
            }
        });
    });

    // Fecha o popup quando o botão de fechar (".close") é clicado
    document.querySelectorAll(".popup .close").forEach(button => {
        button.addEventListener("click", function (e) {
            const popup = button.closest(".popup");
            if (popup) {
                const cameraId = popup.id.replace('popup-', '');
                if (cameraId) {
                    encerrarVisualizacao(cameraId);
                }
                popup.style.display = "none"; // Esconde o popup
                document.body.classList.remove("no-scroll");
            }
            e.stopPropagation();
        });
    });

    // Fecha o popup se o usuário clicar fora do conteúdo (na área de overlay)
    document.querySelectorAll(".popup").forEach(popup => {
        popup.addEventListener("click", function (e) {
            // Se o clique foi diretamente no overlay (fora do conteúdo interno)
            if (e.target === popup) {
                const cameraId = popup.id.replace('popup-', '');
                if (cameraId) {
                    encerrarVisualizacao(cameraId);
                }
                popup.style.display = "none";
                document.body.classList.remove("no-scroll");
            }
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const backToTopButton = document.querySelector(".back-to-top");

    window.addEventListener("scroll", function () {
        if (window.scrollY > 300) { // Exibe o botão ao descer 300px
            backToTopButton.classList.add("show");
        } else {
            backToTopButton.classList.remove("show");
        }
    });

    backToTopButton.addEventListener("click", function () {
        window.scrollTo({
            top: 0,
            behavior: "smooth" // Rolagem suave ao topo
        });
    });
});

// Função para abrir o popup da câmera (caso seja chamada de outro lugar)
function abrirPopup(id) {
    const popup = document.getElementById(`popup-${id}`);
    if (popup) {
        popup.style.display = "flex";
    } else {
        return;
    }
}

// Função para fechar o popup (caso seja chamada de outro lugar)
function fecharPopup(id) {
    const popup = document.getElementById(`popup-${id}`);
    if (popup) {
        popup.style.display = "none";
    } else {
        return;
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const darkModeToggle = document.getElementById("dark-mode-toggle");
    const darkModeSwitch = document.getElementById("dark-mode-switch");
    const toggleBtn = document.querySelector(".toggle-btn");
    const body = document.body;
    const themeIcon = document.getElementById("theme-icon"); // Ícone que será animado

    // Verifica se o modo escuro está ativado no armazenamento local
    if (localStorage.getItem("darkMode") === "enabled") {
        body.classList.add("dark-mode");
        darkModeSwitch.checked = true;
        toggleBtn.classList.add("active");
        if (themeIcon) {
            themeIcon.classList.remove("fa-moon");
            themeIcon.classList.add("fa-sun");
        }
    }

    // Evento de clique no botão de modo escuro
    darkModeToggle.addEventListener("click", function () {
        // Se for em desktop, aplica a animação de rotação no ícone
        if (window.innerWidth > 768 && themeIcon) {
            themeIcon.classList.add("rotate");
        }
        
        // Alterna o modo escuro/claro
        body.classList.toggle("dark-mode");
        const darkModeEnabled = body.classList.contains("dark-mode");

        // Atualiza o estado do switch e do botão
        darkModeSwitch.checked = darkModeEnabled;
        toggleBtn.classList.toggle("active", darkModeEnabled);

        // Armazena a preferência do usuário
        localStorage.setItem("darkMode", darkModeEnabled ? "enabled" : "disabled");

        // Se for desktop, após a animação, atualiza o ícone
        if (window.innerWidth > 768 && themeIcon) {
            setTimeout(function () {
                themeIcon.classList.remove("rotate");
                if (darkModeEnabled) {
                    themeIcon.classList.remove("fa-moon");
                    themeIcon.classList.add("fa-sun");
                } else {
                    themeIcon.classList.remove("fa-sun");
                    themeIcon.classList.add("fa-moon");
                }
            }, 300);
        }
    });

    // Evento de clique no switch
    darkModeSwitch.addEventListener("change", function () {
        body.classList.toggle("dark-mode", darkModeSwitch.checked);
        toggleBtn.classList.toggle("active", darkModeSwitch.checked);

        // Armazena a preferência do usuário
        localStorage.setItem("darkMode", darkModeSwitch.checked ? "enabled" : "disabled");

        // Atualiza o ícone sem animação (opcional)
        if (themeIcon) {
            if (darkModeSwitch.checked) {
                themeIcon.classList.remove("fa-moon");
                themeIcon.classList.add("fa-sun");
            } else {
                themeIcon.classList.remove("fa-sun");
                themeIcon.classList.add("fa-moon");
            }
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".comment-input-container button").forEach(button => {
        button.addEventListener("click", function () {
            const cameraId = this.id.replace("submit-comment-", "");
            const comentario = document.getElementById(`new-comment-${cameraId}`).value.trim();

            if (!comentario) {
                alert("Digite um comentário!");
                return;
            }

            const userId = localStorage.getItem("userId");

            if (userId) {
                // Buscar o nome do usuário logado
                fetch("getUserName.php?userId=" + userId)
                    .then(response => response.json())
                    .then(data => {
                        const usuario = data.name || "Usuário";
                        enviarComentario(cameraId, usuario, comentario);
                    })
                    .catch(err => {
                        enviarComentario(cameraId, "Usuário", comentario);
                    });
            } else {
                alert("Você precisa estar logado para comentar.");
            }
        });
    });
});

function enviarComentario(cameraId, usuario, comentario) {
    fetch("salvar_comentario.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `camera_id=${cameraId}&usuario=${encodeURIComponent(usuario)}&comentario=${encodeURIComponent(comentario)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById(`new-comment-${cameraId}`).value = "";
            carregarComentarios(cameraId);
        } else {
            alert("Erro ao salvar comentário.");
        }
    });
}

function carregarComentarios(cameraId) {
    fetch(`carregar_comentarios.php?camera_id=${cameraId}`)
        .then(response => response.json())
        .then(comentarios => {
            const listaComentarios = document.getElementById(`comments-list-${cameraId}`);
            listaComentarios.innerHTML = "<h1>Comentários</h1>";

            comentarios.forEach(comentario => {
                const comentarioElemento = document.createElement("div");
                comentarioElemento.classList.add("comment");
                comentarioElemento.innerHTML = `
                    <div class="comment-header">
                        <i class="fas fa-user-circle fa-2x comment-avatar"></i>
                        <div class="comment-info">
                            <span class="comment-username">${comentario.usuario}</span>
                            <span class="comment-timestamp">${new Date(comentario.data).toLocaleString()}</span>
                        </div>
                    </div>
                    <div class="comment-text">
                        <p>${comentario.comentario}</p>
                    </div>
                `;
                listaComentarios.appendChild(comentarioElemento);
            });
        });
}

document.addEventListener("DOMContentLoaded", function () {
    const userId = localStorage.getItem("userId");
    const authLink = document.getElementById("auth-link");

    if (userId) {
        // Se o usuário está logado, mudar o link para "Sair"
        authLink.innerHTML = '<i class="fas fa-sign-out-alt"></i> Sair';
        authLink.href = "#"; // Remover redirecionamento para login
        authLink.addEventListener("click", function (event) {
            event.preventDefault(); // Evita o redirecionamento
            logoutUser(); // Chama a função de logout
        });
    }
});

// Função de logout
function logoutUser() {
    localStorage.removeItem("userId"); // Remove o ID do usuário
    window.location.reload(); // Recarrega a página para aplicar a mudança
}

document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("search-input");
    const resultsContainer = document.getElementById("autocomplete-results");

    searchInput.addEventListener("input", function () {
        const query = searchInput.value.trim();

        if (query.length < 1) {
            resultsContainer.innerHTML = "";
            resultsContainer.style.display = "none";
            return;
        }

        fetch(`busca_cameras.php?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                resultsContainer.innerHTML = "";
                
                if (data.length > 0) {
                    resultsContainer.style.display = "block";

                    data.forEach(nome => {
                        const div = document.createElement("div");
                        div.classList.add("autocomplete-item");
                        div.textContent = nome;
                        div.addEventListener("click", function () {
                            searchInput.value = nome;
                            resultsContainer.innerHTML = "";
                            resultsContainer.style.display = "none";
                            
                            // Simula o clique no botão de busca
                            searchButton.click();
                        });
                        
                        resultsContainer.appendChild(div);
                    });
                } else {
                    resultsContainer.style.display = "none";
                }
            })
            .catch(error => {});
    });

    // Oculta as sugestões se clicar fora do campo de busca
    document.addEventListener("click", function (e) {
        if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
            resultsContainer.style.display = "none";
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("search-input");
    const resultsContainer = document.getElementById("autocomplete-results");
    let currentIndex = -1; // Índice do item selecionado

    searchInput.addEventListener("keydown", function (event) {
        const items = resultsContainer.querySelectorAll(".autocomplete-item");

        if (items.length > 0) {
            if (event.key === "ArrowDown") {
                event.preventDefault();
                currentIndex = (currentIndex + 1) % items.length;
                updateSelection();
            } else if (event.key === "ArrowUp") {
                event.preventDefault();
                currentIndex = (currentIndex - 1 + items.length) % items.length;
                updateSelection();
            } else if (event.key === "Enter") {
                event.preventDefault();
                if (currentIndex >= 0) {
                    // Atualiza o input com o item selecionado
                    searchInput.value = items[currentIndex].textContent;
                    resultsContainer.innerHTML = "";
                }
                // Submete o formulário automaticamente
                searchInput.form.submit();
            } else if (event.key === "Backspace") {
                // Reseta a seleção durante a exclusão
                currentIndex = -1;
                clearSelection();
            }
        } else if (event.key === "Enter") {
            // Caso não haja itens no autocomplete, submete o formulário normalmente
            searchInput.form.submit();
        }
    });

    // Atualiza a seleção visual e o valor do input
    function updateSelection() {
        const items = resultsContainer.querySelectorAll(".autocomplete-item");
        items.forEach(item => item.classList.remove("selected"));
        if (currentIndex >= 0) {
            items[currentIndex].classList.add("selected");
            searchInput.value = items[currentIndex].textContent;
        }
    }

    // Limpa a seleção visual
    function clearSelection() {
        const items = resultsContainer.querySelectorAll(".autocomplete-item");
        items.forEach(item => item.classList.remove("selected"));
    }

    // Limpa os resultados ao perder o foco
    searchInput.addEventListener("blur", function () {
        setTimeout(() => {
            currentIndex = -1;
            resultsContainer.innerHTML = "";
        }, 200);
    });

    // Ao clicar em um item, preenche o campo de busca e submete o formulário
    resultsContainer.addEventListener("click", function (event) {
        if (event.target.classList.contains("autocomplete-item")) {
            searchInput.value = event.target.textContent;
            resultsContainer.innerHTML = "";
            searchInput.form.submit();
        }
    });
});

document.querySelectorAll('.slide').forEach(slide => {
    slide.addEventListener('click', function () {
      const cameraId = this.getAttribute('data-camera-id');
      if (cameraId) {
        const popup = document.getElementById(`popup-${cameraId}`);
        if (popup) {
          popup.style.display = "flex";
          document.body.classList.add("no-scroll");
          // Carrega os comentários para a câmera
          carregarComentarios(cameraId);
          // Verifica se o iframe já foi carregado e, se não, carrega-o
          const iframe = popup.querySelector("iframe");
          if (iframe && !iframe.src) {
            const videoSrc = iframe.getAttribute("data-src");
            iframe.src = videoSrc;
          }
        } else {
          return;
        }
      } else {
        return;
      }
    });
});

// Para slides que possuem câmera, manter a funcionalidade original
document.querySelectorAll('.slide:not(.no-popup)').forEach(slide => {
    slide.addEventListener('click', function () {
      const cameraId = this.getAttribute('data-camera-id');
      if (cameraId) {
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
        }
      }
    });
});

let currentFullscreenContainer = null;

function enterFullScreen(containerId) {
  var container = document.getElementById(containerId);
  currentFullscreenContainer = container;
  
  if (container.requestFullscreen) {
    container.requestFullscreen().then(() => {
      if (screen.orientation && screen.orientation.lock) {
        screen.orientation.lock("landscape").catch(err => {});
      }
      toggleFullscreenButtons(container, true);
    }).catch(err => {});
  } else if (container.webkitRequestFullscreen) { // Safari
    container.webkitRequestFullscreen();
    if (screen.orientation && screen.orientation.lock) {
      screen.orientation.lock("landscape").catch(err => {});
    }
    toggleFullscreenButtons(container, true);
  } else if (container.msRequestFullscreen) { // IE11
    container.msRequestFullscreen();
    if (screen.orientation && screen.orientation.lock) {
      screen.orientation.lock("landscape").catch(err => {});
    }
    toggleFullscreenButtons(container, true);
  } else {
    alert("Fullscreen não é suportado neste dispositivo.");
  }
}

function exitFullScreen() {
  if (document.exitFullscreen) {
    document.exitFullscreen();
  } else if (document.webkitExitFullscreen) { // Safari
    document.webkitExitFullscreen();
  } else if (document.msExitFullscreen) { // IE11
    document.msExitFullscreen();
  }
  
  if (screen.orientation && screen.orientation.unlock) {
    screen.orientation.unlock();
  }
  
  // Altera manualmente os botões para o estado original
  if (currentFullscreenContainer) {
    toggleFullscreenButtons(currentFullscreenContainer, false);
    currentFullscreenContainer = null;
  }
}

function toggleFullscreenButtons(container, isFullscreen) {
  // Procura todos os botões dentro do container
  var buttons = container.querySelectorAll("button");
  buttons.forEach(function(button) {
    if (button.innerHTML.indexOf("fa-compress") !== -1) {
      button.style.display = isFullscreen ? "block" : "none";
    }
    if (button.innerHTML.indexOf("fa-expand") !== -1) {
      button.style.display = isFullscreen ? "none" : "block";
    }
  });
}

document.addEventListener("fullscreenchange", function() {
  if (!document.fullscreenElement) {
    // Aguarda 100ms para garantir que a transição tenha finalizado
    setTimeout(() => {
      document.body.style.transform = "none";
      document.body.style.zoom = "1";
      document.documentElement.style.transform = "none";
      window.scrollTo(0, 0);
    }, 100);
  }
});

// Função para coletar informações do navegador e dispositivo
function getUserDeviceInfo() {
    let userAgent = navigator.userAgent;
    let deviceType = /android/i.test(userAgent) ? 'Android' : 
                     /iPhone|iPad|iPod/i.test(userAgent) ? 'iOS' : 'Desktop';
    
    let browser = /chrome/i.test(userAgent) && !/edge/i.test(userAgent) ? 'Chrome' :
                  /firefox/i.test(userAgent) ? 'Firefox' :
                  /safari/i.test(userAgent) && !/chrome/i.test(userAgent) ? 'Safari' : 'Desconhecido';

    let androidVersion = '';
    let match = userAgent.match(/Android\s([0-9\.]+)/);
    if (match) {
        androidVersion = match[1];
    }

    return {
        userAgent: userAgent,
        deviceType: deviceType,
        browser: browser,
        androidVersion: androidVersion,
        screenWidth: window.screen.width,
        screenHeight: window.screen.height,
        language: navigator.language
    };
}

// Enviar os dados para o servidor quando a página for carregada
window.addEventListener("load", function () {
    let visitorData = getUserDeviceInfo();

    fetch('save_visitor.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(visitorData)
    })
    .then(response => response.text())
    .then(data => {})
    .catch(error => {});
});

function atualizarContadores() {
    fetch('contador.php')
        .then(response => response.text())
        .then(text => {
            return JSON.parse(text);
        })
        .then(data => {
            document.getElementById('total').textContent = data.total;
            document.getElementById('online').textContent = data.online;
        })
        .catch(error => {});
}

// Atualiza os contadores apenas ao carregar a página
atualizarContadores();

// Função para registrar uma nova visualização ao abrir o popup
// Função para registrar a abertura do popup
function registrarVisualizacao(cameraId) {
    fetch(`visualizacoes.php?camera_id=${cameraId}&acao=abrir`)
        .then(response => response.json())
        .then(data => {
            atualizarInterface(cameraId, data);
        })
        .catch(error => {});
}

// Função para registrar o fechamento do popup
// 2) Também ajuste o lugar onde você registra o fechamento do popup,
//    para usar o mesmo padrão de leitura de texto + parse:

function encerrarVisualizacao(cameraId) {
    fetch(`visualizacoes.php?camera_id=${cameraId}&acao=fechar`)
      .then(res => {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.text();
      })
      .then(text => {
        try {
          const data = JSON.parse(text);
          atualizarInterface(cameraId, data);
        } catch (e) {
          console.error('Resposta não é JSON ao fechar:', text);
        }
      })
      .catch(err => console.error('Erro ao encerrar visualização:', err));
  }
  

// Atualiza a interface com os novos valores da API
function atualizarInterface(cameraId, data) {
    // Atualiza elementos dentro do popup (se estiver aberto)
    const totalElement = document.getElementById(`total-${cameraId}`);
    const onlineElement = document.getElementById(`online-${cameraId}`);
    if (totalElement) totalElement.textContent = data.total || 0;
    if (onlineElement) onlineElement.textContent = data.online || 0;

    // Atualiza os cards em todas as seções (lista principal e slider)
    document.querySelectorAll(`[data-id="${cameraId}"] .stats-views, [data-camera-id="${cameraId}"] .stats-views`).forEach(el => {
        el.textContent = `${data.total || 0} visualizações`;
    });

    document.querySelectorAll(
        `[data-id="${cameraId}"] .stats-live, [data-camera-id="${cameraId}"] .stats-live`
      ).forEach(el => {
        el.innerHTML = `<i class="fa fa-circle"></i> ${data.online || 0} ao vivo`;
      });
      
} 
// Evento para abrir o popup
document.querySelectorAll('.video-card').forEach(card => {
    card.addEventListener('click', function () {
        const cameraId = this.getAttribute('data-id');
        if (cameraId) {
            registrarVisualizacao(cameraId);
        }
    });
});

// Evento para fechar o popup corretamente ao clicar no "X"
document.querySelectorAll('.popup .close').forEach(button => {
    button.addEventListener('click', function () {
        const popup = this.closest('.popup');
        if (popup) {
            const cameraId = popup.id.replace('popup-', '');
            if (cameraId) {
                encerrarVisualizacao(cameraId);
            }
            popup.style.display = "none"; // Esconde o popup
            document.body.classList.remove("no-scroll");
        }
    });
});

// Atualiza o contador online periodicamente sem alterar valores no banco
// 1) Função de polling de status (executada periodicamente)
setInterval(() => {
    document.querySelectorAll('.video-card').forEach(card => {
      const cameraId = card.getAttribute('data-id');
      if (!cameraId) return;
  
      fetch(`visualizacoes.php?camera_id=${cameraId}&acao=status`)
        .then(res => {
          if (!res.ok) throw new Error('HTTP ' + res.status);
          return res.text();                    // lê como texto cru
        })
        .then(text => {
          try {
            const data = JSON.parse(text);      // tenta converter para JSON
            atualizarInterface(cameraId, data);
          } catch (e) {
            console.error('Resposta não é JSON:', text);
          }
        })
        .catch(err => console.error('Erro ao atualizar status:', err));
    });
  }, 10000); // a cada 10s
  
function toggleHistoria(id) {
    let historiaText = document.getElementById(`historia-${id}`);
    let botao = document.querySelector(`[onclick='toggleHistoria(${id})']`);

    if (historiaText.classList.contains("expandido")) {
        historiaText.style.maxHeight = "60px"; // Volta para o tamanho original
        botao.textContent = "Ver mais";
    } else {
        historiaText.style.maxHeight = "none"; // Expande o texto
        botao.textContent = "Ver menos";
    }

    historiaText.classList.toggle("expandido");
}

function verificarComentario(comentario) {
    let palavroes = ["palavrao1", "palavrao2", "palavrao3"]; // Adicione os palavrões aqui
    let regex = new RegExp(palavroes.join("|"), "gi");

    if (regex.test(comentario)) {
        alert("Seu comentário contém palavras inadequadas.");
        return false;
    }
    return true;
}

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".comment-input-container button").forEach(button => {
        button.addEventListener("click", function () {
            const cameraId = this.id.replace("submit-comment-", "");
            const comentario = document.getElementById(`new-comment-${cameraId}`).value.trim();

            if (!verificarComentario(comentario)) {
                return;
            }

            // Continua com o envio do comentário
        });
    });
});
document.querySelectorAll('.comment-input-container textarea').forEach(textarea => {
    textarea.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) { // Verifica se Enter foi pressionado sem Shift
            e.preventDefault(); // Evita a quebra de linha
            // Aciona o clique no botão de publicar comentário no mesmo container
            this.parentElement.querySelector('button').click();
        }
    });
});

window.addEventListener('beforeunload', function () {
    // Seleciona todos os elementos com a classe "popup"
    const popups = document.querySelectorAll('.popup');
    
    popups.forEach(popup => {
      // Verifica se o popup está visível (não está escondido)
      if (popup.style.display !== 'none') {
        // Extrai o ID da câmera a partir do id do popup (ex.: "popup-3")
        const cameraId = popup.id.replace('popup-', '');
        const url = `visualizacoes.php?camera_id=${cameraId}&acao=fechar`;
        
        // Usa sendBeacon para enviar a requisição de decremento de forma assíncrona
        if (navigator.sendBeacon) {
          navigator.sendBeacon(url);
        } else {
          // Fallback: requisição síncrona (menos recomendada, mas garante a execução)
          var xhr = new XMLHttpRequest();
          xhr.open("GET", url, false); // false torna a requisição síncrona
          xhr.send(null);
        }
      }
    });
});
  
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".star-rating").forEach(function (ratingContainer) {
        let stars = Array.from(ratingContainer.querySelectorAll(".star"));

        stars.forEach(function (star, index) {
            star.addEventListener("mouseover", function () {
                stars.forEach((s, i) => s.classList.toggle("hover", i <= index));
            });
            star.addEventListener("mouseout", function () {
                stars.forEach(s => s.classList.remove("hover"));
            });

            star.addEventListener("click", function () {
                const userId = localStorage.getItem("userId"); // Pega userId do LocalStorage

                if (!userId) {
                    alert("Você precisa estar logado para avaliar.");
                    return;
                }

                let ratingValue = index + 1;
                stars.forEach((s, i) => s.classList.toggle("selected", i < ratingValue));

                let container = ratingContainer.closest(".rating-container");
                let cameraId = container.getAttribute("data-camera-id");

                fetch("salvar_avaliacao.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `camera_id=${cameraId}&nota=${ratingValue}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        container.querySelector(".rating-average").textContent = data.media;
                        container.querySelector(".rating-count").textContent = data.total;
                        localStorage.setItem(`rating_${cameraId}`, ratingValue); // Salva localmente
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {});
            });
        });
    });

    // Recupera a avaliação do usuário ao carregar a página
    document.querySelectorAll(".rating-container").forEach(container => {
        let cameraId = container.getAttribute("data-camera-id");
        let userRating = localStorage.getItem(`rating_${cameraId}`);

        if (userRating) {
            let stars = container.querySelectorAll(".star");
            stars.forEach((s, i) => s.classList.toggle("selected", i < userRating));
        }
    });
});

document.getElementById('post-teebweb').addEventListener('click', () => {
  // Monta a mensagem de energia solar personalizada
  let mensagem = `Olá! 😊\n\n` +
    `*Pedido de Informações sobre Energia Solar*\n` +
    `🌞 *Energia*: Sistema de Energia Solar da Teeb Web\n` +
    `💬 Estou interessado em saber mais sobre os planos, preços e vantagens de adotar energia solar para o meu empreendimento.\n\n` +
    `Aguardo seu retorno. Obrigado!`;

  let telefoneVendedor = "+5588993253038";
  let url = `https://api.whatsapp.com/send?phone=${telefoneVendedor}&text=${encodeURIComponent(mensagem)}`;

  window.location.href = url;
});



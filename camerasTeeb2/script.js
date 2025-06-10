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

// Função unificada para abrir o popup e carregar comentários
function abrirPopup(cameraId) {
    console.log(`Abrindo popup para câmera ID: ${cameraId}`);
    const popup = document.getElementById(`popup-${cameraId}`);
    if (!popup) {
        console.error(`Popup com ID popup-${cameraId} não encontrado.`);
        return;
    }

    // Verifica se o popup já está aberto para evitar duplicação
    if (popup.style.display === "flex") {
        console.log(`Popup ${cameraId} já está aberto`);
        return;
    }

    // Exibe o popup
    popup.style.display = "flex";
    document.body.classList.add("no-scroll");

    // Carrega os comentários imediatamente
    carregarComentarios(cameraId);

    // Registra visualização apenas uma vez
    console.log(`Registrando visualização para câmera ${cameraId}`);
    fetch(`visualizacoes.php?camera_id=${cameraId}&acao=abrir`, {
        method: 'GET',
        headers: {
            'Cache-Control': 'no-cache',
            'Pragma': 'no-cache'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log(`Resposta do servidor para câmera ${cameraId}:`, data);
        if (data && typeof data === 'object') {
            atualizarInterface(cameraId, data);
            // Inicia heartbeat apenas após registrar a visualização com sucesso
            if (heartbeatInterval) {
                clearInterval(heartbeatInterval);
            }
            heartbeatInterval = setInterval(() => {
                // Usa ping apenas para manter a sessão ativa, sem incrementar contadores
                fetch(`visualizacoes.php?camera_id=${cameraId}&acao=ping`, {
                    method: 'GET',
                    headers: {
                        'Cache-Control': 'no-cache',
                        'Pragma': 'no-cache'
                    }
                }).catch(err => console.error('Erro no ping:', err));
            }, 30000);
        } else {
            console.error('Dados inválidos recebidos:', data);
        }
    })
    .catch(error => {
        console.error('Erro ao registrar visualização:', error);
    });

    // Carrega o iframe se necessário
    const iframe = popup.querySelector("iframe");
    if (iframe && !iframe.src) {
        iframe.src = iframe.getAttribute("data-src");
    }
}

// Função para fechar o popup
function fecharPopup(cameraId) {
    console.log(`Fechando popup para câmera ID: ${cameraId}`);
    const popup = document.getElementById(`popup-${cameraId}`);
    if (!popup) {
        console.error(`Popup com ID popup-${cameraId} não encontrado.`);
        return;
    }

    // Verifica se o popup está realmente aberto
    if (popup.style.display !== "flex") {
        console.log(`Popup ${cameraId} já está fechado`);
        return;
    }

    // Limpa o heartbeat
    if (heartbeatInterval) {
        clearInterval(heartbeatInterval);
        heartbeatInterval = null;
    }

    // Limpa o iframe
    const iframe = popup.querySelector("iframe");
    if (iframe) iframe.src = "";

    // Esconde o popup
    popup.style.display = "none";
    document.body.classList.remove("no-scroll");

    // Registra o fechamento
    console.log(`Registrando fechamento para câmera ${cameraId}`);
    fetch(`visualizacoes.php?camera_id=${cameraId}&acao=fechar`, {
        method: 'GET',
        headers: {
            'Cache-Control': 'no-cache',
            'Pragma': 'no-cache'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log(`Resposta do servidor ao fechar câmera ${cameraId}:`, data);
        if (data && typeof data === 'object') {
            atualizarInterface(cameraId, data);
        } else {
            console.error('Dados inválidos recebidos ao fechar:', data);
        }
    })
    .catch(error => {
        console.error('Erro ao fechar visualização:', error);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Configuração dos cards de vídeo e slides
    document.querySelectorAll('.video-card, .map-video-card, .slide:not(.no-popup)').forEach(card => {
        card.addEventListener('click', function() {
            const cameraId = this.getAttribute('data-id') || this.getAttribute('data-camera-id');
            if (!cameraId) return;

            // Fecha o popup do mapa se existir
            if (typeof map !== "undefined" && map.closePopup) {
                map.closePopup();
            }

            abrirPopup(cameraId);
        });
    });

    // Configuração dos botões de fechar
    document.querySelectorAll('.popup .close').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const popup = this.closest('.popup');
            if (!popup) return;
            const cameraId = popup.id.replace('popup-', '');
            fecharPopup(cameraId);
        });
    });

    // Fecha o popup ao clicar fora do conteúdo
    document.querySelectorAll('.popup').forEach(popup => {
        popup.addEventListener('click', function(e) {
            if (e.target === popup) {
                const cameraId = popup.id.replace('popup-', '');
                fecharPopup(cameraId);
            }
        });
    });

    // Configuração única dos botões de comentário
  // Remova esta seção original:
/*
document.querySelectorAll('.comment-input-container button').forEach(btn => {
    btn.addEventListener('click', e => {
        // ...
    });
});
*/

// Substitua por esta nova abordagem:
document.addEventListener('click', function(e) {
    // Verifica se o clique foi em um botão de submit de comentário
    if (e.target && e.target.matches('.comment-input-container button')) {
        e.preventDefault();
        const btn = e.target;
        const cameraId = btn.id.replace('submit-comment-', '');
        const comentario = document.getElementById(`new-comment-${cameraId}`).value.trim();
        
        if (!comentario) { 
            alert('Digite um comentário!'); 
            return; 
        }

        const userId = localStorage.getItem('userId');
        if (!userId) { 
            alert('Você precisa estar logado para comentar.'); 
            return; 
        }

        // Busca nome e envia o comentário
        fetch(`getUserName.php?userId=${userId}`)
            .then(r => r.json())
            .then(data => enviarComentario(cameraId, data.name || 'Usuário', comentario))
            .catch(() => enviarComentario(cameraId, 'Usuário', comentario));
    }
});

    // Configuração dos textareas para envio com Enter
    document.querySelectorAll('.comment-input-container textarea').forEach(textarea => {
        textarea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                const btn = this.parentElement.querySelector('button');
                if (btn) btn.click();
            }
        });
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

// Função para enviar comentário (encapsula submitComment)
function enviarComentario(cameraId, usuario, comentario) {
    const textarea = document.getElementById(`new-comment-${cameraId}`);
    textarea.value = comentario;

    const btn = document.getElementById(`submit-comment-${cameraId}`);
    btn.disabled = true;

    submitComment(cameraId)
        .catch(err => {
            // Só loga erro se não for um comentário duplicado
            if (err !== 'Comentário duplicado') {
                console.error('Erro:', err);
            }
        })
        .finally(() => {
            btn.disabled = false;
        });
}

// Função que faz o POST no servidor
function submitComment(cameraId) {
    const textarea = document.getElementById(`new-comment-${cameraId}`);
    const text = textarea.value.trim();
    if (!text) { 
        alert('Digite um comentário'); 
        return Promise.reject('Comentário vazio');
    }

    // Verifica palavras inadequadas
    if (!verificarComentario(text)) {
        return Promise.reject('Comentário contém palavras inadequadas');
    }

    const formData = new FormData();
    formData.append('camera_id', cameraId);
    formData.append('comentario', text);

    return fetch('salvar_comentario.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                textarea.value = '';
                carregarComentarios(cameraId);
                return res;
            } else {
                // Tratamento especial para mensagem de comentário duplicado
                if (res.message === 'Você já enviou este comentário recentemente') {
                    // Limpa o campo de comentário já que é um duplicado
                    textarea.value = '';
                    // Mostra mensagem mais amigável
                    alert('Por favor, aguarde um momento antes de enviar o mesmo comentário novamente.');
                    return Promise.reject('Comentário duplicado');
                }
                throw new Error(res.message || 'Erro ao salvar comentário');
            }
        })
        .catch(err => {
            // Não mostra erro no console se for apenas um comentário duplicado
            if (err !== 'Comentário duplicado') {
                console.error('Erro ao salvar comentário:', err);
                alert(err.message || 'Erro ao salvar comentário');
            }
            throw err;
        });
}

// Função para carregar comentários (única definição)
function carregarComentarios(cameraId) {
    fetch(`carregar_comentarios.php?camera_id=${cameraId}`)
        .then(r => r.json())
        .then(comentarios => {
            const lista = document.getElementById(`comments-list-${cameraId}`);
             if (!lista.dataset.lastLoad || lista.dataset.lastLoad < Date.now() - 10000) {
            lista.innerHTML = '<h1>Comentários</h1>';
        }
            lista.innerHTML = '<h1>Comentários</h1>';
            comentarios.forEach(c => {
                const div = document.createElement('div');
                div.className = 'comment';
                div.innerHTML = `
                    <div class="comment-header">
                        <i class="fas fa-user-circle fa-2x comment-avatar"></i>
                        <div class="comment-info">
                            <span class="comment-username">${c.nome}</span>
                            <span class="comment-timestamp">${new Date(c.data).toLocaleString()}</span>
                        </div>
                    </div>
                    <div class="comment-text">
                        <p>${c.comentario}</p>
                    </div>
                `;
                lista.appendChild(div);
                
            });
        })
        
        .catch(err => console.error('Erro ao carregar comentários:', err));
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

// Atualiza a interface com os novos valores da API
function atualizarInterface(cameraId, data) {
    console.log(`Atualizando interface para câmera ${cameraId}:`, data);
    if (!data || typeof data !== 'object') {
        console.error('Dados inválidos recebidos:', data);
        return;
    }

    const total = parseInt(data.total) || 0;
    const online = parseInt(data.online) || 0;

    // Atualiza elementos dentro do popup (se estiver aberto)
    const totalElement = document.getElementById(`total-${cameraId}`);
    const onlineElement = document.getElementById(`online-${cameraId}`);
    if (totalElement) {
        totalElement.textContent = total;
        console.log(`Total atualizado para ${total}`);
    }
    if (onlineElement) {
        onlineElement.textContent = online;
        console.log(`Online atualizado para ${online}`);
    }

    // Atualiza os cards em todas as seções (lista principal e slider)
    document.querySelectorAll(`[data-id="${cameraId}"] .stats-views, [data-camera-id="${cameraId}"] .stats-views`).forEach(el => {
        el.innerHTML = `<i class="fa fa-eye"></i> ${total} visualizações`;
    });

    document.querySelectorAll(`[data-id="${cameraId}"] .stats-live, [data-camera-id="${cameraId}"] .stats-live`).forEach(el => {
        el.innerHTML = `<i class="fa fa-circle"></i> ${online} ao vivo`;
    });
}

// Atualiza o contador online periodicamente sem alterar valores no banco
setInterval(() => {
    document.querySelectorAll('.popup[style*="flex"]').forEach(popup => {
        const cameraId = popup.id.replace('popup-', '');
        if (!cameraId) return;

        // Usa status apenas para verificar visualizações online, sem incrementar
        fetch(`visualizacoes.php?camera_id=${cameraId}&acao=status`, {
            method: 'GET',
            headers: {
                'Cache-Control': 'no-cache',
                'Pragma': 'no-cache'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data && typeof data === 'object') {
                atualizarInterface(cameraId, data);
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

// Funções para controlar a barra de carregamento
function showLoading() {
    const overlay = document.getElementById('loadingOverlay');
    const bar = document.getElementById('loadingBar');
    overlay.style.display = 'block';
    bar.style.width = '30%';
}

function updateLoading(percent) {
    const bar = document.getElementById('loadingBar');
    bar.style.width = percent + '%';
}

function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    overlay.style.display = 'none';
}

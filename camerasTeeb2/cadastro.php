<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/imagens/icon.ico" type="image/x-icon">
    <title>Cadastro</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
            font-family: 'Montserrat', sans-serif;
            background-color: #272727;
        }

        .mt-4 {
            
        }

        .header-logo {
            display: none
        }

        .logo-left {
            display: none;
        }

        .frame {
            width: 80%;
            height: 95%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            display: flex;
            border-radius: 10px 0px 0px 10px;
        }

        .left-panel {
            flex: 1;
            background-color: #fdd835;
            border-radius: 10px 0px 0px 10px;
        }


        .right-panel {
            flex: 1;
            background-color: #121212;
            color: #f4f4f4;
            overflow-y: auto;
            position: relative;
            border-radius: 10px;
        }

        .header-bar {
            display: none;
            width: 100%;
            height: 20px;
            background-color: #fdd835;
            position: absolute;
            top: 20px;
            left: 0;
        }

        .logo {
            margin-top: 10%;
            margin-left: 15%;
            max-width: 70%;
            height: auto;
        }

        .btn-primary {
            background-color: #fdd835;
            border: none;
            color: #121212;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #ffd600;
        }

        .btn-secondary {
            background-color: #fdd835;
            /* Amarelo padrão */
            color: #121212;
            /* Texto escuro para contraste */
            border: none;
            border-radius: 8px;
            /* Bordas arredondadas */
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-secondary:hover {
            background-color: #ffd600;
            /* Amarelo mais claro no hover */
            color: #121212;
        }

        .btn-secondary i {
            font-size: 18px;
            /* Ícone do botão */
        }

        .progress-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .progress-step {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            background-color: #333;
            border: 2px solid #f4f4f4;
            text-align: center;
            color: #f4f4f4;
            font-size: 14px;
            line-height: 20px;
            font-weight: 600;
        }

        .progress-step.active {
            background-color: #fdd835;
            border-color: #fdd835;
            color: #121212;
            font-weight: bold;
        }

        .progress-line {
            flex: 1;
            height: 2px;
            background-color: #333;
        }

        .progress-line.completed {
            background-color: #fdd835;
        }

        .progress-step.completed {
            background-color: #fdd835;
            /* Amarelo */
            border-color: #fdd835;
            color: #121212;
        }

        #camera {
            display: none;
        }

        video,
        canvas {
            width: 100%;
            max-width: 100%;
            border-radius: 8px;
            margin-bottom: 5px;
            margin-top: 5px;
        }

        h3 {
            color: #fdd835;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .effect-quote {
            position: absolute;
            top: 50%;
            left: 29%;
            width: 30%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: #fdd835;
            font-size: 1.5rem;
            font-weight: 600;
            font-style: italic;
            padding: 10px;
            border: 2px solid #121212;
            border-radius: 8px;
            background-color: #121212;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        #photoMessage {
            display: none;
            opacity: 0;
            transform: translate(-50%, -60%) scale(0.8);
            transition: opacity 0.6s ease, transform 0.6s ease;
            color: #fdd835;
            font-weight: bold;
            position: absolute;
            top: 70%;
            left: 50%;
            background-color: #121212;
            padding: 15px 30px;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            text-align: center;
            z-index: 10;
        }

        #photoMessage.show {
            display: block;
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }


        #photoMessageError {
            display: none;
            opacity: 0;
            transform: translate(-50%, -60%) scale(0.8);
            transition: opacity 0.6s ease, transform 0.6s ease;
            color: #ff4d4d;
            font-weight: bold;
            position: absolute;
            top: 70%;
            left: 50%;
            background-color: #121212;
            padding: 15px 30px;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            text-align: center;
            z-index: 10;
        }

        #photoMessageError.show {
            display: block;
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }

        .text-center mt-4 {
            text-align: center;
        }

        .h3-step4 {
            text-align: center;
        }

        .img-ultimatela {
            width: 30%;
        }

        .img_inicio {
            margin-top: -6%;
            margin-left: 6%;
            width: 90%;
        }

        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .popup-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            position: relative;

        }

        .close-btn {
            position: absolute;
            top: 4px;
            right: 10px;
            background: none;
            border: none;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        .popup-title {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }

        .popup-subtitle {
            font-size: 18px;
            margin-bottom: 20px;
            color: #555;
        }

        .popup-image {
            width: 100%;
            max-width: 300px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .popup-text {
            font-size: 16px;
            color: #333;
        }

        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;

        }

        .popup-content {
            background: #272727;
            padding: 20px;
            max-width: 80%;
            max-height: 90%;
            overflow-y: auto;
            /* Permite o scroll vertical */
            border-radius: 10px;
            position: relative;
            box-sizing: border-box;


        }

        .close-btn {
            position: fixed;
            /* Fixa o botão no topo direito */
            top: 4%;
            right: 10%;
            font-size: 42px;
            background: transparent;
            border: none;
            cursor: pointer;
            z-index: 1100;
            color: white;
            /* Garante que o botão esteja acima do conteúdo */
        }

        .popup-title,
        .popup-subtitle {
            font-family: 'Montserrat', sans-serif;
            margin: 10px 0;
            margin-top: -5px;
            color: white;
        }

        .popup-image {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
        }

        .popup-text {
            font-family: 'Montserrat', sans-serif;
            margin: 10px 0;
            margin-top: 0%;
            color: white;
        }

        #uploadProgress {
            width: 100%;
            display: none;
            height: 8px;
            border-radius: 4px;
            background-color: #fdd835;
            margin-top: 20px;
            /* Amarelo padrão */
        }


        @media (max-width: 768px) {
            .popup-text {
                margin: 10px 0;
                margin-top: 0%;
            }

        }


        @media (max-width: 568px) {
            .frame {
                width: 100%;
                height: 100%;
                flex-direction: column;
            }

            .left-panel {
                display: none;
            }

            .right-panel {
                flex: none;
                width: 100%;
                height: 100%;
                padding: 10px;
            }

            .popup-text {
                margin: 10px 0;
                margin-top: -15%;
            }

            .header-logo {
                display: block;
                max-width: 50%;
                margin-top: 0%;
                margin-left: 25%;
            }
            @media (max-width: 768px) {

                margin-top: 0%;
            }
            

            .img_inicio {
                display: hidden;
            }

            .container {
                margin-top: 10%;
            }

            .success-message {
                font-size: 24px;
                font-weight: bold;
                color: #333;
                margin-bottom: 20px;
            }

            .redirect-message {
                font-size: 16px;
                color: #555;
            }

            .redirect-message a {
                color: #007bff;
                text-decoration: none;
            }

            .redirect-message a:hover {
                text-decoration: underline;
            }

            .illustration {
                margin-top: 20px;
            }

            #photoMessage {
                top: 50%;
            }

            #photoMessageError {
                top: 50%;
            }
        }
    </style>

</head>

<body>
<!-- Adicione isso logo após a abertura do <body> -->
<div id="loadingOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999;">
    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); text-align:center;">
        <div style="width:300px; height:8px; background:#333; border-radius:4px; overflow:hidden;">
            <div id="loadingBar" style="height:100%; width:0%; background:#fdd835; transition:width 0.3s;"></div>
        </div>
        <p style="color:#fdd835; margin-top:20px;">Processando, por favor aguarde...</p>
    </div>
</div>
    <div class="frame">
        <div class="left-panel">
            <img src="imagens/TeebLogo.png" alt="Logo" class="logo">
            <img src="imagens/indoor-security-system-abstract-concept-vector-ill (4).png" alt="img_inicio"
                class="img_inicio">

        </div>
        <div class="right-panel">
            <img src="imagens/TeebLogo_amarela.png" alt="Header Logo" class="header-logo">
            <div class="container mt-4">
                <div class="logo-container">
                    <img src="imagens/" alt="Logo" class="logo-left">
                </div>

                

                <div id="step1" class="step">
                    <h3>Informações Pessoais</h3>
                    <form id="form1">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="cpf" class="form-label">CPF</label>
                            <input type="text" class="form-control" id="cpf" placeholder="000.000.000-00" maxlength="14"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <!-- Inside the step1 form -->
<!-- Dentro do seu form, logo após o campo de senha -->
<div class="mb-3">
    <label for="password" class="form-label">Senha</label>
    <div class="input-group">
        <input type="password" class="form-control" id="password" required>
        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
            <i class="fas fa-eye"></i>
        </button>
    </div>
</div>

<!-- Campo de Confirmação de Senha -->
<div class="mb-3">
    <label for="confirm_password" class="form-label">Confirme a Senha</label>
    <div class="input-group">
        <input type="password" class="form-control" id="confirm_password" required>
        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirm_password">
            <i class="fas fa-eye"></i>
        </button>
    </div>
</div>


                        <div class="mb-3">
                            <label for="phone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="phone" placeholder="(00) 00000-0000"
                                maxlength="15" required>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary disabled">Voltar</button>
                            <!-- No botão da etapa 1: manter apenas o onclick -->
<button type="button" class="btn btn-primary" onclick="validateStep1()">Próximo</button>
                        </div>

                    </form>
                </div>

                            
<!-- Dentro do formulário step1 -->
<div class="mt-3 text-center">
  <p style="color: #f4f4f4;">Já tem uma conta? 
    <a href="login.php" style="color: #fdd835;">Entrar</a>
  </p>
</div>
<div id="step2" class="step" style="display: none;">
    <h3>Verificação por E-mail</h3>
    <div class="mb-3">
        <label for="verificationCode" class="form-label">Código de Verificação</label>
        <input type="text" class="form-control" id="verificationCode" maxlength="6" required>
    </div>
    <div class="d-flex justify-content-between">
        <button type="button" class="btn btn-secondary" onclick="previousStep(1)">Voltar</button>
        <button type="button" class="btn btn-primary" onclick="validateCode()">Verificar</button>
    </div>
    <div class="mt-3">
        <p>Não recebeu o código? <a href="#" onclick="resendCode(); return false;">Reenviar</a></p>
    </div>
</div>

            <div id="step3" class="step" style="display: none;">
    <h3 class="h3-step4" style="color:#3bb54a">Concluído</h3>
    <div class="text-center mt-4">
        <img src="imagens/verificar.png" alt="Cadastro Concluído" class="img-ultimatela"
            style="max-width: 200px;">
        <p style="margin-top:30px ; color: #3bb54a;" class="text-success fw-bold">
            Informações enviadas com sucesso!
        </p>
        <p style="color: #f4f4f4; margin-top: 15px;">
            Você será redirecionado para a página inicial em 3 segundos...
        </p>
    </div>
</div>


            </div>
        </div>

    </div>

    <div id="popup" class="popup-overlay" style="display: none;">
        <div class="popup-content">
            <button class="close-btn" onclick="closePopup()">×</button>
            <h2 class="popup-title">Selfie Com Documento</h2>
            <h4 class="popup-subtitle">Use esse sistema para validar seu cadastro. Atente-se as instruções abaixo</h4>
            <img src="imagens/homem_identidade.png" alt="Imagem 2" class="popup-image">
            <br><br><br>
            <p class="popup-text">Tire a foto com o documento próximo ao rosto, conforme o exemplo ilustrado abaixo e
                clique em “Enviar Documentação”</p>
            <br> <br>

            <h4 class="popup-subtitle" style="font-size: 21px;">Caso tenha dúvidas, siga o passo a passo abaixo</h4>
            <br>
            <br>
            <p class="popup-text">
            <p class="popup-text">

                <li> 1° Passo: Aperte o botão "Capturar Foto", a câmera do telefone abrirá logo em seguida.</li><br>
                <li> 2° Passo: Agora posicione o documento ao lado do rosto semelhante ao demonstrado no exemplo acima, e capture a imagem.</li><br>
                <li> 3° Passo: Logo após envie a selfie com o documento apertando em "Enviar Documentação", espere a mensagem, e passe para próxima etapa.</li>


            </p>

        </div>
    </div>
<script>
    let currentStep = 1;
    // Funções para controlar a barra de carregamento
function showLoading() {
    const overlay = document.getElementById('loadingOverlay');
    const bar = document.getElementById('loadingBar');
    overlay.style.display = 'block';
    bar.style.width = '30%';
}
// Adicione esta função no final do seu script
function setupPasswordToggles() {
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
}

// Chame a função no DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
    setupInputMasks();
    setupPasswordToggles(); // Adicione esta linha
    
    // Restante do seu código...
});
function updateLoading(percent) {
    const bar = document.getElementById('loadingBar');
    bar.style.width = percent + '%';
}

function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    overlay.style.display = 'none';
}

// Avança para o próximo passo
function nextStep(step) {
    document.getElementById(`step${currentStep}`).style.display = 'none';
    document.getElementById(`step${step}`).style.display = 'block';
    currentStep = step;
    updateStepIndicators(step);
}

// Volta para o passo anterior
function previousStep(step) {
    document.getElementById(`step${currentStep}`).style.display = 'none';
    document.getElementById(`step${step}`).style.display = 'block';
    currentStep = step; // Atualiza a etapa atual corretamente

    updateStepIndicators(step);
}


// Atualiza os indicadores de etapa
function updateStepIndicators(step) {
    for (let i = 1; i <= 4; i++) {
        const stepIndicator = document.getElementById(`stepIndicator${i}`);
        const line = document.getElementById(`line${i - 1}`);
        if (stepIndicator) {
            stepIndicator.classList.toggle('completed', i < step);
            stepIndicator.classList.toggle('active', i === step);
            if (line) line.classList.toggle('completed', i < step);
        }
    }
}
        function sendVerificationCode(userId, email) {
            return fetch("enviar_codigo.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ userId, email })
            })
            .then(response => response.json());
        }

        function validateCode() {
        const code = document.getElementById("verificationCode").value;
        const userId = localStorage.getItem("userId");

        fetch("verificar_codigo.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ userId, code })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                nextStep(3); // Vai para a etapa de conclusão
                
                // Redireciona para a home após 3 segundos
                setTimeout(() => {
                    window.location.href = "index.php"; // Altere para a URL da sua home
                }, 3000);
            } else {
                alert("Código inválido. Tente novamente.");
            }
        });
    }

function resendCode() {
    const userId = localStorage.getItem("userId");
    const email = localStorage.getItem("userEmail");
    
    sendVerificationCode(userId, email)
        .then(data => {
            if (data.success) {
                alert("Código reenviado com sucesso!");
            } else {
                alert("Erro ao reenviar código.");
            }
        });
}
// Validação e envio dos dados da Etapa 1
function validateStep1() {
 const btn = document.querySelector("#form1 button.btn-primary");
    
    // Bloquear múltiplos cliques
    if (btn.getAttribute('data-loading') === 'true') return;
    
    // Obter valores ANTES de mostrar carregamento
    const name  = document.getElementById("name").value.trim();
    const cpf   = document.getElementById("cpf").value.trim();
    const email = document.getElementById("email").value.trim();
    const phone = document.getElementById("phone").value.trim();
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm_password").value;

    // Validar senhas ANTES de qualquer processamento
    if (password.length < 6) {
        alert("A senha deve ter pelo menos 6 caracteres!");
        return;
    }
    
    if (password !== confirmPassword) {
        alert("As senhas não coincidem!");
        return;
    }

    // Só depois das validações iniciamos o processo
    btn.setAttribute('data-loading', 'true');
    btn.textContent = "Enviando...";
    btn.disabled = true;
    
    showLoading();
    updateLoading(30);

    // Envia os dados (incluindo a senha)
       fetch("salvar_usuario1.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `name=${encodeURIComponent(name)}&cpf=${encodeURIComponent(cpf)}&email=${encodeURIComponent(email)}&phone=${encodeURIComponent(phone)}&password=${encodeURIComponent(password)}`
    })
    .then(response => {
        updateLoading(60);
        return response.json();
    })
    .then(data => {
        if (data.error) {
            hideLoading();
            alert(`Erro ao salvar dados: ${data.error}`);
        } else {
            updateLoading(80);
            localStorage.setItem("userId", data.id);
            localStorage.setItem("userEmail", email);
            
            // Envia o código APENAS UMA VEZ
            sendVerificationCode(data.id, email)
                .then(() => {
                    updateLoading(100);
                    setTimeout(() => {
                        hideLoading();
                        nextStep(2);
                    }, 500);
                })
                .catch(error => {
                    hideLoading();
                    console.error("Erro ao enviar código:", error);
                    alert("Falha ao enviar código de verificação");
                });
        }
    })
    .catch(error => {
        hideLoading();
        console.error("Erro:", error);
        alert("Ocorreu um erro ao enviar os dados. Tente novamente.");
    });
    
}

// Máscaras nos campos de entrada
function setupInputMasks() {
    VMasker(document.getElementById("cpf")).maskPattern("999.999.999-99");
    VMasker(document.getElementById("phone")).maskPattern("(99) 99999-9999");
}

document.addEventListener("DOMContentLoaded", () => {
    setupInputMasks();
    document.querySelector("#form1 button.btn-primary").addEventListener("click", validateStep1);
    document.querySelector("#step2 button.btn-secondary").addEventListener("click", () => previousStep(1)); // Corrigido o botão voltar
});


    </script>
    <script src="https://cdn.jsdelivr.net/npm/ua-parser-js@0.7.36/dist/ua-parser.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-masker/1.2.0/vanilla-masker.min.js"></script>

    <script src="script.js"></script>
</body>

</html>
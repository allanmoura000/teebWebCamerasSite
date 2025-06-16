<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="shortcut icon" href="/imagens/icon.ico" type="image/x-icon">
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

        .header-logo {
            display: none
        }

        .logo-left {
            display: none;
        }

        .frame {
            width: 80%;
            height: 90%;
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
            padding: 20px;
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
            top: 10px;
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
            max-height: 80%;
            overflow-y: auto;
            /* Permite o scroll vertical */
            border-radius: 10px;
            position: relative;
            box-sizing: border-box;


        }

        input[type="text"] {
            border: none !important;
            outline: none !important;
            background-color: white !important;
        }

        .close-btn {
            position: absolute;
            /* Fixa o botão no topo direito */
            top: 10px;
            right: 10px;
            font-size: 50px;
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
            height: 8px;
            border-radius: 4px;
            background-color: #fdd835;
            margin-top: 20px;
            display: none;
        }

        Ï @media (max-width: 768px) {
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
                margin-top: 10%;
                margin-left: 25%;
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

            .logo,
            .header-logo {
                content: url("imagens/TeebLogo.png");
                /* Substitui a logo preta pela nova */
                display: block;
                max-width: 50%;
                margin-top: 10%;
                margin-bottom: 10%;
                margin-left: auto;
                margin-right: auto;

            }
        }





        .error-message {
            color: #ff4d4d;
            margin-top: 10px;
            display: none;
        }

        .register-link {
            margin-top: 10px;
            color: #fdd835;
            display: block;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="frame">
        <div class="left-panel">
            <img src="imagens/TeebLogoPreta.png" alt="Logo" class="logo">
            <img src="imagens/indoor-security-system-abstract-concept-vector-ill (4).png" alt="img_inicio"
                class="img_inicio">

        </div>
        <div class="right-panel">

            <div class="container mt-4">
                <div class="logo-container">
                    <a href="index.php">
                        <img src="imagens/TeebLogoPreta.png" alt="Logo" class="logo">
                    </a>
                </div>

                <h3 class="text-center">Entrar</h3>
                <form id="loginForm">
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="password" required>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="loginUser()">Entrar</button>
                    <p class="error-message" id="errorMessage">E-mail ou senha incorretos. Faça o cadastro.</p>
                </form>
                <a href="conta.php" class="register-link">Ainda não tem uma conta? Cadastre-se</a>
            </div>
        </div>

        <script>
            function loginUser() {
                const email = document.getElementById("email").value.trim();
                const password = document.getElementById("password").value.trim();
                const errorMessage = document.getElementById("errorMessage");

                if (!email || !password) {
                    errorMessage.innerText = "Preencha todos os campos.";
                    errorMessage.style.display = "block";
                    return;
                }

                fetch("loginCode.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            localStorage.setItem("userId", data.id);
                            window.location.href = "index.php"; // Redireciona para a página principal
                        } else {
                            errorMessage.innerText = data.error;
                            errorMessage.style.display = "block";
                        }
                    })
                    .catch(error => {
                        errorMessage.innerText = "Erro ao conectar ao servidor.";
                        errorMessage.style.display = "block";
                    });
            }
            document.getElementById("loginForm").addEventListener("keydown", function (e) {
                if (e.key === "Enter") {
                    e.preventDefault(); // Previne a submissão padrão do formulário
                    loginUser(); // Chama a função de login
                }
            });

        </script>

</body>

</html>
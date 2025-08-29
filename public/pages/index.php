<?php 
    include_once(__DIR__ . '/../../database/config.php');
    include_once(__DIR__ . '/../../app/controllers/AuthController.php');
    
    $auth = new AuthController($conexao);

        // Se for registro
    if(isset($_POST['submit']) && $_POST['submit'] === "Cadastrar") {
        $nome = $_POST['nome'];
        $usuario = $_POST['usuario'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        $sexo = $_POST['Sexo'];
        $tipo_usuario = $_POST['tipo_usuario'];

        $resultado = $auth->register($nome, $usuario, $email, $senha, $sexo, $tipo_usuario);

        if($resultado === true) {
            echo "Usuário registrado com sucesso!";
        } else {
            echo $resultado; // mensagem de erro
        }
    }   

    // Se for login
    if(isset($_POST['submit']) && $_POST['submit'] === "Entrar") {
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        $resultado = $auth->login($email, $senha);

        if($resultado === true) {
            header("Location: inicio.php");
            exit;
        } else {
            echo $resultado;
        }
    }
?>

    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta author="Guilherme Bisof">
        <meta description="Rede Social">
        <meta keywords="html, css ,javascript, redesocial, rede social, responsivo, fatec, cps, centro paula souza">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
        <link rel="shortcut icon" href="../src/icons/favicon.ico" type="image/x-icon">
        <title>Página de Login |ConnectU</title>

        <!-- CSS Customizado -->

        <link rel="stylesheet" href="../src/css/style.css">

    </head>
    <body>
        <div class="container" id="container">
            
            <!-- Formulário de Registro -->
            <div class="form-container sign-up">
                <form action="index.php" method="POST">
                    <h1>Criar Conta</h1>
                    <div class="social-icons">
                        <a href="#" class="icon">
                            <i class="fa-brands fa-github"></i>
                        </a>
                        <a href="#" class="icon">
                            <i class="fa-brands fa-linkedin-in"></i>
                        </a>
                    </div>
                    <span>ou use seu email para registrar</span>
                    
                    <div class="input-group">
                        
                        <input type="text" name="nome" placeholder="Nome Completo" required>
                    </div>
                    
                    <div class="input-group">
                        
                        <input type="email" name="email" placeholder="Email" required>
                    </div>
                    
                    <div class="input-group">
                        <label for="senha">Senha</label>
                        <input type="password" name="senha" placeholder="Senha (mínimo 8 Caracteres)" required>
                    </div>
                    
                    <div class="input-group">
                        
                        <select name="Sexo" id="form-select">
                            <option value="Masculino">Masculino</option>
                            <option value="Feminino">Feminino</option>
                            <option value="Outro">Outro</option>
                         </select>
                    </div>
                    
                    <div class="input-group">
                        <select name="tipo_usuario">
                            <option value="Aluno">Aluno</option>
                            <option value="Ex-Aluno">Ex-Aluno</option>
                            <option value="Professor">Professor</option>
                        </select>
                    </div>
                    
                    <button type="submit" name="submit" value="Cadastrar">Cadastrar</button>
                </form>
            </div>
            
            <!-- Formulário de Login -->
            <div class="form-container sign-in">
                <form action="login.php" method="POST">
                    <h1>Entrar</h1>
                    <div class="social-icons">
                        <a href="#" class="icon">
                            <i class="fa-brands fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="icon">
                            <i class="fa-brands fa-github"></i>
                        </a>
                    </div>
                    <span>Ou use seu email e senha</span>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="senha" placeholder="Senha" required>
                    <a href="#">Esqueceu sua Senha?</a>
                    <button type="submit" name="submit" value="Entrar">Entrar</button>
                </form>
            </div>
            
            <!-- Outras seções -->
            <div class="toggle-container">
                <div class="toggle">
                    <div class="toggle-panel toggle-left">
                        <h1>Bem-vindo!</h1>
                        <p>Entre com suas informações para utilizar o Site</p>
                        <button class="hidden" id="login">Entrar</button>
                    </div>
                    <div class="toggle-panel toggle-right">
                        <h1>Olá, Amigo!</h1>
                        <p>Registre suas informações para utilizar o Site</p>
                        <button class="hidden" id="register">Registrar</button>
                    </div>
                </div>
            </div>
            <div class="fundo">
                <img src="" alt="">
            </div>
        </div>
        
        <script src="../src/js/script.js"></script>
    </body>
    </html>
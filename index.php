    <?php 
        if(isset($_POST['submit'])){
        
        include_once ('config.php');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $nome = mysqli_real_escape_string($conexao, $_POST['nome'] ?? '');
            $email = mysqli_real_escape_string($conexao, $_POST['email'] ?? '');
            $senha = mysqli_real_escape_string($conexao, $_POST['senha'] ?? '');
        }
        
        if($nome && $email && $senha){
            //Criptografar senha
            $senhaHash = password_hash($senha, PASSWORD_BCRYPT);
        
            // Consulta SQL
            $sql ="INSERT INTO usuarios(nome,email,senha) VALUES ('$nome', '$email', '$senha')";
    
            // Executa e consulta e verica erros
        
            if(mysqli_query($conexao,$sql)){
            echo "Usuário registrado com sucesso!";
        } else{
            echo "Erro ao registrar usuário: ". mysqli_error($conexao);
        }
        } else{
            echo "Por favor, preencha todos os campos";
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
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
        <title>Página de Login |ConnectU</title>

        <!-- CSS Customizado -->

        <link rel="stylesheet" href="src/css/style.css">

    </head>
    <body>
        <div class="container" id="container">
            
            <!-- Formulário de Registro -->
            <div class="form-container sign-up">
                <form action="index.php" method="POST">
                    <h1>Criar Conta</h1>
                    <div class="social-icons">
                        <a href="#" class="icon">
                            <i class="fa-brands fa-google"></i>
                        </a>
                        <a href="#" class="icon">
                            <i class="fa-brands fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="icon">
                            <i class="fa-brands fa-github"></i>
                        </a>
                        <a href="#" class="icon">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                    </div>
                    <span>ou use seu email para registrar</span>
                    <label for="name">Nome</label>
                    <input type="text" name="nome" placeholder="Nome" required>

                    <label for="email">Email</label>
                    <input type="email" name="email" placeholder="Email" required>
                    
                    <label for="senha">Senha</label>
                    <input type="password" name="senha" placeholder="Senha" required>
                    
                    <button type="submit" name="submit">Registrar</button>
                </form>
            </div>
            
            <!-- Formulário de Login -->
            <div class="form-container sign-in">
                <form action="login.php" method="POST">
                    <h1>Entrar</h1>
                    <div class="social-icons">
                        <a href="#" class="icon">
                            <i class="fa-brands fa-google"></i>
                        </a>
                        <a href="#" class="icon">
                            <i class="fa-brands fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="icon">
                            <i class="fa-brands fa-github"></i>
                        </a>
                        <a href="#" class="icon">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                    </div>
                    <span>Ou use seu email e senha</span>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="senha" placeholder="Senha" required>
                    <a href="#">Esqueceu sua Senha?</a>
                    <button type="submit" name="submit">Entrar</button>
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
        
        <script src="src/js/script.js"></script>
    </body>
    </html>
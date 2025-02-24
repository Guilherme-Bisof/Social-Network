<?php 
        if(isset($_POST['submit'])){
        
        include_once ('../database/config.php');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $nome = mysqli_real_escape_string($conexao, $_POST['nome'] ?? '');
            $usuario = mysqli_real_escape_string($conexao, $_POST ['usuario'] ?? '');
            $email = mysqli_real_escape_string($conexao, $_POST['email'] ?? '');
            $senha = mysqli_real_escape_string($conexao, $_POST['senha'] ?? '');
            $sexo = mysqli_real_escape_string($conexao, $_POST['Sexo'] ?? '');
            $tipo_usuario = mysqli_real_escape_string($conexao, $_POST ['tipo_usuario'] ?? 'Aluno');
        }
        
        if($nome && $usuario && $email && $senha && $sexo && $tipo_usuario){
           
            $checkEmail = "SELECT * FROM usuarios WHERE email = '$email'";
            $result = mysqli_query($conexao, $checkEmail);
            if(mysqli_num_rows($result)> 0 ){
            echo "O email já esta registrado.";
            }else{
            
            
            //Criptografar senha
            $senhaHash = password_hash($senha, PASSWORD_BCRYPT);
        
            // Consulta SQL
            $sql ="INSERT INTO usuarios(nome, email, senha, usuario, sexo, tipo_usuario) VALUES ('$nome', '$email', '$senhaHash', '$usuario', '$sexo', '$tipo_usuario')";
    
            // Executa e consulta e verica erros
        
            if(mysqli_query($conexao,$sql)){
            echo "";
            } else{
                echo "Erro ao registrar usuário: ". mysqli_error($conexao);
             }
        }
        } else{
            echo "Por favor, preencha todos os campos obrigatórios";
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
        <link rel="shortcut icon" href="src/icons/favicon.ico" type="image/x-icon">
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
                    
                    <div class="input-group">
                        
                        <input type="text" name="nome" placeholder="Nome Completo" required>
                    </div>

                    <div class="input-group">
                        
                        <input type="text" name="usuario" placeholder="Nome de Usuário">
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
                    
                    <button type="submit" name="submit">Cadastrar</button>
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
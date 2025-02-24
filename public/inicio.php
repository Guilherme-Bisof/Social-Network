<?php 
    header('Content-Type: text/html; charset=UTF-8');
    session_start();

    if((!isset($_SESSION['email']) == true) and (!isset($_SESSION['senha']) == true))
    {
        unset($_SESSION['email']);
        unset($_SESSION['senha']);
        header('Location: index.php');
    }
    $logado = $_SESSION['email'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta author="Guilherme Bisof">
    <meta description="Rede Social">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="src/css/inicio.css">
    <title>Página de Inicio | ConnectU</title>

</head>
<body>
    <header>
    
    <nav>
        <div class="nav-left">
            <div class="logo">ConnectU</div>
        </div>
        <div class="nav-center">
            <!-- Busca -->
            <form action="buscar.php" class="busca-perfis" method="GET">
                <input type="text" name="termo" placeholder="Buscar usuários...">
                <button type="submit"></button>
             </form>
            
        </div>    
        <div class="nav-right">
                <div class="menu">
                <a href="perfil.php" class="btn-perfil">Perfil</a>
                <a href="#projetos" class="btn-projetos">Projetos</a>
                <a href="#publicacoes" class="btn-publicacoes">Publicações</a>
                <a href="sair.php" class="btn-sair">Sair</a>
            </div>     
        </div> 
    </nav>
    </header>
</body>
</html>
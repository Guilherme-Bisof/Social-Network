<?php 
    session_start();
    // print_r($_SESSION);
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
    <title>Página de Inicio|ConnectU</title>

    <!-- CSS Customizado -->

    <link rel="stylesheet" href="inicio.css">

</head>
<body>
    <header>
    <nav>
        <div class="logo">ConnectU</div>

        <div class="menu">
            <a href="#">Menu</a>
            <a href="#projetos">Projetos</a>
            <a href="#publicacoes">Publicações</a>
        </div>
        <div class="d-flex">
            <a href="" class="btn btn-danger me-5">Sair</a>
        </div>
    </nav>
    </header>
</body>
</html>
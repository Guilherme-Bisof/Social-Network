<?php
    session_start();
    if(isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['senha']) )
    {
        // Acessa
        include_once('../../database/config.php');
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        $stmt = $conexao->prepare("SELECT * FROM usuarios WHERE email = ? AND senha = ?");
        $stmt->bind_param("ss", $email, $senha);
        $stmt->execute();
        $result = $stmt->get_result();

        

        // $sql = "SELECT * FROM usuarios WHERE email = '$email' and senha = '$senha'";

        //$result = $conexao->query($sql);

       

        if(mysqli_num_rows($result) < 1)
        {
            unset($_SESSION['email']);
            unset($_SESSION['senha']);
            header('Location: index.php');
        }
        else{
            $_SESSION['email'] = $email;
            $_SESSION['senha'] = $senha;
            header('Location: inicio.php'); 
        }
    }
    else
    {
        // Não Acessa
        header('Location: login.php');
    }
?>
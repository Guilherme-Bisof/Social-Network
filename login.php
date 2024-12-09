<?php
    session_start();
    if(isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['senha']) )
    {
        // Acessa
        include_once('config.php');
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        // print_r('Email:' . $email);
        // print_r('<br>');
        // print_r('Senha:' . $senha);

        $sql = "SELECT * FROM usuarios WHERE email = '$email' and senha = '$senha'";

        $result = $conexao->query($sql);

        // print_r($sql);
        // print_r($result);

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
// session_start();
// include_once('config.php');

// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
//     $email = mysqli_real_escape_string($conexao, $_POST['email'] ?? '');
//     $senha = $_POST['senha'] ?? '';

//     // Verifica se o email existe no banco
//     $query = "SELECT * FROM usuarios WHERE email = '$email'";
//     $result = mysqli_query($conexao, $query);

//     if (mysqli_num_rows($result) > 0) {
//         $user = mysqli_fetch_assoc($result);

//         // Verifica se a senha corresponde
//         if (password_verify($senha, $user['senha'])) {
//             // Salva dados na sessão
//             $_SESSION['user_id'] = $user['id'];
//             $_SESSION['user_nome'] = $user['nome'];

//             // Redireciona para a página desejada
//             header("Location: inicio.php");
//             exit;
//         } else {
//             echo "Senha incorreta.";
//         }
//     } else {
//         echo "Usuário não encontrado.";
//     }
// } else {
//     echo "Requisição inválida.";
// }
?>

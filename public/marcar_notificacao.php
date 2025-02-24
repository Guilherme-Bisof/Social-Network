<?php 
    include_once('../database/config.php');
    session_start();

    if(!isset($_SESSION['email'])){
        exit("Usuário não autenticado.");
    }

    $user_id = $_SESSION['id'];

    if(isset($_POST['id'])){
        $notificacao_id = intval($_POST['id']);
        $sql = "UPDATE notificacoes SET status = 'lida' WHERE id = $notificacao_id AND usuario_id = $user_id";
        $conexao->query($sql);
    }
?>
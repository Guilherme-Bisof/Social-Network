<?php 
    session_start();
    include '../../database/config.php';


    // Verifica a conexão
    if ($conexao->connect_error){
        die("Erro na conexão: " . $conexão->connect_error);
    }

    // Verificar se o formulário foi enviado via POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Pegar ID do Usuário logado
        $user_id = $_SESSION['user_id'];

        // Pegar valores do formulário

        $usuario = trim($_POST['usuario']);
        $biografia = trim($_POST['biografia']);

        // Evitar SQL Injection
        $usuario = $conexao->real_escape_string($usuario);
        $biografia = $conexao->real_escape_string($biografia);

        // Atualizar no banco de dados
        $sql = "UPDATE usuarios SET usuario = '$usuario', biografia = '$biografia' WHERE id = $user_id";

        if ($conexao->query($sql) === TRUE) {
            $_SESSION['mensagem'] = "Perfil atualizado com sucesso";
        } else {
            $_SESSION['mensagem'] = "Erro ao atualizar" . $conexao->error;
        }

        // Redirecionar de volta para o perfil.php
        header("Location: ../pages/perfil.php");
        exit();
    }
    
    $conexao->close();
?>
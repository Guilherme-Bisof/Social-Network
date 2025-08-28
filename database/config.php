<?php
    $dbHost = 'Localhost';
    $dbUsername = 'root';
    $dbPassword = 'suporte123@';
    $dbName= 'connectu';

    $conexao = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

    // Verifica se houve erro na conexão

    if($conexao->connect_error){
        die("Falha na conexão: " . $conexao->connect_error);
    }

    // //if($conexao->connect_errno)
    // {
    //     echo "Erro";
    // }
    // else
    // {
    //     echo"Conexão efetuada com sucesso";
    // }
?>
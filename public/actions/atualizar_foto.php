<?php 
    session_start();
    
    include_once('../database/config.php');

    // Verifica se o úsuario está logado
    if(!isset($_SESSION['email'])){
        header('Location: index.php');
        exit();
    }

    // Recupera o email do usuário logado
    $email = $_SESSION['email'];

    // Verifica se o user_id está na sessão
    if(isset($_SESSION['user_id'])){
        $user_id = $_SESSION['user_id'];
    } else{
        // Caso o user_id não esteja na sessão, obtenha do banco de dados.
        $sqlUser = "SELECT id FROM usuarios WHERE email = '$email'";
        $resultUser = $conexao->query($sqlUser);
        if ($resultUser && $resultUser-> num_rows > 0 ) {
            $row = $resultUser->fetch_assoc();
            $user_id = $row['id'];
            $_SESSION['user_id'] = $user_id;
        }else{
            echo "Usuário não encontrado";
            exit();
        }
    }
    

    // Verifica se o arquivo foi enviado
    if (isset($_FILES['foto-perfil']) && $_FILES['foto-perfil'] ['error'] == 0){
        $foto = $_FILES['foto-perfil'];

        // Define o diretório para salvar as fotos
        $diretorioUpload = '../uploads/';
        if (!is_dir($diretorioUpload)){
            mkdir($diretorioUpload, 0777, true);
        }

        // Nomeia o arquivo com base no ID do usuário para evitar duplicação

        $nomeArquivo = $user_id . "_" . basename($foto['name']);
        $caminhoFoto = $diretorioUpload . $nomeArquivo;

        // Move o arquivo para o diretório de Upload
        if (move_uploaded_file($foto['tmp_name'], $caminhoFoto)){
            // Atualiza o caminho da foto no banco de dados
            $sqlAtualizarFoto = "UPDATE usuarios SET foto_perfil = '$caminhoFoto' WHERE id = $user_id";
            if ($conexao->query($sqlAtualizarFoto)){
                echo "Foto de perfil atualizada com sucesso!";
                header('Location: ../pages/perfil.php');
                exit();
        } else {
            echo "Erro ao atualizar a foto no banco de dados.";
        }
    } else {
        echo "Erro ao mover o arquivo para o diretório de upload.";
    }
}else {
    echo "Nenhum arquivo enviado ou erro no envio.";
}
    $conexao->close();
?>
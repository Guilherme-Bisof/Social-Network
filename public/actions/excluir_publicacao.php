<?php 
    session_start();
    include('../../database/config.php');

    // Verificar se o usuário está logado
    if (!isset($_SESSION['email'])) {
        header('Location: index.php');
        exit;
    }

    // Verificar o ID da publicação foi enviado
    if (!isset($_POST['publicacao_id'])) {
        header('Location: inicio.php');
        exit;
    }

    $publicacao_id = $_POST['publicacao_id'];
    $imagem = $_POST['imagem'];

    // Obter o ID do usuário logado
    $logado = $_SESSION['email'];
    $stmt = $conexao->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param('s', $logado);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    $user_id = $usuario['id'];

    //Verifica se a publicação pertence ao usuário
    $stmt = $conexao->prepare("SELECT usuario_id, imagem FROM publicacoes WHERE id = ?");
    $stmt->bind_param('i', $publicacao_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $publicacao = $result->fetch_assoc();

    if (!$publicacao || $publicacao['usuario_id'] != $user_id) {
        $_SESSION['error'] = "Você não tem permissão para excluir esta publicação.";
        header('Location: inicio.php');
        exit;
    }

    // Excluir a imagem do servidor (se existir)
    if (!empty($publicacao['imagem'])) {
        $caminho_imagem = $publicacao['imagem'];
        if (file_exists($caminho_imagem)) {
            unlink($caminho_imagem); //Remove o arquivo
        }
    }

    // Excluir a publicação do banco de dados
    $stmt = $conexao->prepare("DELETE FROM publicacoes WHERE id = ?");
    $stmt->bind_param('i', $publicacao_id);
    $stmt->execute();

    // Redirecionar de volta
    header('Location: ../pages/inicio.php');
    exit;
?>
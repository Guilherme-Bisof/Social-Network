<?php 
    session_start();
    include('../../database/config.php');

    if (!isset($_SESSION['user_id'])){
        die("Não autenticado");
    }

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])){
        die("ID Inválido");
    }

    $perfil_id = $_GET['id'] ?? null;
    $user_id = $_SESSION['user_id']; // Você precisa armazenar o ID na sessão

    // Enviar auto-solicitação

    if ($user_id == $perfil_id){
        die("Ação Inválida");
    }


    if ($perfil_id && $perfil_id != $_SESSION['user_id']){
        $sql = "INSERT INTO amizades (usuario_1, usuario_2, status, data) VALUES (?, ?, 'pendente', NOW())";
        
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ii", $_SESSION['user_id'], $perfil_id);

    if($stmt->execute()) {
        // Criar Notificação
        header('Location: ' . $_SERVER['HTTP_REFERER']); //Volta pra Página Anterior
    } else{
        echo "Erro: " . $conexao->error;
    }
}
    exit();
?>
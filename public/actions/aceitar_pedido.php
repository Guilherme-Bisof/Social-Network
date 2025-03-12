<?php 
    session_start();
    include('../database/config.php');

    if (!isset($_SESSION['user_id'])) {
        die('Acesso não autorizado');
    }

    $pedido_id = $_GET['id'] ?? null;

    if($pedido_id) {
        // Atualiza status para 'Aceito'
        $sql = "UPDATE amizades SET status = 'aceito' WHERE id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $pedido_id);

        if ($stmt->execute()) {
            header("Location: " . $_SERVER['HTTP_REFERER']); // VOLTA PARA A PÁGINA ANTERIOR.
            exit();
        } else {
            echo "Erro:" . $conexao->error;
        }
    }
?>
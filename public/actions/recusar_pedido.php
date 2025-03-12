<?php 
    session_start();
    include('../../database/config.php');

    if(!isset($_SESSION['user_id'])){
        die("Acesso não autorizado");
    }

    $pedido_id = $_GET['id'] ?? null;

    if ($pedido_id) {
        // Primeiro obtém os IDs dos usuários envolvidos
        $sql = "SELECT usuario_1 FROM amizades WHERE id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $pedido_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $perfil_id = $row['usuario_1'];

            // Deleta o pedido
            $sql_delete = "DELETE FROM amizades WHERE id = ?";
            $stmt_delete = $conexao->prepare($sql_delete);
            $stmt_delete->bind_param("i", $pedido_id);
        

        if ($stmt_delete->execute()){
            header("Location: ../pages/perfil.php?id=" .  $perfil_id . "&refused=1"); 
            exit ();
        } else {
            echo "Erro ao excluir o pedido: " . $conexao->error;
        }
    } else {
        echo "Pedudi não encontrado.";
    }
} else {
    echo "Parâmetro inválido";
}
?>
<?php 
    session_start();
    include('../../database/config.php');
    
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Não autorizado']);
        exit();
    }
    
    $grupo_id = $_GET['grupo_id'] ?? null;
    $usuario_id = $_SESSION['user_id'];
    
    // Verificar se já existe solicitação
    $sql_check = "SELECT * FROM grupos_membros 
                 WHERE grupo_id = ? AND usuario_id = ?";
    $stmt_check = $conexao->prepare($sql_check);
    $stmt_check->bind_param('ii', $grupo_id, $usuario_id);
    $stmt_check->execute();
    
    if ($stmt_check->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Solicitação já enviada']);
        exit();
    }
    
    // Inserir nova solicitação
    $sql = "INSERT INTO grupos_membros (grupo_id, usuario_id, status) 
            VALUES (?, ?, 'pendente')";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('ii', $grupo_id, $usuario_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro no banco de dados']);
    }
?>
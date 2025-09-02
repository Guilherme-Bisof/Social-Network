<?php
session_start();
include('../../database/config.php');

if (!isset($_SESSION['email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Não logado']);
    exit;
}

if (!isset($_POST['publicacao_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID da publicação não fornecido']);
    exit;
}

$publicacao_id = intval($_POST['publicacao_id']);
$email = $_SESSION['email'];

// Obter o ID do usuário
$sql = "SELECT id FROM usuarios WHERE email = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$usuario_id = $user['id'];

// Verificar se já curtiu
$sql_check = "SELECT id FROM curtidas WHERE usuario_id = ? AND publicacao_id = ?";
$stmt_check = $conexao->prepare($sql_check);
$stmt_check->bind_param('ii', $usuario_id, $publicacao_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    // Já curtiu, então remove a curtida
    $sql_remove = "DELETE FROM curtidas WHERE usuario_id = ? AND publicacao_id = ?";
    $stmt_remove = $conexao->prepare($sql_remove);
    $stmt_remove->bind_param('ii', $usuario_id, $publicacao_id);
    if ($stmt_remove->execute()) {
        $acao = 'descurtido';
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao descurtir']);
        exit;
    }
} else {
    // Não curtiu, então adiciona a curtida
    $sql_insert = "INSERT INTO curtidas (usuario_id, publicacao_id) VALUES (?, ?)";
    $stmt_insert = $conexao->prepare($sql_insert);
    $stmt_insert->bind_param('ii', $usuario_id, $publicacao_id);
    if ($stmt_insert->execute()) {
        $acao = 'curtido';
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao curtir']);
        exit;
    }
}

// Contar curtidas atuais
$sql_count = "SELECT COUNT(*) as total FROM curtidas WHERE publicacao_id = ?";
$stmt_count = $conexao->prepare($sql_count);
$stmt_count->bind_param('i', $publicacao_id);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$count = $result_count->fetch_assoc()['total'];

echo json_encode(['status' => 'success', 'acao' => $acao, 'total_curtidas' => $count]);
?>
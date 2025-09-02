<?php
session_start();
include('../../database/config.php');

if (!isset($_SESSION['email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Não logado']);
    exit;
}

if (!isset($_POST['publicacao_id']) || !isset($_POST['comentario']) || empty(trim($_POST['comentario']))) {
    echo json_encode(['status' => 'error', 'message' => 'Dados incompletos']);
    exit;
}

$publicacao_id = intval($_POST['publicacao_id']);
$comentario = trim($_POST['comentario']);
$email = $_SESSION['email'];

// Obter o ID do usuário
$sql = "SELECT id, nome, foto_perfil FROM usuarios WHERE email = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$usuario_id = $user['id'];
$nome_usuario = $user['nome'];
$foto_perfil = $user['foto_perfil'];

// Inserir comentário
$sql_insert = "INSERT INTO comentarios (usuario_id, publicacao_id, comentario) VALUES (?, ?, ?)";
$stmt_insert = $conexao->prepare($sql_insert);
$stmt_insert->bind_param('iis', $usuario_id, $publicacao_id, $comentario);
if ($stmt_insert->execute()) {
    // Retornar o HTML do comentário
    $html = '
    <div class="comentario">
        <div class="comentario-header">
            <img src="' . htmlspecialchars($foto_perfil) . '" class="avatar pequeno" alt="' . htmlspecialchars($nome_usuario) . '">
            <strong>' . htmlspecialchars($nome_usuario) . '</strong>
            <small>' . date('d/m/Y H:i') . '</small>
        </div>
        <div class="comentario-conteudo">
            <p>' . htmlspecialchars($comentario) . '</p>
        </div>
    </div>';
    echo json_encode(['status' => 'success', 'html' => $html]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao comentar']);
}
?>
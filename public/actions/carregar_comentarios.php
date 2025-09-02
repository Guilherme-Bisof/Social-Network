<?php
session_start();
include('../../database/config.php');

if (!isset($_GET['publicacao_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID da publicação não fornecido']);
    exit;
}

$publicacao_id = intval($_GET['publicacao_id']);

// Buscar comentários
$sql = "SELECT c.*, u.nome, u.foto_perfil 
        FROM comentarios c 
        JOIN usuarios u ON c.usuario_id = u.id 
        WHERE c.publicacao_id = ? 
        ORDER BY c.data_criacao DESC";
$stmt = $conexao->prepare($sql);
$stmt->bind_param('i', $publicacao_id);
$stmt->execute();
$result = $stmt->get_result();

$html = '';
if ($result->num_rows > 0) {
    while ($comentario = $result->fetch_assoc()) {
        $html .= '
        <div class="comentario">
            <div class="comentario-header">
                <img src="' . htmlspecialchars($comentario['foto_perfil']) . '" class="avatar pequeno" alt="' . htmlspecialchars($comentario['nome']) . '">
                <strong>' . htmlspecialchars($comentario['nome']) . '</strong>
                <small>' . date('d/m/Y H:i', strtotime($comentario['data_criacao'])) . '</small>
            </div>
            <div class="comentario-conteudo">
                <p>' . htmlspecialchars($comentario['comentario']) . '</p>
            </div>
        </div>';
    }
} else {
    $html = '<p class="sem-comentarios">Seja o primeiro a comentar!</p>';
}

echo json_encode(['status' => 'success', 'html' => $html]);
?>
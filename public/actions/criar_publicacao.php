<?php 
    session_start();
    include("../../database/config.php");

    if (!isset($_SESSION['email'])) {
        header('Location: ../pages/index.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obter dados do formulário
        $conteudo = trim($_POST['conteudo']);
        $usuario_email = $_SESSION['email'];

        // Obeter ID do usuário
        $sql_usuario = "SELECT id FROM usuarios WHERE email = ?";
        $stmt = $conexao->prepare($sql_usuario);
        $stmt->bind_param('s', $usuario_email);
        $stmt->execute();
        $result = $stmt->get_result();
        $usuario = $result->fetch_assoc();
        $usuario_id = $usuario['id'];

        // Processar imagem
        $imagem_path = null;
        if (!empty($_FILES['imagem']['name'])) {
            $upload_dir = '../uploads/';
            $file_name = uniqid() . '_' . basename($_FILES['imagem']['name']);
            $target_path = $upload_dir . $file_name;

            // Validar e mover arquivo
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['imagem']['type'];

            if (in_array($file_type, $allowed_types) && move_uploaded_file($_FILES['imagem'] ['tmp_name'], $target_path)) {
                
                $imagem_path = $target_path;
            }
        }

        // Inserir no banco de dados
        $sql = "INSERT INTO publicacoes (usuario_id, conteudo, imagem, data_criacao) VALUES (?, ?, ?, NOW())";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param('iss', $usuario_id, $conteudo, $imagem_path);

        if ($stmt->execute()) {
            header('Location: ../pages/inicio.php');
        } else {
            echo "Erro ao publicar: " . $conexao->error;
        }

        exit();
    }
?>
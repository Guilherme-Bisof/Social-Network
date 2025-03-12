<?php 
    session_start();
    include_once('../../database/config.php');

    // Verifica se o usuário está logado
    if (!isset($_SESSION['email'])) {
        header('Location: ../../public/pages/index.php');
        exit();
    }

    // Obter ID do Usuário
    $email = $_SESSION['email'];
    $sql_user = "SELECT id FROM usuarios WHERE email = ?";
    $stmt = $conexao->prepare($sql_user);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $criador_id = $user['id'];

    $erro = '';
    $sucesso = '';

    // Processar o formulário
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = trim($_POST['nome']);
        $descricao = trim($_POST['descricao']);

        // Validar Dados
        if (empty($nome)) {
            $erro = 'O nome do grupo é obrigatório!';
        } else {
            // Processar upload da imagem
            $icone = '';
            if (!empty($_FILES['icone']['name'])) {
                $diretorio = '../../public/uploads/grupos/';
                if (!is_dir($diretorio)) {
                    mkdir($diretorio, 0777, true);
                }
                
                $extensao = pathinfo($_FILES['icone']['name'], PATHINFO_EXTENSION);
                $nome_arquivo = uniqid('grupo_') . '.' . $extensao;
                $caminho_completo = $diretorio . $nome_arquivo;

                if (move_uploaded_file($_FILES['icone']['tmp_name'], $caminho_completo)) {
                    $icone = '/Social-Network/public/uploads/grupos/' . $nome_arquivo;
                } else {
                    $erro = 'Erro ao fazer upload da imagem!';
                }
            }

            // Inserir no banco de dados
            if (empty($erro)) {
                try {
                    $conexao->begin_transaction();

                    //Inserir grupo

                    $sql_grupo = "INSERT INTO grupos (nome,descricao,criador_id,icone) VALUES (?, ?, ?, ?)";
                    $stmt = $conexao->prepare($sql_grupo);
                    $stmt->bind_param('ssis', $nome, $descricao, $criador_id, $icone);
                    $stmt->execute();
                    $grupo_id = $conexao->insert_id ;

                    // Adicionar criador como membro

                    $sql_membro = "INSERT INTO grupos_membros (grupo_id, usuario_id) VALUES (?, ?)";
                    $stmt = $conexao->prepare($sql_membro);
                    $stmt->bind_param('ii', $grupo_id, $criador_id);
                    $stmt->execute();

                    $conexao->commit();
                    $sucesso = 'Grupo criado com sucesso!';
                    header('Location: ../../public/pages/inicio.php');
                    exit();
                } catch (Exception $e) {
                    $conexao->rollback();
                    $erro = 'Erro ao criar o grupo: ' . $e->getMessage();
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Novo Grupo | ConnectYUU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/src/css/inicio.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow-lg" style="max-width: 600px; margin: 0 auto;">
            <div class="card-header bg-primary text-white">
                <h2 class="text-center">Criar Novo Grupo</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($erro)): ?>
                    <div class="alert alert-danger"><?= $erro ?></div>
                <?php endif; ?>

                <?php if (!empty($sucesso)): ?>
                    <div class="alert alert-sucess"><?= $sucesso ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Grupo</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>

                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição:</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="icone" class="form-label">Ícone do Grupo(opcional)</label>
                        <input type="file" class="form-control" id="icone" name="icone" accept="image/*">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Criar Grupo</button>
                        <a href="../../public/pages/inicio.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
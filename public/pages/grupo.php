<?php 
    session_start();
    include_once('../../database/config.php');

    // Verifica se o parâmetro Id do grupo foi passado
    if (!isset($_GET['id'])) {
        header('Location: inicio.php');
        exit();
    }

    $grupo_id = $_GET['id'];

    // Buscar informações do grupo
    $sql_grupo = "SELECT g.*, u.nome AS criador_nome FROM grupos g JOIN usuarios u ON g.criador_id = u.id WHERE g.id = ?";
    $stmt_grupo = $conexao->prepare($sql_grupo);
    $stmt_grupo->bind_param('i', $grupo_id);
    $stmt_grupo->execute();
    $result_grupo = $stmt_grupo->get_result();

    if ($result_grupo->num_rows === 0) {
        // Grupo não encontrado
        header('Location: inicio.php');
        exit();
    }

    $grupo = $result_grupo->fetch_assoc();

    // Buscar membros do grupo
    $sql_membros = "SELECT u.id, u.nome, u.foto_perfil FROM grupos_membros gm JOIN usuarios u ON gm.usuario_id = u.id WHERE gm.grupo_id = ?";
    $stmt_membros = $conexao->prepare($sql_membros);
    $stmt_membros->bind_param('i', $grupo_id);
    $stmt_membros->execute();
    $membros = $stmt_membros->get_result()->fetch_all(MYSQLI_ASSOC);

    // Verificar se o usuário logado é membro
    $usuario_logado_id = $_SESSION['user_id'] ?? null;
    $eh_membro = false;

    if ($usuario_logado_id) {
        $sql_verificar_membro = "SELECT 1 FROM grupos_membros WHERE grupo_id = ? AND usuario_id = ?";
        $stmt_verificar = $conexao->prepare($sql_verificar_membro);
        $stmt_verificar->bind_param('ii', $grupo_id, $usuario_logado_id);
        $stmt_verificar->execute();
        $eh_membro = $stmt_verificar->get_result()->num_rows > 0;
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($grupo['nome']) ?> - ConnectYUU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/src/css/inicio.css">
</head>
<body>
    <header>
        <section class="Nav">
            <h1>ConnectYUU</h1>
            <nav>
                <a href="inicio.php" class="nav-link">Início</a>
                <a href="perfil.php" class="nav-link">Perfil</a>
                <a href="../actions/sair.php" class="nav-link">Sair</a>
           </nav>
        </section>
    </header>

    <main>
        <div class="container-mt5">
            <div class="grupo-header text-center">
                <?php if (!empty($grupo['icone'])): ?>
                    <img src="<?= htmlspecialchars($grupo['icone']) ?>" alt="Ícone do Grupo" class="grupo-icone">
                <?php endif; ?>
                <h1><?= htmlspecialchars($grupo['nome']) ?></h1>
                <p><?= htmlspecialchars($grupo['descricao']) ?></p>
                <small>Criado por: <?= htmlspecialchars($grupo['criador_nome']) ?></small>
            </div>

            <!-- Ações Grupo -->
            <div class="grupo-actions mt-4">
                <?php if ($eh_membro): ?>
                    <button class="btn btn-danger">Sair do Grupo</button>
                <?php else: ?>
                    <button class="btn btn-primary">Entrar no Grupo</button>
                <?php endif; ?>
             </div>

            <!-- Lista de Membros -->
            <section class="members-group mt-5">
                <h2>Membros (<?= count($membros) ?>)</h2>
                <div class="row">
                    <?php foreach ($membros as $membro): ?>
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <img src="<?= htmlspecialchars($membro['foto_perfil']) ?>" class="card-img-top" alt="Foto de Perfil">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($membro['nome']) ?></h5>
                                    <a href="perfil.php?id=<?= $membro['id'] ?>" class="btn btn-sm btn-primary">Ver Perfil</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Publicações do Grupo -->
             <section class="publicacoes-group mt-5">
                <h2>Publicações</h2>
                <div id="lista-publicacoes">
                    <p>Nenhuma publicação no grupo ainda.</p>
                </div>
             </section>
        </div>
    </main>
</body>
</html>
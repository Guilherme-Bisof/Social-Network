<?php 
    session_start();
    include('../../database/config.php');

    // Verifica se o usuário está logado
    if (!isset($_SESSION['email'])){
        header('Location: index.php');
        exit();
    }

    // Captura o termo de busca
    $termo = $_GET['termo'] ?? '';

    // Consulta no banco de dados (usado prepared statements)

    $sql_usuarios = "SELECT id, nome, usuario, foto_perfil FROM usuarios WHERE nome LIKE ? OR usuario LIKE ?";
    $stmt_usuarios = $conexao->prepare($sql_usuarios);
    $termoBusca = "%$termo%";
    $stmt_usuarios->bind_param("ss", $termoBusca, $termoBusca);
    $stmt_usuarios->execute();
    $result_usuarios = $stmt_usuarios->get_result();

    // Busca Grupos
    $sql_grupos = "SELECT id, nome , icone, descricao FROM grupos WHERE nome LIKE ? OR descricao LIKE ?";
    $stmt_grupos = $conexao->prepare($sql_grupos);
    $stmt_grupos->bind_param("ss", $termoBusca, $termoBusca);
    $stmt_grupos->execute();
    $result_grupos = $stmt_grupos->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="src/icons/favicon.ico" type="image/x-icon">
        <title>Busca | ConnectYUU</title>

        <!-- FAVICON -->
        <link rel="shortcut icon" href="../src/icons/favicon.ico" type="image/x-icon">

        <!-- CSS -->
        <link rel="stylesheet" href="../src/css/perfil.css">
        <link rel="stylesheet" href="../src/css/search.css">

        <!--JavaSript-->
        <script src="../src/js/perfil.js" defer></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body>
<header>
            <section class="Nav">
            <h1>ConnectYUU</h1>

            <!-- Busca -->
             <form action="../../actions/buscar.php" class="busca-perfis" method="GET">
                <input type="text" name="termo" placeholder="Buscar" value="<?= htmlspecialchars($termo) ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
             </form>
            <nav>
                <a href="../pages/inicio.php" class="nav-link ">Home</a>
                <a href="#" class="nav-link">Projetos</a>
                <a href="../actions/sair.php" class="nav-link">Sair</a>
            </nav>
            </section>
        </header>
    <main class="resultados-busca">
            <h2>Resultados para "<?= htmlspecialchars($termo) ?>"</h2>

            <div class="carregando"></div>

            <?php if ($result_usuarios->num_rows > 0): ?>
                <section class="categoria-resultados">
                    <h3>Usuários</h3>
                    <div class="lista-perfis">
                        <?php while ($usuario = $result_usuarios->fetch_assoc()): ?>
                            <div class="perfil">
                                <img src="<?= htmlspecialchars($usuario['foto_perfil']) ?>" alt="Foto de <?= htmlspecialchars($usuario['nome']) ?>">
                                <div>
                                    <h3><?= htmlspecialchars($usuario['nome'])?></h3>
                                    <p>@<?= htmlspecialchars($usuario ['usuario']) ?></p>
                                    <a href="../pages/perfil.php?id=<?= $usuario['id'] ?>" class="btn-ver-perfil">Ver Perfil</a>
                                </div>
                            </div>   
                        <?php endwhile; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Grupos -->
            <?php if ($result_grupos->num_rows > 0):?>
                <section class="categoria-resultados">
                    <h3>Grupos</h3>
                    <div class="lista-resultados">
                        <?php while ($grupo = $result_grupos->fetch_assoc()): ?>
                            <div class="item-resultado">
                                <?php if (!empty($grupo['icone'])): ?>
                                    <img src="<?= htmlspecialchars($grupo['icone']) ?> " alt="Ícone do grupo <?= htmlspecialchars(($grupo['nome'])) ?>" class="icone-grupo">
                                <?php else: ?>
                                    <div class="icone-grupo-padrao">
                                        <i class="fas fa-users"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="info-grupo">
                                    <h4><?= htmlspecialchars($grupo['nome']) ?></h4>
                                    <p><?= htmlspecialchars($grupo['descricao']) ?></p>
                                    <a href="../pages/grupo.php?id=<?= $grupo['id'] ?>" class="btn-ver-grupo">Ver Grupo</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Mensagem sem resultados -->
            <?php if ($result_usuarios->num_rows === 0 && $result_grupos->num_rows === 0): ?>
                <div class="sem-resultados">
                    <i class="fas fa-search-minus"></i>
                    <p>Nenhum resultado encontrado para "<?= htmlspecialchars($termo) ?>"</p>
                </div>
                <?php endif;?>
    </main>
</body>
</html>
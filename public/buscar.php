<?php 
    session_start();
    include('../database/config.php');

    // Verifica se o usuário está logado
    if (!isset($_SESSION['email'])){
        header('Location: index.php');
        exit();
    }

    // Captura o termo de busca
    $termo = $_GET['termo'] ?? '';

    // Consulta no banco de dados (usado prepared statements)

    $sql = "SELECT id, nome, usuario, foto_perfil FROM usuarios WHERE nome LIKE ? OR usuario LIKE ?";
    $stmt = $conexao->prepare($sql);

    $termoBusca = "%$termo%";
    $stmt->bind_param("ss", $termoBusca, $termoBusca);
    $stmt->execute();
    $result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="src/icons/favicon.ico" type="image/x-icon">
        <title>Perfil de Usuário</title>

        <!-- CSS -->
        <link rel="stylesheet" href="src/css/perfil.css">

        <!--JavaSript-->
        <script src="src/js/perfil.js" defer></script>

</head>
<body>
<header>
            <section class="Nav">
            <h1>ConnectU</h1>
            <!-- Busca -->
             <form action="buscar.php" class="busca-perfis" method="GET">
                <input type="text" name="termo" placeholder="Buscar">
                <button type="submit">🔍</button>
             </form>
            <nav>
                <a href="inicio.php">Home</a>
                <a href="#">Projetos</a>
                <a href="index.php">Sair</a>
            </nav>
            </section>
        </header>
        <main class="resultados-busca">
            <h2>Resultados para "<?= htmlspecialchars($termo) ?>"</h2>

            <div class="carregando"></div>

            <?php if ($result->num_rows > 0): ?>
                <div class="lista-perfis">
                    <?php while ($usuario = $result->fetch_assoc()): ?>
                        <div class="perfil">
                            <img src="<?= htmlspecialchars($usuario['foto_perfil']) ?>" alt="Foto de <?= htmlspecialchars($usuario['nome']) ?>">
                            <div>
                            <h3><?= htmlspecialchars($usuario['nome'])?></h3>
                            <p>@<?= htmlspecialchars($usuario ['usuario']) ?></p>
                            <a href="perfil.php?id=<?= $usuario['id'] ?>" class="btn-ver-perfil">Ver Perfil</a>
                            </div>
                        </div>   
                    <?php endwhile; ?>
                </div>
                <<?php else: ?>
                    <p class="sem-resultados">Nenhum usuário encontrado.</p>
                <?php endif; ?>
        </main>
</body>
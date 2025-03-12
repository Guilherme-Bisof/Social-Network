<?php 
    header('Content-Type: text/html; charset=UTF-8');
    session_start();
    include('../../database/config.php');

    if((!isset($_SESSION['email']) == true) and (!isset($_SESSION['senha']) == true))
    {
        unset($_SESSION['email']);
        unset($_SESSION['senha']);
        header('Location: index.php');
    }
    $logado = $_SESSION['email'];

    // Obter dados do usuário logado
    $sql_usuario = "SELECT nome, foto_perfil FROM usuarios WHERE email = ?";
    $stmt = $conexao->prepare($sql_usuario);
    $stmt->bind_param('s', $logado);
    $stmt->execute();
    $result_usuario = $stmt->get_result();
    $usuario_logado = $result_usuario->fetch_assoc();


    $nome_usuario = $usuario_logado['nome'];
    $foto_perfil = $usuario_logado['foto_perfil'];

    require_once('../../database/config.php');

    $sql = "SELECT  p.*, u.nome, u.foto_perfil FROM publicacoes p JOIN usuarios u ON p.usuario_id = u.id ORDER BY p.data_criacao DESC";
    $result = $conexao->query($sql);

    // Verifica se Tem publicacoes
    if ($result->num_rows > 0){
        $publicacoes = [];
        while($row = $result->fetch_assoc()) {
            $publicacoes[] = $row;
        }
    } else {
        $publicacoes = [];
    }

    // Obter ID do usuário logado
    $sql_user_id = "SELECT id FROM usuarios WHERE email =?";
    $stmt_user = $conexao->prepare($sql_user_id);
    $stmt_user->bind_param('s', $logado);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $user_data = $result_user->fetch_assoc();
    $user_id = $user_data['id'];

    // Consultar Atividades
    $sql_atividades =  "SELECT a.*, u.nome, u.foto_perfil FROM atividades a JOIN usuarios u ON a.usuario_id = u.id WHERE a.usuario_id IN (SELECT CASE WHEN usuario_1 = ? THEN usuario_2 ELSE usuario_1 END AS amigos FROM amizades WHERE (usuario_1 =? OR usuario_2 =?) AND status = 'aceito') ORDER BY a.data DESC LIMIT 10";
    $stmt_atividades = $conexao->prepare($sql_atividades);
    $stmt_atividades->bind_param('iii', $user_id, $user_id, $user_id);
    $stmt_atividades->execute();
    $result_atividades = $stmt_atividades->get_result();
    $atividades = $result_atividades->fetch_all(MYSQLI_ASSOC);

    // Consultar Amigos

    $sql_amigos = "SELECT u.id, u.nome, u.foto_perfil FROM usuarios u JOIN amizades a ON (u.id = a.usuario_1 OR u.id = a.usuario_2) WHERE (a.usuario_1 = ? OR a.usuario_2 = ?) AND a.status = 'aceito' AND u.id != ? ORDER BY u.ultimo_login DESC LIMIT 5";
    $stmt_amigos = $conexao->prepare($sql_amigos);
    $stmt_amigos->bind_param('iii', $user_id, $user_id, $user_id);
    $stmt_amigos->execute();
    $result_amigos = $stmt_amigos->get_result();
    $amigos = $result_amigos->fetch_all(MYSQLI_ASSOC);

    // Consultar Grupos do Usuário
    $sql_grupos = "SELECT g.* FROM grupos g JOIN grupos_membros gm ON g.id = gm.grupo_id WHERE gm.usuario_id = ? ORDER BY g.data_criacao DESC";
    $stmt_grupos = $conexao->prepare($sql_grupos);
    $stmt_grupos->bind_param('i', $user_id);
    $stmt_grupos->execute();
    $result_grupos = $stmt_grupos->get_result();
    $grupos = $result_grupos->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta author="Guilherme Bisof">
    <meta description="Rede Social">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Fonte -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">

    
    <!-- Favicon -->
    <link rel="shortcut icon" href="../src/icons/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- CSS -->
    <link rel="stylesheet" href="../src/css/inicio.css">

    <!-- JAVASCRIPT -->
     <script src="../src/js/inicio.js"></script>

    <title>Página de Inicio | ConnectU</title>

</head>
<body>
    <header>
        <section class="Nav">
            <h1>ConnectU</h1>

            <!-- Busca-->
             <form action="../actions/buscar.php" class="buscar-perfis" method="GET">
                <input type="text" name="termo" placeholder="Buscar...">
                <button type="submit"></button>
             </form>

             <nav>
                <a href="perfil.php" class="nav-link">Perfil</a>
                <a href="#projetos" class="nav-link">Projetos</a>
                <a href="#publicacoes" class="nav-link">Publicacoes</a>
                <a href="../actions/sair.php" class="nav-link">Sair</a>
             </nav>
        </section>
    
    </header>

    <main>
         <!-- Grupos -->
         <aside class="grupos-section">
            <h2>Grupos</h2>
            <div class="grupos-lista">
                <!-- Lista de Grupos -->
                <?php if (!empty($grupos)): ?>
                    <?php foreach ($grupos as $grupo): ?>
                        <div class="grupo-item">
                            <img src="<?= htmlspecialchars($grupo['icone']) ?>" alt="Icone do Grupo">
                            <span><?= htmlspecialchars($grupo['nome']) ?></span>
                            <div class="grupo-acoes">
                                <a href="grupo.php?id=<?= $grupo['id']?>" class="btn-ver-grupo"> Ver Grupo</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Você não faz parte de nenhum grupo ainda</p>
                <?php endif; ?>
            </div>
            <a href="../../public/actions/criar_grupo.php" class="btn-criar-grupo">Criar Grupo</a>
         </aside> 
        
         <!-- Formulário de Nova Publicação -->
         <section class="nova-publicacao">
            <form action="../actions/criar_publicacao.php" method="POST" enctype="multipart/form-data">
                <div class="publicacao-editor">
                    <div class="usuario-info">
                        <?php  if(!empty($foto_perfil)): ?>
                            <img src="<?=  htmlspecialchars($foto_perfil) ?>" class="avatar" alt="Seu perfil">
                        <?php else: ?>
                            <div class="avatar-inicial"><?= strtoupper(substr($nome_usuario, 0, 1)) ?></div>
                        <?php endif; ?>
                        <textarea name="conteudo" placeholder="No que você está pensando <?= htmlspecialchars($nome_usuario) ?>?" required></textarea>
                    </div>
                    <div class="acoes">
                        <label class="custom-file-upload">
                            <i class="fas fa-image"></i>
                            <input type="file" name="imagem" accept="image/*">
                        </label>
                        <button type="submit" class="btn-publicar">Publicar</button>
                    </div>
                </div>
            </form>
         </section>
         
         <section id="publicacoes">
            <h2> <i class="fas fa-comments"></i>Publicacoes</h2>
            <div id="lista-publicacoes">
                <!-- As publicacoes sao exibidas aqui -->
                 <?php if (!empty($publicacoes)): ?>
                    <?php foreach ($publicacoes as $publicacao): ?>
                        <div class="publicacao">
                            <div class="publicacao-header">
                                <?php if(!empty($publicacao['foto_perfil'])): ?>
                                    <img src="<?= htmlspecialchars($publicacao['foto_perfil']) ?>" class="avatar" alt="Perfil">
                                <?php else: ?>
                                    <div class="avatar-inicial"> <?= strtoupper(substr($publicacao['nome'], 0, 1)) ?></div>
                                    <?php endif; ?>
                                <div class="publicacao-info">
                                    <h3><?= htmlspecialchars($publicacao ['nome']); ?></h3>
                                    <small><?= date('d/m/Y \à\s H:i', strtotime($publicacao['data_criacao'])); ?></small>
                                </div>
                            </div>
                            
                            <div class="publicacao-conteudo">
                                <?php if (!empty($publicacao['conteudo'])): ?>
                                    <p><?= htmlspecialchars($publicacao['conteudo']); ?></p>
                                    <?php endif; ?>
                                <?php if(!empty($publicacao['imagem'])): ?>
                                    <div class="imagem-container">
                                        <img src="<?= htmlspecialchars($publicacao['imagem']) ?>" alt="Imagem da publicação"
                                        class="imagem-publicacao"
                                        loading="lazy">
                                    </div>
                                 <?php endif; ?>
                            </div>

                            <div class="publicaco-acoes">
                                <div class="acao-btn">
                                    <i class="far fa-thumbs-up"></i>
                                    <span>Curtir</span>
                                </div>
                                <div class="acao-btn">
                                    <i class="far fa-comment-alt"></i>
                                    <span>Comentar</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Nenhuma publicação encontrada. Seja o primeiro a compartilhar algo!</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Feed de Atividades e amigos -->
         <aside class="feed-atividades">
            <h2>Atividades Recentes</h2>
            <div class="lista-atividades">
                <!-- Lista de atividades -->
                <?php if (!empty($atividades)): ?>
                    <?php foreach ($atividades as $atividade): ?>
                        <div class="atividade-item">
                            <img src="<?= htmlspecialchars($atividade['foto-perfil']) ?>" alt="Foto de Perfil">
                            <div class="atividade-info">
                                <p><?= htmlspecialchars($atividade['nome']) ?> <?= htmlspecialchars($atividade['descricao']) ?></p>
                                <small><?= date('d/m/Y H:i', strtotime($atividade['data'])) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Nenhuma Atividade Recente</p>
                <?php endif; ?>
            </div>

            <h2>Amigues Online</h2>
            <div class="amigos-online">
                <!-- Lista de Amigos-->
                <?php if (!empty($amigos)): ?>
                    <?php foreach ($amigos as $amigo): ?>
                        <div class="amigo-item">
                            <img src="<?= htmlspecialchars($amigo['foto_perfil']) ?>" alt="Foto de Perfil">
                            <span><?= htmlspecialchars($amigo['nome']) ?></span>
                            <div class="amigo-acoes">
                                <a href="perfil.php?id=<?= $amigo['id'] ?>" class="btn-ver-perfil"> Ver Perfil</a>
                                <a href="mensagens.php?user=<?= $amigo['id']?>" class="btn-mensagem">Mensagem</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Nenhum amigo online no momento.</p>
                <?php endif; ?>
            </div>
         </aside>
    </main>
</body>
</html>
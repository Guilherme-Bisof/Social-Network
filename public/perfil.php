<?php 
        session_start();
        include_once('../database/config.php');
        // Verifica se o usuário está logado

        if (!isset($_SESSION['email'])){
            header('Location: index.php');
            exit();
        }

        // Obtém dados do Usuário Logado
        $email = $_SESSION['email'];
        $sql_usuario = "SELECT id, nome, usuario, foto_perfil FROM usuarios WHERE email = ?";
        $stmt = $conexao->prepare($sql_usuario);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0 ) {
            session_destroy();
            header('Location: index.php');
            exit();
        }

        $usuario_logado = $result->fetch_assoc();
        $_SESSION ['user_id'] = $usuario_logado['id'];
        $user_id = $usuario_logado['id'];
       

        // Recupera o email do usuário logado
        $perfil_id = $_GET['id'] ?? $user_id;

        // Consulta para buscar o nome e o usuário
        $sql_perfil = "SELECT nome, usuario, id, foto_perfil FROM usuarios WHERE id = ?";
        $stmt_perfil= $conexao->prepare($sql_perfil);
        $stmt_perfil->bind_param("i", $perfil_id);
        $stmt_perfil->execute();
        $result_perfil = $stmt_perfil->get_result();

        if ($result_perfil->num_rows > 0){
            $perfil = $result_perfil->fetch_assoc();
            $nome_perfil = $perfil['nome'];
            $usuario_perfil = $perfil['usuario'];
            $foto_perfil = $perfil['foto_perfil'];
        } else{
            echo "Perfil não encontrado.";
            exit();
        }

        // Se o formulário foi enviado (Método POST), processa o upload
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $perfil_id == $user_id){
            if (isset($_FILES['foto-perfil']) && $_FILES['foto-perfil']['error'] == 0){
                $foto = $_FILES['foto-perfil'];

                // Define o diretório para salvar as fotos 
                $diretorioUploadNoServidor = $_SERVER['DOCUMENT_ROOT'] . '/Social-Network/public/uploads/';
                $caminhoPublico = 'uploads/';
                if (!is_dir($diretorioUploadNoServidor)) {
                    mkdir($diretorioUploadNoServidorUpload, 0777, true);
                }
                
                // Gera um nome único para o arquivo (usando o ID do usuário e o timestamp)
                $nomeArquivo = $user_id . "_" . time() . "_" . basename($foto['name']);
            $caminhoCompleto = $diretorioUploadNoServidor  . $nomeArquivo;

                // Salve no banco apenas o caminho público
                $caminhoParaBanco = $caminhoPublico . $nomeArquivo;
        }

            // ...
            $nomeArquivo = $user_id . "_" . time() . "_" . basename($foto['name']);
            $caminhoCompleto = $diretorioUploadNoServidor . $nomeArquivo;
            $caminhoParaBanco = $caminhoPublico . $nomeArquivo;

        // Move o arquivo para o diretório de uploads
        if (move_uploaded_file($foto['tmp_name'], $caminhoCompleto)){
                //Atualiza o caminho da foto no banco dados
            $sqlUpdate = "UPDATE usuarios SET foto_perfil = ? WHERE id = ?";
            $stmtUpdate = $conexao->prepare($sqlUpdate);
            $stmtUpdate->bind_param("si", $caminhoParaBanco, $perfil_id);
            if ($stmtUpdate->execute()) {
                $_SESSION['foto-perfil'] = $caminhoParaBanco;
                header('Location: perfil.php?id=' . $perfil_id);
                 exit();
            } else {
                echo "Erro ao atualizar: " . $conexao->error;
            }
        } else{
            echo "Erro ao mover o arquivo.";
        }
    }

        // Consulta para contar o numero de Projetos

        $sqlProjetos = "SELECT COUNT(*) AS total_projetos FROM projetos WHERE usuario_id = $perfil_id";
        $resultProjetos = $conexao->query($sqlProjetos);
        $totalProjetos = ($resultProjetos->num_rows > 0) ? $resultProjetos->fetch_assoc()['total_projetos'] : 0;

        // Consulta para contar o número de amigos

        $sqlAmigos = "SELECT COUNT(*) AS total_amigos FROM amizades WHERE (usuario_1 = $perfil_id OR usuario_2 = $perfil_id) AND status ='aceito'";
        $resultAmigos = $conexao->query($sqlAmigos);
        $totalAmigos = ($resultAmigos->num_rows > 0) ? $resultAmigos->fetch_assoc()['total_amigos'] : 0;
        
        // Consulta para contar o número de publicações

        $sqlPublicacoes = "SELECT COUNT(*) AS total_publicacoes FROM publicacoes WHERE usuario_id = $perfil_id";
        $resultPublicacoes = $conexao->query($sqlPublicacoes);
        $totalPublicacoes = ($resultPublicacoes->num_rows > 0) ? $resultPublicacoes->fetch_assoc()['total_publicacoes'] : 0;

        // Consulta para buscar notificações não lidas do usuário
        $sqlNotificacoes = "SELECT id, mensagem FROM notificacoes WHERE usuario_id = $user_id AND status = 'pendente' ORDER BY data DESC";
        $resultNotificacoes = $conexao->query($sqlNotificacoes);

        $notificacoes = [];
        if($resultNotificacoes->num_rows > 0) {
            while ($notificacao = $resultNotificacoes->fetch_assoc()){
                $notificacoes[] = $notificacao;
            }
        }

        // Verifica relação entre amizade com o Perfil visitado
        // $perfil_id = $_GET['id'] ?? $user_id; //Id do perfil sendo visualizado

        if($perfil_id && $perfil_id != $user_id){
            // Consulta para verificar o status da amizade
            $sql_amizade = "SELECT * FROM amizades WHERE (usuario_1 = ? AND usuario_2 = ?) OR (usuario_1 = ? AND usuario_2 = ?) ORDER BY data DESC LIMIT 1";
            $stmt_amizade = $conexao->prepare($sql_amizade);
            $stmt_amizade->bind_param("iiii", $user_id, $perfil_id, $perfil_id, $user_id);
            $stmt_amizade->execute();
            $result_amizade = $stmt_amizade->get_result(); 

            // Resetar variáveis
            $amizade_status = null;
            $amizade = null;

            if ($result_amizade->num_rows > 0 ) {
                $amizade = $result_amizade->fetch_assoc();
                $amizade_status = $amizade['status'];
            }

            // Forçar recarga do status caso recusa
            if (isset($_GET['refused'])){
                $amizade_status = null;
                unset($amizade);
            }
        }

        // Fecha a conexão
        $conexao->close();
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
                <input type="text" name="termo" placeholder="Buscar usuários...">
                <button type="submit"></button>
             </form>
            <nav>
                <a href="inicio.php">Home</a>
                <a href="#">Projetos</a>
                <a href="index.php">Sair</a>
                <button id="btn-notificacoes">🔔 Notificações</button>
                <div class="notificacoes-dropdown" id="notificacoes-dropdown">
                    <?php if(count($notificacoes) > 0): ?>
                        <?php foreach ($notificacoes as $notif): ?>
                            <div class="notificacao">
                                <p><?php echo htmlspecialchars($notif['mensagem']); ?></p>
                                <button onclick="marcarComoLida(<?php echo $notif['id']; ?>)"> Marcar Como Lida</button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Sem notificações</p>
                    <?php endif; ?>
                </div>
            </nav>
            </section>
        </header>

        <main>
            <div class="main-container">
            <!-- Seção do Perfil -->
            <section class="profile-section">
                <div class="container">
                
                <!-- Formulário para atualizar a foto de perfil -->
                <form id="form-foto-perfil" action="" method="post" enctype="multipart/form-data">
                    
                    <!-- Input de arquivo oculto -->
                    <input type="file" name="foto-perfil" id="foto-perfil" accept="image/*" style="display: none;">
                
                    <div class="foto-perfil-container">
                        <img id="foto-perfil-usuario" src= "<?= htmlspecialchars($foto_perfil) ?>?<?= time() ?>" alt="Foto de Perfil" class="foto-perfil">

                        <!-- Botão "Editar Foto" -->
                        <label for="foto-perfil" class= "btn-editar-foto">📷 Editar Foto</label>
                    </div>
                </form>
                
                <!-- Seções de Projetos, Amigos e Publicações -->
                    <div class="social-stats">
                        
                        <div class="stat-item">
                            <h2>Projetos</h2>
                            <h3><?php echo $totalProjetos; ?></h3>
                        </div>
                        
                        <div class="stat-item">
                            <h2>Amigos</h2>
                            <h3><?php echo $totalAmigos;?></h3>
                        </div>
                        
                        <div class="stat-item">
                            <h2>Publicações</h2>
                            <h3><?php  echo $totalPublicacoes; ?></h3>
                        </div>
                    </div>
                     <!-- Botões "Seguir" , "Mensagem" e "Editar Perfil" -->
                    <div class="profile-actions">
                        <?php if ($perfil_id && $perfil_id != $user_id):?>
                            <?php if (!isset($amizade_status)):?>
                                <!-- Botão seguir (não existe relação) -->
                                <button class="btn-seguir" onclick="enviarPedidoAmizade(<?= $perfil_id ?>)">Seguir</button>

                            <?php elseif ($amizade_status == 'pendente'):?>
                                <!-- Pedido Pendente -->
                                 <?php if ($amizade['usuario_1'] == $user_id):?>
                                    <button class="btn-pendente" disabled>Pendente</button>
                                <?php else:?>
                                    <button class="btn-aceitar" onclick="aceitarPedido(<?= $amizade['id'] ?>)">Aceitar</button>
                                    <button class="btn-recusar" onclick="recusarPedido(<?= $amizade['id'] ?>, <?= $perfil_id ?>)">Recusar</button>
                                <?php endif; ?>
                            <?php elseif ($amizade_status == 'aceito'): ?>
                                <!-- Já são amigos -->
                                 <button class="btn-amigo" disabled>Amigos</button>
                            <?php endif; ?>
                                
                        <?php endif; ?>
                        <button type="submit" id= "Mensagem" class="btn-mensagem">Mensagem</button>
                        <a href="editar-perfil.php" class="btn-config" id="configuracoes">Editar Perfil</a>
                    </div>
                    
                </div>
                 <!-- Nomes do usuário -->
                <div class="nomes">
                    <h2><?php echo htmlspecialchars($usuario_perfil)?></h2>
                    <h2><?php echo htmlspecialchars($nome_perfil)?></h2>
                </div>
                
            </section>

            <!-- <section class="Configurações">
                <button type="submit" id="configuracoes" class="btn-config">Configurações</button>
            </section> -->

            <section class="posts-section">
                <h2 class="section-title">Atividades Recentes</h2>
                <div class="posts-grid">
                    <div class="post-card">
                        <h3>Minhas Postagens e meus Projetos</h3>
                    </div>    
                </div>
            </section>
        </div>
        </main>
    </body>
    </html>
<?php 
        session_start();
        include_once('../../database/config.php');
        // Verifica se o usuário está logado

        if (!isset($_SESSION['email'])){
            header('Location: index.php');
            exit();
        }

        // Obtém dados do Usuário Logado
        $email = $_SESSION['email'];
        $sql_usuario = "SELECT id, nome, foto_perfil FROM usuarios WHERE email = ?";
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
        $sql_perfil = "SELECT nome, id, foto_perfil FROM usuarios WHERE id = ?";
        $stmt_perfil= $conexao->prepare($sql_perfil);
        $stmt_perfil->bind_param("i", $perfil_id);
        $stmt_perfil->execute();
        $result_perfil = $stmt_perfil->get_result();

        if ($result_perfil->num_rows > 0){
            $perfil = $result_perfil->fetch_assoc();
            $nome_perfil = $perfil['nome'];
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
                $diretorioUploadNoServidor = $_SERVER['DOCUMENT_ROOT'] . '/social-network/public/uploads/';
                $caminhoPublico = '/social-network/public/uploads/';
                if (!is_dir($diretorioUploadNoServidor)) {
                    mkdir($diretorioUploadNoServidor, 0777, true);
                }
                
                // Gera um nome único para o arquivo (usando o ID do usuário e o timestamp)
                $nomeArquivo = $user_id . "_" . time() . "_" . basename($foto['name']);
                $caminhoCompleto = $diretorioUploadNoServidor  . $nomeArquivo;

                // Salve no banco apenas o caminho público
                $caminhoParaBanco = $caminhoPublico . $nomeArquivo;
        }

        // Move o arquivo para o diretório de uploads
        if (move_uploaded_file($foto['tmp_name'], $caminhoCompleto)){
                //Atualiza o caminho da foto no banco dados
            $sqlUpdate = "UPDATE usuarios SET foto_perfil = ? WHERE id = ?";
            $stmtUpdate = $conexao->prepare($sqlUpdate);
            $stmtUpdate->bind_param("si", $caminhoParaBanco, $perfil_id);
            if ($stmtUpdate->execute()) {
                $_SESSION['foto-perfil'] = $caminhoParaBanco;
                header('Location: ../pages/perfil.php?id=' . $perfil_id);
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
       
    ?>

    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="src/icons/favicon.ico" type="image/x-icon">
        <title>Perfil de Usuário</title>

        <!-- FAVICON -->
        <link rel="shortcut icon" href="../src/icons/favicon.ico" type="image/x-icon">

        <!-- Fonte -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">

        <!-- CSS -->
        <link rel="stylesheet" href="../src/css/perfil.css">

        <!--JavaSript-->
        <script src="../src/js/perfil.js" defer></script>

    </head>
    <body>
        <header>
            <section class="Nav">
            
            <a href="inicio.php" class="logo">
                <img src="../src/img/logo.png" alt="ConnectU" class="logo-img">
            </a>
            
            <!-- Busca -->
             <form action="../actions/buscar.php" class="busca-perfis" method="GET">
                <input type="text" name="termo" placeholder="Buscar usuários...">
                <button type="submit"></button>
             </form>
            <nav>
                <a href="projects.php" class="nav-link">Projetos</a>
                <a href="index.php" class="nav-link" >Sair</a>
                <button class="nav-link" id="btn-notificacoes">🔔 Notificações</button>
                <div class="notificacoes-dropdown" id="notificacoes-dropdown">
                    <?php if(count($notificacoes) > 0): ?>
                        <?php foreach ($notificacoes as $notif): ?>
                            <div class="notificacao">
                                <p><?php echo htmlspecialchars($notif['mensagem']); ?></p>
                                <button onclick="marcarComoLida(<?php echo $notif['id']; ?>)"> Marcar Como Lida</button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
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
                            <?php if ($perfil_id == $user_id): ?>

                            <!-- Botão "Editar Foto" -->
                            <label for="foto-perfil" class= "btn-editar-foto">📷 Editar Foto</label>
                            <?php endif; ?>
                        </div>
                    </form>
                    
                    <!-- Nomes do usuário -->
                    <div class="nomes">
                        <h2><?php echo htmlspecialchars($nome_perfil)?></h2>
                    </div>
                    
                    <!-- Seções de Projetos, Amigos e Publicações -->
                        <div class="social-stats">
                            
                            <div class="stat-item">
                                <h3><?php echo $totalProjetos; ?></h3>
                                <p>Projetos</p>
                            </div>
                            
                            <div class="stat-item">
                                <h3><?php echo $totalAmigos;?></h3>
                                <p>Amigos</p>
                            </div>
                            
                            <div class="stat-item">
                                <h3><?php  echo $totalPublicacoes; ?></h3>
                                <p>Publicações</p>
                            </div>
                        </div>
                        <!-- Botões "Seguir" , "Mensagem" e "Editar Perfil" -->
                        <div class="profile-actions">
                            <?php if ($perfil_id && $perfil_id != $user_id):?>
                                <?php if (!isset($amizade_status)):?>
                                    <button class="btn-follow" onclick="enviarPedidoAmizade(<?= $perfil_id ?>)">➕ Seguir</button>
                                <?php elseif ($amizade_status == 'pendente'):?>
                                    <?php if ($amizade['usuario_1'] == $user_id):?>
                                        <button class="btn-pendent" disabled>⏳ Pendente</button>
                                    <?php else:?>
                                        <button class="btn-accept" onclick="aceitarPedido(<?= $amizade['id'] ?>)">✔️ Aceitar</button>
                                        <button class="btn-decline" onclick="recusarPedido(<?= $amizade['id'] ?>, <?= $perfil_id ?>)">❌ Recusar</button>
                                    <?php endif; ?>
                                <?php elseif ($amizade_status == 'aceito'): ?>
                                    <button class="btn-friend" disabled>✅ Amigos</button>
                                <?php endif; ?>
                            <?php endif; ?>

                            <button type="button" class="btn-message">💬 Mensagem</button>
                            <?php if ($perfil_id == $user_id): ?>
                                <a href="../actions/editar-perfil.php" class="btn-config">⚙️ Editar Perfil</a>
                            <?php endif; ?>
                        </div>
                        
                    </div>
                    
                </section>

                <!-- <section class="Configurações">
                    <button type="submit" id="configuracoes" class="btn-config">Configurações</button>
                </section> -->

                <section class="posts-section">
                    <h2 class="section-title">Atividades Recentes</h2>
                    <div class="posts-grid">
                        <?php 
                        // aqui futuramente você vai buscar as postagens/projetos do usuário
                        // por enquanto deixo um card exemplo
                        ?>
                        <div class="post-card">
                            <h3>🚀 Projeto Exemplo</h3>
                            <p>Esse é um resumo do projeto ou postagem. Você pode exibir título, descrição curta e data.</p>
                            <span class="post-date">Publicado em <?= date("d/m/Y") ?></span>
                        </div>
                    </div>
                </section>
                <aside class="sidebar-direita">
                <!-- Feed de Atividades -->
                <div class="sidebar-widget">
                    <div class="widget-header">
                        <svg class="widget-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <h3 class="widget-title">Feed de Atividades</h3>
                    </div>
                    <div class="activity-list">
                        <?php
                        // Consulta para buscar atividades recentes dos amigos
                        $sql_atividades = "
                            SELECT u.nome, u.foto_perfil, a.tipo, a.descricao, a.data,
                            TIMESTAMPDIFF(MINUTE, a.data, NOW()) as minutos_atras
                            FROM atividades a
                            JOIN usuarios u ON a.usuario_id = u.id
                            JOIN amizades am 
                            ON (
                                    (am.usuario_1 = ? AND am.usuario_2 = u.id) 
                                OR (am.usuario_2 = ? AND am.usuario_1 = u.id)
                                )
                            AND am.status = 'aceito'
                            WHERE a.usuario_id != ?
                            ORDER BY a.data DESC
                            LIMIT 5

                        ";

                        $stmt = $conexao->prepare($sql_atividades);

                        if (!$stmt) {
                            die("Erro no prepare: " . $conexao->error . " | SQL: " . $sql_atividades);
                        }

                        $stmt->bind_param("iii", $user_id, $user_id, $user_id);
                        $stmt->execute();
                        $result_atividades = $stmt->get_result();
                                                
                        if ($result_atividades->num_rows > 0):
                            while ($atividade = $result_atividades->fetch_assoc()):
                                $iniciais = strtoupper(substr($atividade['nome'], 0, 1) . substr(strstr($atividade['nome'], ' '), 1, 1));
                                
                                // Determinar tempo decorrido
                                $tempo = $atividade['minutos_atras'];
                                if ($tempo < 1) $tempo_texto = "agora";
                                elseif ($tempo < 60) $tempo_texto = "há {$tempo} min";
                                elseif ($tempo < 1440) $tempo_texto = "há " . floor($tempo/60) . "h";
                                else $tempo_texto = "há " . floor($tempo/1440) . " dias";
                        ?>
                            <div class="activity-item">
                                <div class="activity-avatar"><?= $iniciais ?></div>
                                <div class="activity-content">
                                    <div class="activity-text">
                                        <strong><?= htmlspecialchars($atividade['nome']) ?></strong> <?= htmlspecialchars($atividade['descricao']) ?>
                                    </div>
                                    <div class="activity-time"><?= $tempo_texto ?></div>
                                </div>
                            </div>
                        <?php 
                            endwhile;
                        else: 
                        ?>
                            <!-- Atividades de exemplo se não houver dados -->
                            <div class="activity-item">
                                <div class="activity-avatar">MR</div>
                                <div class="activity-content">
                                    <div class="activity-text">
                                        <strong>Maria Rosa</strong> curtiu seu projeto "Sistema de Login"
                                    </div>
                                    <div class="activity-time">há 5 minutos</div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-avatar">JS</div>
                                <div class="activity-content">
                                    <div class="activity-text">
                                        <strong>João Silva</strong> comentou no seu projeto
                                    </div>
                                    <div class="activity-time">há 15 minutos</div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-avatar">AL</div>
                                <div class="activity-content">
                                    <div class="activity-text">
                                        <strong>Ana Lima</strong> publicou um novo projeto: "Dashboard Analytics"
                                    </div>
                                    <div class="activity-time">há 1 hora</div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Calendário de Eventos -->
                <div class="sidebar-widget">
                    <div class="widget-header">
                        <svg class="widget-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="widget-title">Eventos</h3>
                    </div>
                    <div class="calendar-header">
                        <div class="calendar-month">Agosto 2025</div>
                        <div class="calendar-nav">
                            <button class="nav-btn" onclick="changeMonth(-1)">‹</button>
                            <button class="nav-btn" onclick="changeMonth(1)">›</button>
                        </div>
                    </div>
                    <div class="event-list">
                        <?php
                        // Consulta para buscar eventos futuros
                        $sql_eventos = "
                            SELECT titulo, descricao, data_evento, local 
                            FROM eventos 
                            WHERE data_evento >= CURDATE() 
                            ORDER BY data_evento ASC 
                            LIMIT 4
                        ";
                        $result_eventos = $conexao->query($sql_eventos);
                        
                        if ($result_eventos && $result_eventos->num_rows > 0):
                            while ($evento = $result_eventos->fetch_assoc()):
                                $data = new DateTime($evento['data_evento']);
                                $dia = $data->format('d');
                                $mes = strtoupper($data->format('M'));
                        ?>
                            <div class="event-item">
                                <div class="event-date">
                                    <div><?= $dia ?></div>
                                    <div><?= $mes ?></div>
                                </div>
                                <div class="event-info">
                                    <h4><?= htmlspecialchars($evento['titulo']) ?></h4>
                                    <p><?= htmlspecialchars($evento['descricao']) ?></p>
                                </div>
                            </div>
                        <?php 
                            endwhile;
                        else: 
                        ?>
                            <!-- Eventos de exemplo -->
                            <div class="event-item">
                                <div class="event-date">
                                    <div>30</div>
                                    <div>AGO</div>
                                </div>
                                <div class="event-info">
                                    <h4>Hackathon ConnectU</h4>
                                    <p>14:00 - Tema: Sustentabilidade</p>
                                </div>
                            </div>
                            <div class="event-item">
                                <div class="event-date">
                                    <div>02</div>
                                    <div>SET</div>
                                </div>
                                <div class="event-info">
                                    <h4>Workshop React</h4>
                                    <p>19:30 - Online via Discord</p>
                                </div>
                            </div>
                            <div class="event-item">
                                <div class="event-date">
                                    <div>05</div>
                                    <div>SET</div>
                                </div>
                                <div class="event-info">
                                    <h4>Meetup Frontend</h4>
                                    <p>18:00 - Hub de Inovação SP</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Seção Para Você -->
                <div class="sidebar-widget">
                    <div class="widget-header">
                        <svg class="widget-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <h3 class="widget-title">Para Você</h3>
                    </div>
                    <div class="recommendations">
                        <?php
                        // Consulta para buscar recomendações baseadas no perfil do usuário
                        $sql_recomendacoes = "
                            SELECT p.titulo, p.descricao, u.nome as autor, 'Projeto' as tipo
                            FROM projetos p 
                            JOIN usuarios u ON p.usuario_id = u.id 
                            WHERE p.usuario_id != ? 
                            ORDER BY p.data_criacao DESC 
                            LIMIT 4
                        ";
                        $stmt_rec = $conexao->prepare($sql_recomendacoes);
                        $stmt_rec->bind_param("i", $user_id);
                        $stmt_rec->execute();
                        $result_recomendacoes = $stmt_rec->get_result();
                        
                        if ($result_recomendacoes && $result_recomendacoes->num_rows > 0):
                            while ($rec = $result_recomendacoes->fetch_assoc()):
                        ?>
                            <div class="recommendation">
                                <div class="rec-header">
                                    <div class="rec-title"><?= htmlspecialchars($rec['titulo']) ?></div>
                                    <div class="rec-type"><?= htmlspecialchars($rec['tipo']) ?></div>
                                </div>
                                <div class="rec-description">
                                    <?= htmlspecialchars(substr($rec['descricao'], 0, 100)) ?>...
                                </div>
                                <div class="rec-author">Por: <?= htmlspecialchars($rec['autor']) ?></div>
                            </div>
                        <?php 
                            endwhile;
                        else: 
                        ?>
                            <!-- Recomendações de exemplo -->
                            <div class="recommendation">
                                <div class="rec-header">
                                    <div class="rec-title">Sistema de Chat em Tempo Real</div>
                                    <div class="rec-type">Projeto</div>
                                </div>
                                <div class="rec-description">
                                    Implementação de chat usando Socket.io e Node.js. Perfeito para expandir seus conhecimentos em backend.
                                </div>
                                <div class="rec-author">Por: Rafael Oliveira</div>
                            </div>
                            <div class="recommendation">
                                <div class="rec-header">
                                    <div class="rec-title">Tutorial: Animações com CSS</div>
                                    <div class="rec-type">Tutorial</div>
                                </div>
                                <div class="rec-description">
                                    Aprenda a criar animações fluidas e profissionais usando apenas CSS. Inclui exemplos práticos.
                                </div>
                                <div class="rec-author">Por: Camila Designer</div>
                            </div>
                            <div class="recommendation">
                                <div class="rec-header">
                                    <div class="rec-title">Oportunidade: Junior Frontend</div>
                                    <div class="rec-type">Vaga</div>
                                </div>
                                <div class="rec-description">
                                    Startup em crescimento procura dev júnior. React, TypeScript. Remoto, salário competitivo.
                                </div>
                                <div class="rec-author">Por: TechCorp Brasil</div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </aside>
            </div>
        </main>
    </body>
</html>

<?php $conexao->close(); ?>
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

    // Verifica se é o criador do grupo
    $eh_criador = ($usuario_logado_id == $grupo['criador_id']);

    // Busca solicitações pendentes (se for criador)

    $solicitacoes_pendentes = [];
    if ($eh_criador) {
        $sql_pendentes = "SELECT u.id, u.nome, u.foto_perfil FROM grupos_membros gm JOIN usuarios u ON gm.usuario_id = u.id  WHERE gm.grupo_id = ? AND gm.status = 'pendente'";
        $stmt_pendentes = $conexao->prepare($sql_pendentes);
        $stmt_pendentes->bind_param('i', $grupo_id);
        $stmt_pendentes->execute();
        $solicitacoes_pendentes = $stmt_pendentes->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Buscar membros do grupo
    $sql_membros = "SELECT u.id, u.nome, u.foto_perfil, gm.eh_admin FROM grupos_membros gm JOIN usuarios u ON gm.usuario_id = u.id WHERE gm.grupo_id = ?";
    $stmt_membros = $conexao->prepare($sql_membros);
    $stmt_membros->bind_param('i', $grupo_id);
    $stmt_membros->execute();
    $membros = $stmt_membros->get_result()->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($grupo['nome']) ?> - ConnectYUU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/src/css/inicio.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <section class="Nav">
            <h1>ConnectU</h1>
            <nav>
                <a href="inicio.php" class="nav-link">Início</a>
                <a href="perfil.php" class="nav-link">Perfil</a>
                <a href="../actions/sair.php" class="nav-link">Sair</a>
           </nav>
        </section>
    </header>

    <main>
        <div class="container mt-5">
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
                    <?php if($eh_criador): ?>
                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#solicitacoesModal">Solicitações Pendentes (<?= count($solicitacoes_pendentes) ?>)</button>
                    <?php endif; ?>
                    <button class="btn btn-danger" onclick="sairDoGrupo(<?= $grupo_id ?>)">Sair do Grupo</button>
                <?php else: ?>
                    <button class="btn btn-primary" onclick="solicitarEntrada(<?= $grupo_id ?>)">Solicitar entrada</button>
                <?php endif; ?>
             </div>

            <!-- Modal de Solicitações Pendentes -->
            <div class="modal fade" id="solicitacoesModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Solicitações Pendentes</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <?php foreach ($solicitacoes_pendentes as $solicitante): ?>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center">
                                        <img src="<?= htmlspecialchars($solicitante['foto_perfil']) ?>" 
                                             class="rounded-circle me-2" 
                                             width="40" 
                                             height="40">
                                        <span><?= htmlspecialchars($solicitante['nome']) ?></span>
                                    </div>
                                    <div>
                                        <button class="btn btn-sm btn-success" 
                                                onclick="gerenciarSolicitacao(<?= $solicitante['id'] ?>, 'aceitar')">
                                            Aceitar
                                        </button>
                                        <button class="btn btn-sm btn-danger" 
                                                onclick="gerenciarSolicitacao(<?= $solicitante['id'] ?>, 'recusar')">
                                            Recusar
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
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
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <?php if ($membro['eh_admin']): ?>
                                                <span class="badge bg-warning">Admin</span>
                                            <?php endif; ?>
                                            <h5 class="card-title"><?= htmlspecialchars($membro['nome']) ?></h5>
                                        </div>
                                        <?php if ($eh_criador && $membro['id'] !=$grupo['criador_id']): ?>
                                            <button class="btn btn-sm btn-info" onclick="promoverAdmin(<?= $membro['id'] ?>)" title="Promover a Administrador">
                                                <i class="fas fa-user-shield"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
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

     <!-- Scripts -->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function solicitarEntrada(grupoId) {
            fetch(`../../actions/solicitar_entrada_grupo.php?grupo_id=${grupoId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Solicitação enviada com sucesso!');
                        location.reload();
                    } else {
                        alert('Erro: ' + data.message);
                    }
                });
        }

        function gerenciarSolicitacao(usuarioId, acao) {
            const formData = new FormData();
            formData.append('usuario_id', usuarioId);
            formData.append('acao', acao);

            fetch(`../../actions/gerenciar_solicitacao.php?grupo_id=<?= $grupo_id ?>`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erro: ' + data.message);
                }
            });
        }

        function promoverAdmin(usuarioId) {
            if (confirm('Tem certeza que deseja promover este usuário a administrador?')) {
                fetch(`../../actions/promover_admin.php?grupo_id=<?= $grupo_id ?>&usuario_id=${usuarioId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Erro: ' + data.message);
                        }
                    });
            }
        }

        function sairDoGrupo(grupoId) {
            if (confirm('Tem certeza que deseja sair do grupo?')) {
                fetch(`../../actions/sair_grupo.php?grupo_id=${grupoId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.href = 'inicio.php';
                        } else {
                            alert('Erro: ' + data.message);
                        }
                    });
            }
        }
    </script>
    
</body>
</html>
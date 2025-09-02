<?php 
header('Content-Type: text/html; charset=UTF-8');
session_start();
include('../../database/config.php');

// Verificar conexão com o banco de dados
if (!$conexao) {
    die("Database connection failed: " . mysqli_connect_error());
}

if((!isset($_SESSION['email']) == true) and (!isset($_SESSION['senha']) == true)) {
    unset($_SESSION['email']);
    unset($_SESSION['senha']);
    header('Location: ../pages/login.php');
    exit();
}

$logado = $_SESSION['email'];

// Obter dados do usuário logado
$sql_user = "SELECT id, nome, foto_perfil FROM usuarios WHERE email = ?";
$stmt_user = $conexao->prepare($sql_user);

if ($stmt_user === false) {
    die("Error in prepare: " . $conexao->error);
}

$stmt_user->bind_param('s', $logado);

if (!$stmt_user->execute()) {
    die("Error in execute: " . $stmt_user->error);
}

$result_user = $stmt_user->get_result();

if ($result_user->num_rows === 0) {
    die("User not found with email: " . $logado);
}

$usuario = $result_user->fetch_assoc();
    
$user_id = $usuario['id'];
$nome_usuario = $usuario['nome'];
$foto_perfil = $usuario['foto_perfil'];

// Consultar projetos do usuário - VERIFICAR SE A TABELA projetos EXISTE
$sql_projetos = "SELECT p.*, 
                (SELECT COUNT(*) FROM projetos_membros WHERE projeto_id = p.id) as total_membros,
                (SELECT nome FROM usuarios WHERE id = p.creator_id) as creator_name
                FROM projetos p 
                WHERE p.id IN (SELECT projeto_id FROM projetos_membros WHERE usuario_id = ?)
                ORDER BY p.data_criacao DESC";

$stmt_projetos = $conexao->prepare($sql_projetos);

// Verificar se a preparação da consulta foi bem-sucedida
if ($stmt_projetos === false) {
    // Se falhar, criar a tabela projetos (se não existir)
    criarTabelaProjetos($conexao);
    
    // Tentar preparar a consulta novamente
    $stmt_projetos = $conexao->prepare($sql_projetos);
    
    if ($stmt_projetos === false) {
        die("Error preparing projetos query: " . $conexao->error);
    }
}

$stmt_projetos->bind_param('i', $user_id);

if (!$stmt_projetos->execute()) {
    die("Error executing projetos query: " . $stmt_projetos->error);
}

$result_projetos = $stmt_projetos->get_result();
$projetos = $result_projetos->fetch_all(MYSQLI_ASSOC);

// Consultar projetos públicos disponíveis
$sql_projetos_abertos = "SELECT p.*, 
                        (SELECT COUNT(*) FROM projetos_membros WHERE projeto_id = p.id) as total_membros,
                        (SELECT nome FROM usuarios WHERE id = p.creator_id) as creator_name
                        FROM projetos p 
                        WHERE p.privacidade = 'publico' 
                        AND p.id NOT IN (SELECT projeto_id FROM projetos_membros WHERE usuario_id = ?)
                        ORDER BY p.data_criacao DESC 
                        LIMIT 10";

$stmt_abertos = $conexao->prepare($sql_projetos_abertos);

if ($stmt_abertos === false) {
    die("Error preparing projetos_abertos query: " . $conexao->error);
}

$stmt_abertos->bind_param('i', $user_id);

if (!$stmt_abertos->execute()) {
    die("Error executing projetos_abertos query: " . $stmt_abertos->error);
}

$result_abertos = $stmt_abertos->get_result();
$projetos_abertos = $result_abertos->fetch_all(MYSQLI_ASSOC);

// Função para criar a tabela projetos se não existir
function criarTabelaProjetos($conexao) {
    $sql = "CREATE TABLE IF NOT EXISTS projetos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(255) NOT NULL,
        descricao TEXT,
        area VARCHAR(100),
        status ENUM('planejamento', 'andamento', 'concluido') DEFAULT 'planejamento',
        privacidade ENUM('publico', 'privado') DEFAULT 'publico',
        creator_id INT NOT NULL,
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (creator_id) REFERENCES usuarios(id)
    )";
    
    if (!$conexao->query($sql)) {
        die("Error creating projetos table: " . $conexao->error);
    }
    
    $sql = "CREATE TABLE IF NOT EXISTS projetos_membros (
        id INT AUTO_INCREMENT PRIMARY KEY,
        projeto_id INT NOT NULL,
        usuario_id INT NOT NULL,
        data_entrada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('pendente', 'aceito', 'rejeitado') DEFAULT 'pendente',
        FOREIGN KEY (projeto_id) REFERENCES projetos(id),
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    )";
    
    if (!$conexao->query($sql)) {
        die("Error creating projetos_membros table: " . $conexao->error);
    }
    
    echo "<!-- Tables projetos and projetos_membros created successfully -->";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projetos | ConnectU</title>
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="../src/css/projetos.css">
</head>
<body>
    <!-- Header (igual ao do inicio.php) -->
    <header>
        <section class="Nav">
            <a href="inicio.php" class="logo">
                <img src="../src/img/logo.png" alt="ConnectU" class="logo-img">
            </a>

            <!-- Busca-->
            <form action="../actions/buscar.php" class="buscar-perfis" method="GET">
                <input type="text" name="termo" placeholder="Buscar...">
                <input type="hidden" name="tipo" value="projetos">
                <button type="submit"></button>
            </form>

            <nav>
                <a href="inicio.php" class="nav-link">Início</a>
                <a href="perfil.php" class="nav-link">Perfil</a>
                <a href="projects.php" class="nav-link active">Projetos</a>
                <a href="../actions/sair.php" class="nav-link">Sair</a>
            </nav>
        </section>
    </header>

    <main class="container-projetos">
        <div class="projetos-header">
            <h1><i class="fas fa-project-diagram"></i> Projetos Acadêmicos</h1>
            <button class="btn-criar-projeto" data-bs-toggle="modal" data-bs-target="#modalCriarProjeto">
                <i class="fas fa-plus"></i> Criar Novo Projeto
            </button>
        </div>

        <!-- Meus Projetos -->
        <section class="meus-projetos">
            <h2>Meus Projetos</h2>
            <div class="projetos-grid">
                <?php if (!empty($projetos)): ?>
                    <?php foreach ($projetos as $projeto): ?>
                        <div class="projeto-card">
                            <div class="projeto-header">
                                <h3><?= htmlspecialchars($projeto['titulo']) ?></h3>
                                <span class="projeto-status <?= $projeto['status'] ?>"><?= $projeto['status'] ?></span>
                            </div>
                            <p class="projeto-descricao"><?= htmlspecialchars($projeto['descricao']) ?></p>
                            <div class="projeto-metadata">
                                <span><i class="fas fa-users"></i> <?= $projeto['total_membros'] ?> membros</span>
                                <span><i class="fas fa-user"></i> Criado por: <?= htmlspecialchars($projeto['creator_name']) ?></span>
                                <span><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($projeto['data_criacao'])) ?></span>
                            </div>
                            <div class="projeto-actions">
                                <a href="projeto.php?id=<?= $projeto['id'] ?>" class="btn-ver-projeto">Ver Projeto</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="sem-projetos">
                        <i class="fas fa-project-diagram"></i>
                        <p>Você ainda não participa de nenhum projeto.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Projetos Disponíveis -->
        <section class="projetos-disponiveis">
            <h2>Projetos Disponíveis</h2>
            <div class="projetos-grid">
                <?php if (!empty($projetos_abertos)): ?>
                    <?php foreach ($projetos_abertos as $projeto): ?>
                        <div class="projeto-card">
                            <div class="projeto-header">
                                <h3><?= htmlspecialchars($projeto['titulo']) ?></h3>
                                <span class="projeto-status <?= $projeto['status'] ?>"><?= $projeto['status'] ?></span>
                            </div>
                            <p class="projeto-descricao"><?= htmlspecialchars($projeto['descricao']) ?></p>
                            <div class="projeto-metadata">
                                <span><i class="fas fa-users"></i> <?= $projeto['total_membros'] ?> membros</span>
                                <span><i class="fas fa-user"></i> Criado por: <?= htmlspecialchars($projeto['creator_name']) ?></span>
                                <span><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($projeto['data_criacao'])) ?></span>
                            </div>
                            <div class="projeto-actions">
                                <button class="btn-participar-projeto" data-projeto-id="<?= $projeto['id'] ?>">
                                    Participar do Projeto
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="sem-projetos">
                        <i class="fas fa-search"></i>
                        <p>Nenhum projeto disponível no momento.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Modal para Criar Projeto -->
    <div class="modal fade" id="modalCriarProjeto" tabindex="-1" aria-labelledby="modalCriarProjetoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCriarProjetoLabel">Criar Novo Projeto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formCriarProjeto" action="../actions/criar_projeto.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título do Projeto</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" required>
                        </div>
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="4" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="area" class="form-label">Área do Conhecimento</label>
                                    <select class="form-select" id="area" name="area" required>
                                        <option value="">Selecione uma área</option>
                                        <option value="Tecnologia">Tecnologia</option>
                                        <option value="Engenharia">Engenharia</option>
                                        <option value="Administração">Administração</option>
                                        <option value="Design">Design</option>
                                        <option value="Saúde">Saúde</option>
                                        <option value="Educação">Educação</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status do Projeto</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="planejamento">Planejamento</option>
                                        <option value="andamento">Em Andamento</option>
                                        <option value="concluido">Concluído</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="habilidades" class="form-label">Habilidades Necessárias</label>
                            <input type="text" class="form-control" id="habilidades" name="habilidades" 
                                   placeholder="Ex: PHP, JavaScript, Design UX, Gestão de Projetos">
                        </div>
                        <div class="mb-3">
                            <label for="privacidade" class="form-label">Privacidade</label>
                            <select class="form-select" id="privacidade" name="privacidade" required>
                                <option value="publico">Público (qualquer um pode solicitar participação)</option>
                                <option value="privado">Privado (somente por convite)</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="formCriarProjeto" class="btn btn-primary">Criar Projeto</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript Personalizado -->
    <script src="../src/js/projetos.js"></script>
</body>
</html>
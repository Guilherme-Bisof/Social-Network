<?php 
    session_start();
    include('../../database/config.php');

    // Verifica a conexão
    if ($conexao->connect_error) {
        die("Erro na conexão: " . $conexao->connect_error);
    }

    // Verificar se o formuçário foi enviado via POST
    $user_id = $_SESSION['user_id'];
    
    // Buscar ados do usuário no banco
    $sql = "SELECT usuario, biografia FROM usuarios WHERE  id = $user_id";
    $result = $conexao->query($sql);

    if ($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $usuario_atual = htmlspecialchars($row['usuario']);
        $biografia_atual = htmlspecialchars($row['biografia']);
    } else {
        $usuario_atual = "Usuário não encontrado.";
        $biografia_atual = "";
    }
    $conexao->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="../src/css/editar-perfil.css">
</head>
<body>
    <div class="editar-container">
    <h1>Editar Perfil</h1>
    
        <form action="processar-edicao.php" method="post" enctype="multipart/form-data">
        
            <div class="form-group">
            <label for="usuario">Nome de Usuário</label> 
            <input type="text" name="usuario" id="usuario" value="<?php echo $usuario_atual; ?>">
            </div>

            <div class="form-group">
            <label for="bio">Biografia</label>
            <textarea name="bio" id="bio" rows="4"><?php echo $biografia_atual?></textarea>
            </div>

            <button type="submit">Salvar Alterações</button>
        </form>
    </div>
</body>
</html>

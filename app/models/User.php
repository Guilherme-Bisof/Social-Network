<?php 
    class User{
        private $conexao;

        public function __construct($conexao) {
            $this->conexao = $conexao;
        }

        // Criar novo user
        public function create ($nome, $email, $senha, $sexo, $tipo_usuario) {
            $senhaHash = password_hash($senha, PASSWORD_BCRYPT);

            $sql = "INSERT INTO usuarios (nome, email, senha, sexo, tipo_usuario) VALUES (?, ? ,? ,? ,?)";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bind_param("sssss", $nome, $email, $senhaHash, $sexo, $tipo_usuario);

            return $stmt->execute();
        }

        // Buscar user por e-mail
        public function findByEmail($email) {
            $sql = "SELECT * FROM usuarios WHERE email = ?";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        }

        // Buscar user por ID
        public function findById($id) {
            $sql = "SELECT * FROM usuarios WHERE id = ?";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        }

        // Atualizar Perfil
        public function update($id, $nome, $sexo, $tipo_usuario) {
            $sql = "UPDATE usuarios SET nome = ?,sexo = ? , tipo_usuario = ? WHERE id = ?";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bind_param("sssi", $nome, $sexo, $tipo_usuario, $id);

            return $stmt->execute();
        }

        // Atualizar senha
        public function updatePassword($id, $novaSenha) {
            $senhaHash = password_hash($novaSenha, PASSWORD_BCRYPT);
            $sql = "UPDATE usuarios SET senha = ? WHERE id = ?";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bind_param("si", $senhaHash, $id);

            return $stmt->execute();
        }

        // Delete user
        public function delete($id) {
            $sql = "DELETE FROM usuarios WHERE id = ?";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bind_param("i", $id);

            return $stmt->execute();
        }
    }
?>
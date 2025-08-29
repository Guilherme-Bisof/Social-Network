<?php
    include_once(__DIR__ . '/../models/User.php');

class AuthController {
    private $userModel;

    public function __construct($conexao) {
        $this->userModel = new User($conexao);
    }

    public function register($nome, $usuario, $email, $senha, $sexo, $tipo_usuario) {
        // Verificar se já existe usuário
        $existingUser = $this->userModel->findByEmail($email);
        if($existingUser) {
            return "O email já está registrado.";
        }

        // Criar novo usuário
        if($this->userModel->create($nome, $usuario, $email, $senha, $sexo, $tipo_usuario)) {
            return true;
        } else {
            return "Erro ao registrar usuário.";
        }
    }

    public function login($email, $senha) {
        $user = $this->userModel->findByEmail($email);

        if($user && password_verify($senha, $user['senha'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nome'] = $user['nome'];
            return true;
        }
        return "Email ou senha inválidos.";
    }

    // Função de logout
    public function logout() {
        session_start();
        session_destroy();
        header("Location: ../pages/index.php");
        exit;
    }
}
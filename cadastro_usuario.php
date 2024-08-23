<?php
require 'connect.inc.php'; 

class Usuarios {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($post) {
        $nome = $this->conn->real_escape_string($post['nome']);
        $email = $this->conn->real_escape_string($post['email']);
        $matricula = $this->conn->real_escape_string($post['matricula']);
        $senha = password_hash($post['senha'], PASSWORD_DEFAULT); // Criptografa a senha
        $tipoUsuario = $this->conn->real_escape_string($post['tipoUsuario']); // Obtém o tipo de usuário
    
        $sql = "INSERT INTO Usuarios (nome, email, matricula, senha, tipoUsuario)
                VALUES ('$nome', '$email', '$matricula', '$senha', '$tipoUsuario')";
    
        if (!$this->conn->query($sql)) {
            echo "Erro ao executar o SQL: " . $this->conn->error;
            return false;
        }
    
        return true;
    }
    public function matriculaExists($matricula) {
        $matricula = $this->conn->real_escape_string($matricula);
        $sql = "SELECT id FROM Usuarios WHERE matricula = '$matricula'";
        $result = $this->conn->query($sql);
        return $result->num_rows > 0;
    }
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = new Usuarios($conn);

    if ($usuario->matriculaExists($_POST['matricula'])) {
        $error_message = "A matrícula já está em uso.";
    } else {
        // Cria o novo usuário
        if ($usuario->create($_POST)) {
            header('Location: index.html'); 
            exit();
        } else {
            $error_message = "Erro ao cadastrar o usuário.";
        }
    }
}

include 'cadastro_usuario.html';
?>

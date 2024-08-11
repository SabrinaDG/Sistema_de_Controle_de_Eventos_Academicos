<?php
require 'connect.inc.php'; // Inclui o arquivo de conexão ao banco de dados

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

        $sql = "INSERT INTO Usuarios (nome, email, matricula, senha)
                VALUES ('$nome', '$email', '$matricula', '$senha')";

        return $this->conn->query($sql);
    }

    public function matriculaExists($matricula) {
        $matricula = $this->conn->real_escape_string($matricula);
        $sql = "SELECT id FROM Usuarios WHERE matricula = '$matricula'";
        $result = $this->conn->query($sql);
        return $result->num_rows > 0;
    }
}

// Variável para armazenar mensagens de erro
$error_message = '';

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = new Usuarios($conn);

    // Verifica se a matrícula já existe
    if ($usuario->matriculaExists($_POST['matricula'])) {
        $error_message = "A matrícula já está em uso.";
    } else {
        // Cria o novo usuário
        if ($usuario->create($_POST)) {
            header('Location: login.html'); // Redireciona para a página de login
            exit();
        } else {
            $error_message = "Erro ao cadastrar o usuário.";
        }
    }
}

// Inclui a página de cadastro para exibir erros
include 'cadastro_usuario.html';
?>

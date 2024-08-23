<?php
require 'connect.inc.php';

class Usuarios
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function authenticate($matricula, $senha)
    {
        $matricula = $this->conn->real_escape_string($matricula);

        $sql = "SELECT id, nome, matricula, email, senha, tipoUsuario FROM Usuarios WHERE matricula = '$matricula'";
        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($senha, $row['senha'])) { // Verifica a senha
                return $row;
            }
        }
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = new Usuarios($conn);

    $matricula = $_POST['matricula'];
    $senha = $_POST['senha'];

    $user_data = $usuario->authenticate($matricula, $senha);

    if ($user_data) {
        session_start();
        $_SESSION['user_id'] = $user_data['id'];
        $_SESSION['username'] = $user_data['nome'];
        $_SESSION['user_type'] = $user_data['tipoUsuario'];

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => 'Logado com sucesso',
            'redirect' => $user_data['tipoUsuario'] == 'administrador' ? 'administradores_home.php' : 'home.php'
        ]);
        exit();
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'Matrícula ou senha inválidos.'
        ]);
        exit();
    }
}
?>
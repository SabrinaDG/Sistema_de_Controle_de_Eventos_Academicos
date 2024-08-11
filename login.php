<?php
require 'connect.inc.php'; // Inclui o arquivo de conexão ao banco de dados

class Usuarios {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function authenticate($matricula, $senha) {
        $matricula = $this->conn->real_escape_string($matricula);

        // Consulta para verificar se a matrícula e a senha estão corretas
        $sql = "SELECT id, nome, matricula, email, senha FROM Usuarios WHERE matricula = '$matricula'";
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

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = new Usuarios($conn);
    
    $matricula = $_POST['matricula'];
    $senha = $_POST['senha'];

    $user_data = $usuario->authenticate($matricula, $senha);

    if ($user_data) {
        // Inicia a sessão e armazena os dados do usuário
        session_start();
        $_SESSION['user_id'] = $user_data['id']; // Armazena o ID do usuário na sessão
        $_SESSION['username'] = $user_data['nome']; // Armazena o nome do usuário na sessão
    
        // Envia uma resposta JSON indicando sucesso
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => 'Logado com sucesso'
        ]);
        exit();
    }
     else {
        // Envia uma mensagem de erro em formato JSON
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'Matrícula ou senha inválidos.'
        ]);
        exit();
    }
}
?>

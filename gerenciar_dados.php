<?php
session_start();
require 'connect.inc.php';

class Usuarios
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getUserData($user_id)
    {
        $sql = "SELECT id, nome, email, matricula FROM Usuarios WHERE id = ?";
        $stm = $this->conn->prepare($sql);
        $stm->bind_param("i", $user_id);
        $stm->execute();
        $result = $stm->get_result();
        return $result->fetch_assoc();
    }

    public function updateUserData($user_id, $nome, $email, $senha = null)
    {
        if ($senha) {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "UPDATE Usuarios SET nome = ?, email = ?, senha = ? WHERE id = ?";
            $stm = $this->conn->prepare($sql);
            $stm->bind_param("sssi", $nome, $email, $senha_hash, $user_id);
        } else {
            $sql = "UPDATE Usuarios SET nome = ?, email = ? WHERE id = ?";
            $stm = $this->conn->prepare($sql);
            $stm->bind_param("ssi", $nome, $email, $user_id);
        }
        return $stm->execute();
    }

    public function deleteUser($user_id)
    {
        $sql = "DELETE FROM Usuarios WHERE id = ?";
        $stm = $this->conn->prepare($sql);
        $stm->bind_param("i", $user_id);
        return $stm->execute();
    }
}

if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

$usuario = new Usuarios($conn);
$user_id = $_SESSION['user_id'];
$user_data = $usuario->getUserData($user_id);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update'])) {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = !empty($_POST['senha']) ? $_POST['senha'] : null;

        if ($usuario->updateUserData($user_id, $nome, $email, $senha)) {
            echo "<script>
                alert('Dados atualizados com sucesso!');
                window.location.href = window.location.href; // Recarrega a página
            </script>";
        } else {
            echo "<script>alert('Erro ao atualizar dados.');</script>";
        }
    } elseif (isset($_POST['delete'])) {
        if ($usuario->deleteUser($user_id)) {
            session_destroy();
            header('Location: index.html');
            exit();
        } else {
            echo "<script>alert('Erro ao excluir conta.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Dados</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
        function habilitarEdicao() {
            document.getElementById('nome').disabled = false;
            document.getElementById('email').disabled = false;
            document.getElementById('senha').disabled = false;
            document.getElementById('updateBtn').style.display = 'block';
            document.getElementById('editarBtn').style.display = 'none';
        }
    </script>
</head>

<body>
    <div class="container fluid  mt-5">
        <a href="home.php" class="btn btn-secondary mb-3">Voltar para a Página Inicial</a>
        <h2>Gerenciar Dados</h2>
        <form method="post">
            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome"
                    value="<?php echo htmlspecialchars($user_data['nome']); ?>" disabled required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email"
                    value="<?php echo htmlspecialchars($user_data['email']); ?>" disabled required>
            </div>
            <div class="form-group">
                <label for="matricula">Matrícula</label>
                <input type="text" class="form-control" id="matricula" name="matricula"
                    value="<?php echo htmlspecialchars($user_data['matricula']); ?>" disabled>
            </div>
            <div class="form-group">
                <label for="senha">Nova Senha (deixe em branco para manter a senha atual)</label>
                <input type="password" class="form-control" id="senha" name="senha" disabled>
            </div>
            <div class="form-group d-flex justify-content-between">
                <button type="button" id="editarBtn" class="btn btn-primary" onclick="habilitarEdicao()">Editar
                    Dados</button>
                <button type="submit" name="update" id="updateBtn" class="btn btn-success"
                    style="display:none;">Atualizar Dados</button>
                <button type="submit" name="delete" class="btn btn-danger"
                    onclick="return confirm('Tem certeza que deseja excluir sua conta?')">Excluir Conta</button>
            </div>



        </form>
    </div>
</body>

</html>
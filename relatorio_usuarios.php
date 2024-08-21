<?php
session_start();
require 'connect.inc.php';

function format_datetime($datetime) {
    return date("d/m/Y H:i", strtotime($datetime));
}

// Consulta para obter todos os usuários
$sql_usuarios = "SELECT matricula, nome, email FROM usuarios";
$result_usuarios = $conn->query($sql_usuarios);
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <title>Relatório de Usuários</title>
</head>
<body>
<div class="container mt-5">
    <h2>Relatório de Usuários Cadastrados</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Matricula</th>
                <th>Nome</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_usuarios->num_rows > 0): ?>
                <?php while ($row = $result_usuarios->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['matricula']); ?></td>
                        <td><?php echo htmlspecialchars($row['nome']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">Nenhum usuário encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="administradores_home.php" class="btn btn-secondary">Voltar para a Página Inicial</a>
</div>

<script src="https://code.jquery.com/jquery-3.5.2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

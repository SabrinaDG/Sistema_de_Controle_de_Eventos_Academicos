<?php
session_start();
require 'connect.inc.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

function format_datetime($datetime) {
    return date("d/m/Y H:i", strtotime($datetime));
}

$usuario_id = $_SESSION['user_id']; // ID do usuário logado

// Consulta para obter as inscrições do usuário logado, cursos e eventos
$sql_relatorio = "
    SELECT c.titulo AS curso_titulo, e.titulo AS evento_titulo, 
           c.data_inicio AS curso_data_inicio, c.data_fim AS curso_data_fim
    FROM inscricoes i
    JOIN cursos c ON i.curso_id = c.id
    JOIN eventos e ON c.evento_id = e.id
    WHERE i.usuario_id = ?
    ORDER BY c.data_inicio
";

$stmt_relatorio = $conn->prepare($sql_relatorio);
$stmt_relatorio->bind_param("i", $usuario_id);
$stmt_relatorio->execute();
$resultado = $stmt_relatorio->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <title>Relatório de Inscrições</title>
</head>
<body>
<div class="container mt-5">
    <h2>Relatório de Inscrições</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Curso</th>
                <th>Evento</th>
                <th>Data Início</th>
                <th>Data Fim</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado->num_rows > 0): ?>
                <?php while ($row = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['curso_titulo']); ?></td>
                        <td><?php echo htmlspecialchars($row['evento_titulo']); ?></td>
                        <td><?php echo format_datetime(htmlspecialchars($row['curso_data_inicio'])); ?></td>
                        <td><?php echo format_datetime(htmlspecialchars($row['curso_data_fim'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Nenhuma inscrição encontrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="home.php" class="btn btn-secondary">Voltar para o Painel</a>
</div>

<script src="https://code.jquery.com/jquery-3.5.2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

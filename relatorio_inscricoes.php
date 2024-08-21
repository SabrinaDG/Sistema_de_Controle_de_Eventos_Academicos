<?php
session_start();
require 'connect.inc.php';

function format_datetime($datetime) {
    return date("d/m/Y H:i", strtotime($datetime));
}

// Consulta para obter as inscrições de todos os usuários
$sql_inscricoes = "
    SELECT u.nome AS usuario_nome, u.matricula AS usuario_matricula, 
           c.titulo AS curso_titulo, e.titulo AS evento_titulo, 
           c.data_inicio, c.data_fim
    FROM inscricoes i
    JOIN usuarios u ON i.usuario_id = u.id
    JOIN cursos c ON i.curso_id = c.id
    JOIN eventos e ON c.evento_id = e.id
";
$result_inscricoes = $conn->query($sql_inscricoes);
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
                <th>Matrícula</th>
                <th>Usuário</th>
                <th>Curso</th>
                <th>Evento</th>
                <th>Data Início</th>
                <th>Data Fim</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_inscricoes->num_rows > 0): ?>
                <?php while ($row = $result_inscricoes->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['usuario_matricula']); ?></td>
                        <td><?php echo htmlspecialchars($row['usuario_nome']); ?></td>
                        <td><?php echo htmlspecialchars($row['curso_titulo']); ?></td>
                        <td><?php echo htmlspecialchars($row['evento_titulo']); ?></td>
                        <td><?php echo format_datetime(htmlspecialchars($row['data_inicio'])); ?></td>
                        <td><?php echo format_datetime(htmlspecialchars($row['data_fim'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Nenhuma inscrição encontrada.</td>
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
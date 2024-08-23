<?php
session_start();
require 'connect.inc.php';

function format_datetime($datetime) {
    return date("d/m/Y H:i", strtotime($datetime));
}

// Definindo o número de resultados por página
$results_per_page = 10; // ajuste conforme necessário
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Consulta para obter o total de inscrições
$sql_count = "SELECT COUNT(*) AS total FROM inscricoes";
$result_count = $conn->query($sql_count);
$row_count = $result_count->fetch_assoc();
$total_results = $row_count['total'];
$total_pages = ceil($total_results / $results_per_page);

// Consulta para obter as inscrições de todos os usuários com limitação
$sql_inscricoes = "
    SELECT u.nome AS usuario_nome, u.matricula AS usuario_matricula, 
           c.titulo AS curso_titulo, e.titulo AS evento_titulo, 
           c.data_inicio, c.data_fim
    FROM inscricoes i
    JOIN usuarios u ON i.usuario_id = u.id
    JOIN cursos c ON i.curso_id = c.id
    JOIN eventos e ON c.evento_id = e.id
    LIMIT $start_from, $results_per_page
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
    
    <!-- Paginação -->
    <nav aria-label="Navegação de página">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Anterior">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Próximo">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
    
    <a href="administradores_home.php" class="btn btn-secondary">Voltar para a Página Inicial</a>
</div>

<script src="https://code.jquery.com/jquery-3.5.2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

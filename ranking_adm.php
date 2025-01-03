<?php
require 'connect.inc.php';

$limit = 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$sql_ranking = "
    SELECT u.nome, u.matricula, p.pontos 
    FROM usuarios u
    JOIN pontuacao p ON u.id = p.usuario_id
    ORDER BY p.pontos DESC, u.nome ASC
    LIMIT $limit OFFSET $offset
";

$result_ranking = $conn->query($sql_ranking);

$sql_count = "
    SELECT COUNT(DISTINCT u.id) AS total 
    FROM usuarios u
    JOIN pontuacao p ON u.id = p.usuario_id
";

$result_count = $conn->query($sql_count);
$total_rows = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit); 
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Ranking de Participação</title>
</head>

<body>
    <div class="container mt-5">
        <h2>Ranking de Participação dos Alunos</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Posição</th>
                    <th>Nome</th>
                    <th>Matrícula</th>
                    <th>Pontos</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_ranking->num_rows > 0): ?>
                    <?php $posicao = $offset + 1;  ?>
                    <?php while ($row = $result_ranking->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $posicao++; ?></td>
                            <td><?php echo htmlspecialchars($row['nome']); ?></td>
                            <td><?php echo htmlspecialchars($row['matricula']); ?></td>
                            <td><?php echo htmlspecialchars($row['pontos']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Nenhum aluno com pontuação registrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <nav aria-label="Page navigation">
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Anterior">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
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
</body>

</html>
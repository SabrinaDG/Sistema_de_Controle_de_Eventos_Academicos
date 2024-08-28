<?php
require 'connect.inc.php';

$registros_por_pagina = 5;

$pagina_atual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;

$offset = ($pagina_atual - 1) * $registros_por_pagina;

$sql_total = "
    SELECT COUNT(*) AS total
    FROM pontuacao p
    JOIN usuarios u ON p.usuario_id = u.id
";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_registros = $result_total->fetch_assoc()['total'];

$sql_ranking = "
    SELECT u.nome, u.matricula, p.pontos 
    FROM usuarios u
    JOIN pontuacao p ON u.id = p.usuario_id
    ORDER BY p.pontos DESC, u.nome ASC
    LIMIT ? OFFSET ?
";
$stmt_ranking = $conn->prepare($sql_ranking);
$stmt_ranking->bind_param("ii", $registros_por_pagina, $offset);
$stmt_ranking->execute();
$result_ranking = $stmt_ranking->get_result();

$total_paginas = ceil($total_registros / $registros_por_pagina);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <title>Ranking de Participação</title>
</head>

<body>
    <div class="container mt-5">
        <h2>Ranking de Participação Alunos</h2>
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

        <nav aria-label="Navegação de página">
            <ul class="pagination justify-content-center">
                <?php if ($pagina_atual > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?pagina=<?php echo $pagina_atual - 1; ?>" aria-label="Anterior">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?php if ($i == $pagina_atual)
                        echo 'active'; ?>">
                        <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($pagina_atual < $total_paginas): ?>
                    <li class="page-item">
                        <a class="page-link" href="?pagina=<?php echo $pagina_atual + 1; ?>" aria-label="Próxima">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

        <a href="home.php" class="btn btn-secondary">Voltar para a Página Inicial</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
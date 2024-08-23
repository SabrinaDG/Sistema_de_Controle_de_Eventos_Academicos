<?php
session_start();
require 'connect.inc.php';

function format_datetime($datetime) {
    return date("d/m/Y H:i", strtotime($datetime));
}

// Configurações de paginação
$limit = 5; // Número de registros por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Página atual
$offset = ($page - 1) * $limit; // Deslocamento para a consulta

// Consulta para obter todos os usuários com limite e deslocamento
$sql_usuarios = "SELECT matricula, nome, email FROM usuarios LIMIT $limit OFFSET $offset";
$result_usuarios = $conn->query($sql_usuarios);

// Consulta para contar o total de usuários
$sql_count = "SELECT COUNT(*) AS total FROM usuarios";
$result_count = $conn->query($sql_count);
$total_rows = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit); // Total de páginas
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
                <th>Matrícula</th>
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

    <!-- Controles de paginação -->
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

<script src="https://code.jquery.com/jquery-3.5.2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

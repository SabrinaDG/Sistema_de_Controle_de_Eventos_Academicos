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

// Definir o número de registros por página
$registros_por_pagina = 5;

// Verificar se o parâmetro de página foi definido na URL
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

// Calcular o offset
$offset = ($pagina_atual - 1) * $registros_por_pagina;

// Consulta para obter o total de inscrições do usuário logado
$sql_total = "
    SELECT COUNT(*) AS total
    FROM inscricoes i
    WHERE i.usuario_id = ?
";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param("i", $usuario_id);
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_registros = $result_total->fetch_assoc()['total'];

// Consulta para obter as inscrições do usuário logado com paginação
$sql_relatorio = "
    SELECT c.titulo AS curso_titulo, e.titulo AS evento_titulo, 
           c.data_inicio AS curso_data_inicio, c.data_fim AS curso_data_fim
    FROM inscricoes i
    JOIN cursos c ON i.curso_id = c.id
    JOIN eventos e ON c.evento_id = e.id
    WHERE i.usuario_id = ?
    ORDER BY c.data_inicio
    LIMIT ? OFFSET ?
";
$stmt_relatorio = $conn->prepare($sql_relatorio);
$stmt_relatorio->bind_param("iii", $usuario_id, $registros_por_pagina, $offset);
$stmt_relatorio->execute();
$resultado = $stmt_relatorio->get_result();

// Calcular o total de páginas
$total_paginas = ceil($total_registros / $registros_por_pagina);
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

    <!-- Paginação -->
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
                <li class="page-item <?php if ($i == $pagina_atual) echo 'active'; ?>">
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

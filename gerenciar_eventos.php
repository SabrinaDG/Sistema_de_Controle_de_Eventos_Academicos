<?php
session_start();
require 'connect.inc.php';

function saveEvent($conn, $id = null, $titulo, $descricao, $data_inicio, $data_fim)
{
    if ($id) {
        $sql = "UPDATE eventos SET titulo = ?, descricao = ?, data_inicio = ?, data_fim = ? WHERE id = ?";
        $stm = $conn->prepare($sql);
        $stm->bind_param("ssssi", $titulo, $descricao, $data_inicio, $data_fim, $id);
    } else {
        $sql = "INSERT INTO eventos (titulo, descricao, data_inicio, data_fim) VALUES (?, ?, ?, ?)";
        $stm = $conn->prepare($sql);
        $stm->bind_param("ssss", $titulo, $descricao, $data_inicio, $data_fim);
    }
    return $stm->execute();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];

    if (saveEvent($conn, $id, $titulo, $descricao, $data_inicio, $data_fim)) {
        echo "<script>alert('Evento salvo com sucesso!'); window.location.href='gerenciar_eventos.php';</script>";
    } else {
        echo "<script>alert('Erro ao salvar evento.');</script>";
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $sql_check_courses = "SELECT COUNT(*) as total FROM cursos WHERE evento_id = ?";
    $stm_check_courses = $conn->prepare($sql_check_courses);
    $stm_check_courses->bind_param("i", $id);
    $stm_check_courses->execute();
    $result = $stm_check_courses->get_result();
    $row = $result->fetch_assoc();

    if ($row['total'] > 0) {
        echo "<script>alert('PARA EXCLUIR ESTE EVENTO, EXCLUA PRIMEIRO OS CURSOS RELACIONADOS.'); window.location.href='gerenciar_eventos.php';</script>";
    } else {
        $sql_delete_event = "DELETE FROM eventos WHERE id = ?";
        $stm_delete_event = $conn->prepare($sql_delete_event);
        $stm_delete_event->bind_param("i", $id);
        if ($stm_delete_event->execute()) {
            echo "<script>alert('Evento excluído com sucesso!'); window.location.href='gerenciar_eventos.php';</script>";
        } else {
            echo "<script>alert('Erro ao excluir evento.');</script>";
        }
    }
}

function fetchEvents($conn, $limit, $offset)
{
    $sql = "SELECT * FROM eventos LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    return $stmt->get_result();
}

$total_events_query = "SELECT COUNT(*) as total FROM eventos";
$total_result = $conn->query($total_events_query);
$total_row = $total_result->fetch_assoc();
$total_events = $total_row['total'];

$limit = 5;
$total_pages = ceil($total_events / $limit);
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($current_page - 1) * $limit;

$events = fetchEvents($conn, $limit, $offset);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gerenciar Eventos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <a href="administradores_home.php" class="btn btn-secondary mb-3">Voltar para a Página Inicial</a>
        <h2>Gerenciar Eventos</h2>
        <form method="post">
            <div class="form-group">
                <label for="titulo">Título</label>
                <input type="text" class="form-control" id="titulo" name="titulo" required>
            </div>
            <div class="form-group">
                <label for="descricao">Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao"></textarea>
            </div>
            <div class="form-group">
                <label for="data_inicio">Data Início</label>
                <input type="datetime-local" class="form-control" id="data_inicio" name="data_inicio" required>
            </div>
            <div class="form-group">
                <label for="data_fim">Data Fim</label>
                <input type="datetime-local" class="form-control" id="data_fim" name="data_fim" required>
            </div>
            <input type="hidden" name="id" id="event_id">
            <button type="submit" class="btn btn-primary">Salvar Evento</button>
        </form>

        <h3 class="mt-5">Eventos Existentes</h3>
        <div id="eventos-container">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Título</th>
                        <th>Descrição</th>
                        <th>Data Início</th>
                        <th>Data Fim</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="eventos-tbody">
                    <?php while ($row = $events->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['titulo']) ?></td>
                            <td><?= htmlspecialchars($row['descricao']) ?></td>
                            <td><?= htmlspecialchars($row['data_inicio']) ?></td>
                            <td><?= htmlspecialchars($row['data_fim']) ?></td>
                            <td>
                                <a href="gerenciar_eventos.php?delete=<?= htmlspecialchars($row['id']) ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                                <button class="btn btn-info btn-sm"
                                    onclick="editEvent(<?= htmlspecialchars($row['id']) ?>, '<?= htmlspecialchars($row['titulo']) ?>', '<?= htmlspecialchars($row['descricao']) ?>', '<?= htmlspecialchars($row['data_inicio']) ?>', '<?= htmlspecialchars($row['data_fim']) ?>')">Editar</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li class="page-item <?= $current_page == 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $current_page - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $current_page == $i ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $current_page == $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $current_page + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <script>
        function editEvent(id, titulo, descricao, data_inicio, data_fim) {
            document.getElementById('event_id').value = id;
            document.getElementById('titulo').value = titulo;
            document.getElementById('descricao').value = descricao;
            document.getElementById('data_inicio').value = data_inicio;
            document.getElementById('data_fim').value = data_fim;
        }

    </script>
</body>

</html>
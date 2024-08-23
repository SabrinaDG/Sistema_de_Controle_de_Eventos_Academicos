<?php
session_start();
require 'connect.inc.php';

// Função para verificar se as datas do curso estão dentro do intervalo do evento
function validateCourseDates($conn, $evento_id, $data_inicio, $data_fim)
{
    $sql = "SELECT data_inicio, data_fim FROM eventos WHERE id = ?";
    $stm = $conn->prepare($sql);
    $stm->bind_param("i", $evento_id);
    $stm->execute();
    $result = $stm->get_result();
    $evento = $result->fetch_assoc();

    if ($evento) {
        $data_inicio_evento = $evento['data_inicio'];
        $data_fim_evento = $evento['data_fim'];

        // Formata as datas para datetime-local
        $data_inicio_evento_formatted = format_for_datetime_local($data_inicio_evento);
        $data_fim_evento_formatted = format_for_datetime_local($data_fim_evento);

        // Verifica se as datas do curso estão dentro do intervalo do evento
        if ($data_inicio < $data_inicio_evento || $data_fim > $data_fim_evento) {
            return array(
                'valid' => false,
                'inicio_evento' => $data_inicio_evento_formatted,
                'fim_evento' => $data_fim_evento_formatted
            );
        }
    }
    return array('valid' => true);
}

function format_for_datetime_local($datetime)
{
    $dateTime = new DateTime($datetime);
    return $dateTime->format('d/m/Y H:i');
}

// Função para adicionar ou atualizar curso
function saveCourse($conn, $id = null, $titulo, $descricao, $data_inicio, $data_fim, $evento_id)
{
    $validation = validateCourseDates($conn, $evento_id, $data_inicio, $data_fim);

    if (!$validation['valid']) {
        $inicio_evento = $validation['inicio_evento'];
        $fim_evento = $validation['fim_evento'];
        echo "<script>
            alert('As datas do curso não estão dentro do intervalo do evento selecionado.\\nData de início do evento: $inicio_evento\\nData de fim do evento: $fim_evento');
        </script>";
        return false;
    }

    if ($id) {
        $sql = "UPDATE cursos SET titulo = ?, descricao = ?, data_inicio = ?, data_fim = ?, evento_id = ? WHERE id = ?";
        $stm = $conn->prepare($sql);
        $stm->bind_param("ssssii", $titulo, $descricao, $data_inicio, $data_fim, $evento_id, $id);
    } else {
        $sql = "INSERT INTO cursos (titulo, descricao, data_inicio, data_fim, evento_id) VALUES (?, ?, ?, ?, ?)";
        $stm = $conn->prepare($sql);
        $stm->bind_param("ssssi", $titulo, $descricao, $data_inicio, $data_fim, $evento_id);
    }
    return $stm->execute();
}

// Adiciona ou atualiza curso
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
    $evento_id = $_POST['evento_id'];

    if (saveCourse($conn, $id, $titulo, $descricao, $data_inicio, $data_fim, $evento_id)) {
        echo "<script>alert('Curso salvo com sucesso!'); window.location.href='gerenciar_cursos.php';</script>";
    } else {
        echo "<script>alert('Erro ao salvar curso.');</script>";
    }
}

// Exclui curso
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM cursos WHERE id = ?";
    $stm = $conn->prepare($sql);
    $stm->bind_param("i", $id);
    if ($stm->execute()) {
        echo "<script>alert('Curso excluído com sucesso!'); window.location.href='gerenciar_cursos.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir curso.');</script>";
    }
}

// Paginação
$limit = 5; // Número de cursos por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Busca cursos e eventos para seleção
$sql = "SELECT * FROM cursos LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$courses = $stmt->get_result();

$sql = "SELECT COUNT(*) as total FROM cursos";
$result = $conn->query($sql);
$total_courses = $result->fetch_assoc()['total'];
$total_pages = ceil($total_courses / $limit);

$sql = "SELECT * FROM eventos";
$events = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gerenciar Cursos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <a href="administradores_home.php" class="btn btn-secondary mb-3">Voltar para a Página Inicial</a>
        <h2>Gerenciar Cursos</h2>
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
                <label for="evento_id">Evento</label>
                <select class="form-control" id="evento_id" name="evento_id" required>
                    <option value="">Selecione um evento</option>
                    <?php while ($row = $events->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($row['id']); ?>">
                            <?php echo htmlspecialchars($row['titulo']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="data_inicio">Data Início</label>
                <input type="datetime-local" class="form-control" id="data_inicio" name="data_inicio" required>
            </div>
            <div class="form-group">
                <label for="data_fim">Data Fim</label>
                <input type="datetime-local" class="form-control" id="data_fim" name="data_fim" required>
            </div>
            <input type="hidden" name="id" id="course_id">
            <button type="submit" class="btn btn-primary">Salvar Curso</button>
        </form>
        <h3 class="mt-5">Cursos Existentes</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Título</th>
                    <th>Descrição</th>
                    <th>Data Início</th>
                    <th>Data Fim</th>
                    <th>Evento</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $courses->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['titulo']); ?></td>
                        <td><?php echo htmlspecialchars($row['descricao']); ?></td>
                        <td><?php echo format_for_datetime_local($row['data_inicio']); ?></td>
                        <td><?php echo format_for_datetime_local($row['data_fim']); ?></td>

                        <td>
                            <?php
                            $evento_id = $row['evento_id'];
                            $sql = "SELECT titulo FROM eventos WHERE id = ?";
                            $stm = $conn->prepare($sql);
                            $stm->bind_param("i", $evento_id);
                            $stm->execute();
                            $result = $stm->get_result();
                            $evento = $result->fetch_assoc();
                            echo htmlspecialchars($evento['titulo']);
                            ?>
                        </td>
                        <td>
                            <a href="gerenciar_cursos.php?delete=<?php echo htmlspecialchars($row['id']); ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                            <button class="btn btn-info btn-sm"
                                onclick="editCourse(<?php echo htmlspecialchars($row['id']); ?>, '<?php echo htmlspecialchars($row['titulo']); ?>', '<?php echo htmlspecialchars($row['descricao']); ?>', '<?php echo htmlspecialchars($row['data_inicio']); ?>', '<?php echo htmlspecialchars($row['data_fim']); ?>', <?php echo htmlspecialchars($row['evento_id']); ?>)">Editar</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <!-- Navegação de paginação -->
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php if ($page >= $total_pages) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <script>
        function editCourse(id, titulo, descricao, data_inicio, data_fim, evento_id) {
            document.getElementById('course_id').value = id;
            document.getElementById('titulo').value = titulo;
            document.getElementById('descricao').value = descricao;
            document.getElementById('data_inicio').value = data_inicio;
            document.getElementById('data_fim').value = data_fim;
            document.getElementById('evento_id').value = evento_id;
        }
    </script>
</body>

</html>
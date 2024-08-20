<?php
session_start();
require 'connect.inc.php';

// Função para adicionar ou atualizar evento
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

// Adiciona ou atualiza evento
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

// Exclui evento
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Verificar se há cursos associados ao evento
    $sql_check_courses = "SELECT COUNT(*) as total FROM cursos WHERE evento_id = ?";
    $stm_check_courses = $conn->prepare($sql_check_courses);
    $stm_check_courses->bind_param("i", $id);
    $stm_check_courses->execute();
    $result = $stm_check_courses->get_result();
    $row = $result->fetch_assoc();

    if ($row['total'] > 0) {
        // Existem cursos associados ao evento
        echo "<script>alert('PARA EXCLUIR ESTE EVENTO, EXCLUA PRIMEIRO OS CURSOS RELACIONADOS.'); window.location.href='gerenciar_eventos.php';</script>";
    } else {
        // Não existem cursos associados, pode excluir o evento
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


// Busca eventos
$sql = "SELECT * FROM eventos";
$events = $conn->query($sql);
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
            <tbody>
                <?php while ($row = $events->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['titulo']); ?></td>
                        <td><?php echo htmlspecialchars($row['descricao']); ?></td>
                        <td><?php echo htmlspecialchars($row['data_inicio']); ?></td>
                        <td><?php echo htmlspecialchars($row['data_fim']); ?></td>
                        <td>
                            <a href="gerenciar_eventos.php?delete=<?php echo htmlspecialchars($row['id']); ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                            <button class="btn btn-info btn-sm"
                                onclick="editEvent(<?php echo htmlspecialchars($row['id']); ?>, '<?php echo htmlspecialchars($row['titulo']); ?>', '<?php echo htmlspecialchars($row['descricao']); ?>', '<?php echo htmlspecialchars($row['data_inicio']); ?>', '<?php echo htmlspecialchars($row['data_fim']); ?>')">Editar</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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
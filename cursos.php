<?php
session_start();
require 'connect.inc.php';

function format_datetime($datetime) {
    try {
        $dateTime = new DateTime($datetime); 
        return $dateTime->format('d/m/Y H:i');
    } catch (Exception $e) {
        return 'Data inválida';
    }
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html'); 
    exit();
}

if (!isset($_GET['evento_id'])) {
    echo "ID do evento não fornecido.";
    exit();
}

$evento_id = intval($_GET['evento_id']);

$sql_evento = "SELECT * FROM eventos WHERE id = ?";
$stmt_evento = $conn->prepare($sql_evento);
$stmt_evento->bind_param("i", $evento_id);
$stmt_evento->execute();
$evento = $stmt_evento->get_result()->fetch_assoc();

if (!$evento) {
    echo "Evento não encontrado.";
    exit();
}

$sql_cursos = "SELECT * FROM cursos WHERE evento_id = ?";
$stmt_cursos = $conn->prepare($sql_cursos);
$stmt_cursos->bind_param("i", $evento_id);
$stmt_cursos->execute();
$cursos = $stmt_cursos->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <title>Cursos do Evento</title>
</head>
<body>
<div class="container mt-5">
    <a href="home.php" class="btn btn-secondary mb-3">Voltar para a Página Principal</a>
    <h2>Cursos do Evento: <?php echo htmlspecialchars($evento['titulo']); ?></h2>
    <p><strong>Descrição:</strong> <?php echo htmlspecialchars($evento['descricao']); ?></p>
    <p><strong>Data Início:</strong> <?php echo format_datetime(htmlspecialchars($evento['data_inicio'])); ?></p>
    <p><strong>Data Fim:</strong> <?php echo format_datetime(htmlspecialchars($evento['data_fim'])); ?></p>

    <h3 class="mt-5">Cursos Disponíveis</h3>
    <?php if (empty($cursos)): ?>
        <p>Nenhum curso disponível para este evento.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($cursos as $curso): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($curso['titulo']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($curso['descricao']); ?></p>
                            <p><strong>Data Início:</strong> <?php echo format_datetime(htmlspecialchars($curso['data_inicio'])); ?></p>
                            <p><strong>Data Fim:</strong> <?php echo format_datetime(htmlspecialchars($curso['data_fim'])); ?></p>
                            <a href="inscricao.php?curso_id=<?php echo htmlspecialchars($curso['id']); ?>" class="btn btn-primary">Inscrever-se</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Optional JavaScript -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

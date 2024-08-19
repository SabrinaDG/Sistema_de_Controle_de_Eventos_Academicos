<?php
session_start();
require 'connect.inc.php'; // Inclua o arquivo de conexão

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html'); // Redireciona para a página de login se não estiver logado
    exit();
}

// Exibe o nome do usuário logado
$username = $_SESSION['username'];

// Busca eventos
$sql_events = "SELECT * FROM eventos";
$events_result = $conn->query($sql_events);

// Busca cursos
$sql_courses = "SELECT * FROM cursos";
$courses_result = $conn->query($sql_courses);

// Função para formatar a data e hora no formato dd/mm/aaaa hh:mm
function format_datetime($datetime) {
    $dateTime = new DateTime($datetime);
    return $dateTime->format('d/m/Y H:i');
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />

    <title>Página Principal</title>
</head>
<body>
<div id="content" class="container mt-5">

    <!-- Topbar -->
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
        <!-- Sidebar Toggle (Topbar) -->
        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
        </button>

        <!-- Topbar Navbar -->
        <ul class="navbar-nav ml-auto">
            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($username); ?></span>
                </a>
                <!-- Dropdown - User Information -->
                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                     aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="gerenciar_dados_adm.php">
                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                        Gerenciar dados - Administrador
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="logout.php">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Sair
                    </a>
                </div>
            </li>
        </ul>
    </nav>
    <!-- End of Topbar -->

    <!-- Conteúdo da página -->
    <h1>Bem-vindo, administrador <?php echo htmlspecialchars($username); ?>!</h1>

    <!-- Botões para Gerenciar Eventos e Cursos -->
    <div class="mt-4">
        <a href="gerenciar_eventos.php" class="btn btn-primary mr-2">Gerenciar Eventos</a>
        <a href="gerenciar_cursos.php" class="btn btn-secondary">Gerenciar Cursos</a>
    </div>

    <!-- Tabela de Eventos -->
    <h2 class="mt-5">Eventos</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Título</th>
                <th>Descrição</th>
                <th>Data Início</th>
                <th>Data Fim</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $events_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['titulo']); ?></td>
                    <td><?php echo htmlspecialchars($row['descricao']); ?></td>
                    <td><?php echo format_datetime($row['data_inicio']); ?></td>
                    <td><?php echo format_datetime($row['data_fim']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Tabela de Cursos -->
    <h2 class="mt-5">Cursos</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Título</th>
                <th>Descrição</th>
                <th>Data Início</th>
                <th>Data Fim</th>
                <th>Evento</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $courses_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['titulo']); ?></td>
                    <td><?php echo htmlspecialchars($row['descricao']); ?></td>
                    <td><?php echo format_datetime($row['data_inicio']); ?></td>
                    <td><?php echo format_datetime($row['data_fim']); ?></td>
                    <td>
                        <?php
                        // Buscar título do evento associado
                        $evento_id = $row['evento_id'];
                        $sql_event = "SELECT titulo FROM eventos WHERE id = ?";
                        $stm = $conn->prepare($sql_event);
                        $stm->bind_param("i", $evento_id);
                        $stm->execute();
                        $result_event = $stm->get_result();
                        $evento = $result_event->fetch_assoc();
                        echo htmlspecialchars($evento['titulo']);
                        ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html'); // Redireciona para a página de login se não estiver logado
    exit();
}

// Conexão com o banco de dados
require 'connect.inc.php';

// Exibe o nome do usuário logado
$username = $_SESSION['username'];

// Verifica se um evento foi selecionado para exibir os cursos associados
$evento_id = isset($_GET['evento_id']) ? intval($_GET['evento_id']) : null;

if ($evento_id) {
    // Recupera os cursos associados ao evento selecionado
    $sql_cursos = "SELECT * FROM cursos WHERE evento_id = ?";
    $stmt_cursos = $conn->prepare($sql_cursos);
    $stmt_cursos->bind_param("i", $evento_id);
    $stmt_cursos->execute();
    $cursos = $stmt_cursos->get_result();
} else {
    // Recupera todos os eventos
    $sql_eventos = "SELECT * FROM eventos";
    $eventos = $conn->query($sql_eventos);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
  <title>Página Principal</title>
</head>
<body>
<div id="content">

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
                <a class="dropdown-item" href="gerenciar_dados.php">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Gerenciar dados - Usuário
                </a>
                <a class="dropdown-item" href="sobre.html">
                    <i class="fas fa-info-circle fa-sm fa-fw mr-2 text-gray-400"></i>
                    Sobre
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

<div class="container mt-4">
    <h1>Bem-vindo, <?php echo htmlspecialchars($username); ?>!</h1>

    <?php if ($evento_id): ?>
        <!-- Exibe os cursos associados ao evento -->
        <h2>Cursos do Evento</h2>
        <?php if ($cursos->num_rows > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Data Início</th>
                        <th>Data Fim</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($curso = $cursos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($curso['id']); ?></td>
                            <td><?php echo htmlspecialchars($curso['nome']); ?></td>
                            <td><?php echo htmlspecialchars($curso['descricao']); ?></td>
                            <td><?php echo htmlspecialchars($curso['data_inicio']); ?></td>
                            <td><?php echo htmlspecialchars($curso['data_fim']); ?></td>
                            <td>
                                <a href="inscricao.php?curso_id=<?php echo htmlspecialchars($curso['id']); ?>" class="btn btn-primary btn-sm">Inscrever-se</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum curso disponível para este evento.</p>
        <?php endif; ?>
    <?php else: ?>
        <!-- Exibe a lista de eventos disponíveis -->
        <h2>Eventos Disponíveis</h2>
        <div class="row">
            <?php while ($evento = $eventos->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($evento['titulo']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($evento['descricao']); ?></p>
                            <a href="cursos.php?evento_id=<?php echo htmlspecialchars($evento['id']); ?>" class="btn btn-info">Ver Cursos</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Optional JavaScript -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

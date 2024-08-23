<?php
session_start();
require 'connect.inc.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.html');
  exit();
}

$username = $_SESSION['username'];

$limit = 10;

$page_eventos = isset($_GET['page_eventos']) ? (int) $_GET['page_eventos'] : 1;
$offset_eventos = ($page_eventos - 1) * $limit;

$page_cursos = isset($_GET['page_cursos']) ? (int) $_GET['page_cursos'] : 1;
$offset_cursos = ($page_cursos - 1) * $limit;

$sql_events = "SELECT * FROM eventos LIMIT $limit OFFSET $offset_eventos";
$events_result = $conn->query($sql_events);

$sql_count_events = "SELECT COUNT(*) AS total FROM eventos";
$total_events_result = $conn->query($sql_count_events);
$total_events = $total_events_result->fetch_assoc()['total'];
$total_pages_events = ceil($total_events / $limit);

$sql_courses = "SELECT * FROM cursos LIMIT $limit OFFSET $offset_cursos";
$courses_result = $conn->query($sql_courses);

$sql_count_courses = "SELECT COUNT(*) AS total FROM cursos";
$total_courses_result = $conn->query($sql_count_courses);
$total_courses = $total_courses_result->fetch_assoc()['total'];
$total_pages_courses = ceil($total_courses / $limit);

function format_datetime($datetime)
{
  $dateTime = new DateTime($datetime);
  return $dateTime->format('d/m/Y H:i');
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

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
          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
            <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($username); ?></span>
          </a>
          <!-- Dropdown - User Information -->
          <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
            <a class="dropdown-item" href="gerenciar_dados_adm.php">
              <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
              Gerenciar dados - Administrador
            </a>
            <a class="dropdown-item" href="relatorio_usuarios.php">
              <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
              Relatório de Usuários Cadastrados
            </a>
            <a class="dropdown-item" href="relatorio_cursos.php">
              <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
              Relatório de Cursos e Eventos
            </a>
            <a class="dropdown-item" href="relatorio_inscricoes.php">
              <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
              Relatório de Inscrições
            </a>
            <a class="dropdown-item" href="ranking_adm.php">
              <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
              Ranking de usuários
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

    <div class="mt-4">
      <a href="gerenciar_eventos.php" class="btn btn-primary mr-2">Gerenciar Eventos</a>
      <a href="gerenciar_cursos.php" class="btn btn-secondary">Gerenciar Cursos</a>
    </div>

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

    <nav aria-label="Page navigation for events">
      <ul class="pagination">
        <li class="page-item <?php if ($page_eventos <= 1)
          echo 'disabled'; ?>">
          <a class="page-link" href="<?php if ($page_eventos > 1) {
            echo "?page_eventos=" . ($page_eventos - 1) . "&page_cursos=$page_cursos";
          } ?>" aria-label="Previous">
            <span aria-hidden="true">&laquo;</span>
          </a>
        </li>
        <?php for ($i = 1; $i <= $total_pages_events; $i++): ?>
          <li class="page-item <?php if ($i == $page_eventos)
            echo 'active'; ?>">
            <a class="page-link"
              href="?page_eventos=<?php echo $i; ?>&page_cursos=<?php echo $page_cursos; ?>"><?php echo $i; ?></a>
          </li>
        <?php endfor; ?>
        <li class="page-item <?php if ($page_eventos >= $total_pages_events)
          echo 'disabled'; ?>">
          <a class="page-link" href="<?php if ($page_eventos < $total_pages_events) {
            echo "?page_eventos=" . ($page_eventos + 1) . "&page_cursos=$page_cursos";
          } ?>" aria-label="Next">
            <span aria-hidden="true">&raquo;</span>
          </a>
        </li>
      </ul>
    </nav>

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

    <nav aria-label="Page navigation for courses">
      <ul class="pagination">
        <li class="page-item <?php if ($page_cursos <= 1)
          echo 'disabled'; ?>">
          <a class="page-link" href="<?php if ($page_cursos > 1) {
            echo "?page_cursos=" . ($page_cursos - 1) . "&page_eventos=$page_eventos";
          } ?>" aria-label="Previous">
            <span aria-hidden="true">&laquo;</span>
          </a>
        </li>
        <?php for ($i = 1; $i <= $total_pages_courses; $i++): ?>
          <li class="page-item <?php if ($i == $page_cursos)
            echo 'active'; ?>">
            <a class="page-link"
              href="?page_cursos=<?php echo $i; ?>&page_eventos=<?php echo $page_eventos; ?>"><?php echo $i; ?></a>
          </li>
        <?php endfor; ?>
        <li class="page-item <?php if ($page_cursos >= $total_pages_courses)
          echo 'disabled'; ?>">
          <a class="page-link" href="<?php if ($page_cursos < $total_pages_courses) {
            echo "?page_cursos=" . ($page_cursos + 1) . "&page_eventos=$page_eventos";
          } ?>" aria-label="Next">
            <span aria-hidden="true">&raquo;</span>
          </a>
        </li>
      </ul>
    </nav>
  </div>

  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
<?php
session_start();
require 'connect.inc.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

// Verifica se o ID do curso foi fornecido
if (!isset($_GET['curso_id'])) {
    echo "ID do curso não fornecido.";
    exit();
}

function format_datetime($datetime) {
    return date('d/m/Y H:i', strtotime($datetime));
}

$curso_id = intval($_GET['curso_id']);
$usuario_id = $_SESSION['user_id'];

// Verifica se o usuário já está inscrito no curso
$sql_verifica_inscricao = "
    SELECT id 
    FROM inscricoes 
    WHERE usuario_id = ? AND curso_id = ?
";
$stmt_verifica_inscricao = $conn->prepare($sql_verifica_inscricao);
$stmt_verifica_inscricao->bind_param("ii", $usuario_id, $curso_id);
$stmt_verifica_inscricao->execute();
$stmt_verifica_inscricao->store_result();

if ($stmt_verifica_inscricao->num_rows > 0) {
    // Exibe a mensagem de que já está inscrito no curso
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
        <title>Erro de Inscrição</title>
    </head>
    <body>
        <div class="container mt-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Atenção!</h5>
                    <p class="card-text">Você já está inscrito no curso.</p>
                    <a href="home.php" class="btn btn-primary">Voltar para a Página Inicial</a>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Verifica se o usuário já está inscrito em um curso no mesmo horário
$sql_verifica_horario = "
    SELECT c.id 
    FROM inscricoes i
    JOIN cursos c ON i.curso_id = c.id
    WHERE i.usuario_id = ? 
    AND (
        (c.data_inicio < (SELECT data_fim FROM cursos WHERE id = ?)) 
        AND (c.data_fim > (SELECT data_inicio FROM cursos WHERE id = ?))
    )
";
$stmt_verifica_horario = $conn->prepare($sql_verifica_horario);
$stmt_verifica_horario->bind_param("iii", $usuario_id, $curso_id, $curso_id);
$stmt_verifica_horario->execute();
$stmt_verifica_horario->store_result();

if ($stmt_verifica_horario->num_rows > 0) {
    // Exibe a mensagem de erro de inscrição
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
        <title>Erro de Inscrição</title>
    </head>
    <body>
        <div class="container mt-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Atenção!</h5>
                    <p class="card-text">Você já está inscrito em um curso que ocorre no mesmo horário.</p>
                    <a href="home.php" class="btn btn-primary">Voltar para a Página Inicial</a>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Insere a inscrição no banco de dados
$sql_inscricao = "INSERT INTO inscricoes (usuario_id, curso_id) VALUES (?, ?)";
$stmt_inscricao = $conn->prepare($sql_inscricao);
$stmt_inscricao->bind_param("ii", $usuario_id, $curso_id);

if ($stmt_inscricao->execute()) {
    // Busca os dados do curso para exibir na confirmação
    $sql_curso = "SELECT titulo, data_inicio, data_fim FROM cursos WHERE id = ?";
    $stmt_curso = $conn->prepare($sql_curso);
    $stmt_curso->bind_param("i", $curso_id);
    $stmt_curso->execute();
    $curso = $stmt_curso->get_result()->fetch_assoc();
    
    // Atribui 5 pontos ao usuário
    $pontos = 5;

    // Verifica se o usuário já possui pontos registrados
    $sql_check_pontuacao = "SELECT id FROM pontuacao WHERE usuario_id = ?";
    $stmt_check_pontuacao = $conn->prepare($sql_check_pontuacao);
    $stmt_check_pontuacao->bind_param("i", $usuario_id);
    $stmt_check_pontuacao->execute();
    $stmt_check_pontuacao->store_result();

    if ($stmt_check_pontuacao->num_rows > 0) {
        // Se o usuário já tem pontos, atualiza os pontos existentes
        $sql_update_pontos = "UPDATE pontuacao SET pontos = pontos + ? WHERE usuario_id = ?";
        $stmt_update_pontos = $conn->prepare($sql_update_pontos);
        $stmt_update_pontos->bind_param("ii", $pontos, $usuario_id);
        $stmt_update_pontos->execute();
    } else {
        // Se o usuário não tem pontos, insere uma nova entrada
        $sql_insert_pontos = "INSERT INTO pontuacao (usuario_id, pontos) VALUES (?, ?)";
        $stmt_insert_pontos = $conn->prepare($sql_insert_pontos);
        $stmt_insert_pontos->bind_param("ii", $usuario_id, $pontos);
        $stmt_insert_pontos->execute();
    }

    // Exibe a mensagem de confirmação
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
        <title>Confirmação de Inscrição</title>
    </head>
    <body>
        <div class="container mt-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Parabéns!</h5>
                    <p class="card-text">Você está inscrito no curso <strong><?php echo htmlspecialchars($curso['titulo']); ?></strong>, que acontece no dia <strong><?php echo format_datetime(htmlspecialchars($curso['data_inicio'])); ?></strong>.</p>
                    <a href="home.php" class="btn btn-primary">Voltar para a Página Inicial</a>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
} else {
    echo "Erro ao se inscrever no curso.";
}
?>

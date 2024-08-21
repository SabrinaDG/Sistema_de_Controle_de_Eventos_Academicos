<?php
require 'connect.inc.php';

// Consulta para obter o ranking dos alunos
$sql_ranking = "
    SELECT u.nome, u.matricula, p.pontos 
    FROM usuarios u
    JOIN pontuacao p ON u.id = p.usuario_id
    ORDER BY p.pontos DESC, u.nome ASC
";

$result_ranking = $conn->query($sql_ranking);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <title>Ranking de Participação</title>
</head>
<body>
<div class="container mt-5">
    <h2>Ranking de Participação Alunos</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Posição</th>
                <th>Nome</th>
                <th>Matrícula</th>
                <th>Pontos</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_ranking->num_rows > 0): ?>
                <?php $posicao = 1; ?>
                <?php while ($row = $result_ranking->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $posicao++; ?></td>
                        <td><?php echo htmlspecialchars($row['nome']); ?></td>
                        <td><?php echo htmlspecialchars($row['matricula']); ?></td>
                        <td><?php echo htmlspecialchars($row['pontos']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Nenhum aluno com pontuação registrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="home.php" class="btn btn-secondary">Voltar para a Página Inicial</a>
</div>
</body>
</html>

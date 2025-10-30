<?php
include 'db.php';

$sql = "UPDATE jogos SET preco = 59.99 WHERE preco IS NULL OR preco = 0";

if (mysqli_query($conn, $sql)) {
    $num_rows = mysqli_affected_rows($conn);
    $status = 'success';
    $message = "{$num_rows} jogos foram atualizados com o preço padrão de R$ 59,99.";
} else {
    $status = 'danger';
    $message = "Ocorreu um erro ao atualizar os preços: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Atualizar Preços - Fase Bônus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Fase Bônus</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Início</a></li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="estoque.php">Estoque</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container content py-5">
    <div class="card">
        <div class="card-body text-center">
            <h2 class="mb-3">Atualização de Preços</h2>
            <div class="alert alert-<?php echo htmlspecialchars($status); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <p>As advertências na página inicial devem ter desaparecido.</p>
            <p><strong>Importante:</strong> Depois de confirmar os preços, você pode remover este arquivo do projeto, se desejar.</p>
            <a href="index.php" class="btn btn-info text-dark fw-bold">Voltar para a página inicial</a>
        </div>
    </div>
</div>

<footer class="bg-dark text-white text-center p-3 mt-5">
    <p>&copy; <?php echo date('Y'); ?> Ber. Todos os direitos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

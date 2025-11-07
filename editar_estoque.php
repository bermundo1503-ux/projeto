<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: estoque.php");
    exit;
}

// Se o formulário for enviado, atualize a quantidade
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantidade = $_POST['quantidade'];
    $stmt = mysqli_prepare($conn, "UPDATE jogos SET quantidade = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $quantidade, $id);
    mysqli_stmt_execute($stmt);
    header("Location: estoque.php");
    exit;
}

// Pega as informações do jogo
$stmt = mysqli_prepare($conn, "SELECT id, titulo, quantidade FROM jogos WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$jogo = mysqli_fetch_assoc($result);

if (!$jogo) {
    echo "Jogo não encontrado.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Editar Estoque</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow neon-navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php">🎮 Fase Bônus</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav mx-auto text-center">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Início</a></li>
                    <li class="nav-item"><a class="nav-link" href="create.php">Adicionar Jogo</a></li>
                    <li class="nav-item"><a class="nav-link" href="estoque.php">Estoque</a></li>
                    <li class="nav-item"><a class="nav-link" href="usuarios.php">Usuários</a></li>
                </ul>

                <ul class="navbar-nav ms-auto text-center">
                    <li class="nav-item">
                        <a class="nav-link" href="carrinho.php">Carrinho
                            <span class="badge bg-info text-dark">
                                <?php echo isset($_SESSION['carrinho']) ? count($_SESSION['carrinho']) : 0; ?>
                            </span>
                        </a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container content py-5">
        <h2>Editar quantidade para: <?= htmlspecialchars($jogo['titulo']) ?></h2>
        <form method="POST" action="editar_estoque.php?id=<?= $jogo['id'] ?>" class="mt-3">
            <div class="mb-3">
                <label for="quantidade" class="form-label">Quantidade</label>
                <input type="number" id="quantidade" name="quantidade" value="<?= $jogo['quantidade'] ?>" required min="0" class="form-control input-compact">
            </div>
            <button type="submit" class="btn btn-primary">Atualizar Estoque</button>
            <a href="estoque.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <footer class="bg-dark text-white text-center p-3 mt-5">
        <p>&copy; 2025 Ber. Todos os direitos reservados.</p>
    </footer>
</body>

</html>
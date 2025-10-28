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
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <h1>Editar Estoque</h1>
        <nav>
            <ul>
                <li><a href="index.php">Início</a></li>
                <li><a href="create.php">Adicionar Jogo</a></li>
                <li><a href="estoque.php">Estoque</a></li>
                <li><a href="carrinho.php">Carrinho</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <section class="hero">
        <h2>Editar quantidade para: <?= htmlspecialchars($jogo['titulo']) ?></h2>
    </section>

    <div class="container">
        <form method="POST" action="editar_estoque.php?id=<?= $jogo['id'] ?>">
            <input type="number" name="quantidade" value="<?= $jogo['quantidade'] ?>" required min="0">
            <button type="submit">Atualizar Estoque</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2025 Ber. Todos os direitos reservados.</p>
    </footer>
</body>
</html>

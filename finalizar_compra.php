<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    header("Location: carrinho.php");
    exit();
}

$carrinho = $_SESSION['carrinho'];

// Inicia uma transação
mysqli_begin_transaction($conn);

try {
    foreach ($carrinho as $jogo_id => $quantidade) {
        // Pega o estoque atual para garantir que ainda há o suficiente
        $stmt = mysqli_prepare($conn, "SELECT quantidade FROM jogos WHERE id = ? FOR UPDATE");
        mysqli_stmt_bind_param($stmt, "i", $jogo_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $jogo = mysqli_fetch_assoc($result);

        if (!$jogo || $jogo['quantidade'] < $quantidade) {
            // Se não houver estoque, reverte a transação
            throw new Exception("Estoque insuficiente para o jogo ID: $jogo_id");
        }

        // Atualiza o estoque
        $novoEstoque = $jogo['quantidade'] - $quantidade;
        $stmtUpdate = mysqli_prepare($conn, "UPDATE jogos SET quantidade = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmtUpdate, "ii", $novoEstoque, $jogo_id);
        mysqli_stmt_execute($stmtUpdate);
    }

    // Se tudo correu bem, confirma a transação
    mysqli_commit($conn);

    // Limpa o carrinho
    $_SESSION['carrinho'] = [];

    $mensagem = "Compra finalizada com sucesso! O estoque foi atualizado.";

} catch (Exception $e) {
    // Se algo deu errado, reverte a transação
    mysqli_rollback($conn);
    $mensagem = "Erro ao finalizar a compra: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
        <meta charset="UTF-8">
        <title>Finalizar Compra</title>
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
                        <li class="nav-item"><a class="nav-link" href="create.php">Adicionar Jogo</a></li>
                        <li class="nav-item"><a class="nav-link" href="estoque.php">Estoque</a></li>
                        <li class="nav-item"><a class="nav-link" href="usuarios.php">Usuários</a></li>
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="carrinho.php">Carrinho
                                <span class="badge bg-info text-dark"><?php echo isset($_SESSION['carrinho']) ? count($_SESSION['carrinho']) : 0; ?></span>
                            </a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container content">
            <h2>Status da Compra</h2>
            <p><?= $mensagem ?></p>
            <a href="index.php" class="btn btn-info text-dark fw-bold">Continuar Comprando</a>
        </div>
    <footer>
        <p>&copy; 2025 Ber. Todos os direitos reservados.</p>
    </footer>
</body>
</html>

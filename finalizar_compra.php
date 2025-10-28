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
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <h1>Finalização da Compra</h1>
        <nav>
            <ul>
                <li><a href="index.php">Início</a></li>
                <li><a href="carrinho.php">Voltar ao Carrinho</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <h2>Status da Compra</h2>
        <p><?= $mensagem ?></p>
        <a href="index.php">Continuar Comprando</a>
    </div>
    <footer>
        <p>&copy; 2025 Ber. Todos os direitos reservados.</p>
    </footer>
</body>
</html>

<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jogo_id = $_POST['jogo_id'] ?? null;
    $quantidade = $_POST['quantidade'] ?? 1;

    if ($jogo_id && $quantidade > 0) {
        // Verifica se o jogo existe e se há estoque
        $stmt = mysqli_prepare($conn, "SELECT quantidade FROM jogos WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $jogo_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $jogo = mysqli_fetch_assoc($result);

        if ($jogo) {
            $estoqueAtual = $jogo['quantidade'];
            $quantidadeNoCarrinho = $_SESSION['carrinho'][$jogo_id] ?? 0;

            if (($quantidade + $quantidadeNoCarrinho) <= $estoqueAtual) {
                // Adiciona o item ao carrinho
                if (!isset($_SESSION['carrinho'])) {
                    $_SESSION['carrinho'] = [];
                }
                if (isset($_SESSION['carrinho'][$jogo_id])) {
                    $_SESSION['carrinho'][$jogo_id] += $quantidade;
                } else {
                    $_SESSION['carrinho'][$jogo_id] = $quantidade;
                }
                // Redireciona para o carrinho
                header("Location: carrinho.php");
                exit;
            } else {
                // Estoque insuficiente
                echo "Estoque insuficiente. Você tentou adicionar $quantidade, mas só temos $estoqueAtual em estoque (e você já tem $quantidadeNoCarrinho no carrinho).";
                echo "<br><a href='index.php' class='btn btn-info text-dark fw-bold'>Voltar</a>";
                exit;
            }
        }
    }
}
// Se algo der errado, redireciona para a página inicial
header("Location: index.php");
exit;

<?php
session_start();
include 'conn.php';

// Proteção de página: Apenas usuários logados podem deletar
if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, redireciona para o login
    header("Location: login.php");
    exit();
}

// Verifica se o ID foi fornecido na URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Prepara e executa a exclusão de forma segura
    $stmt = $conn->prepare("DELETE FROM jogos WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Opcional: Adicionar uma mensagem de sucesso na sessão
        $_SESSION['message'] = "Jogo excluído com sucesso!";
    } else {
        // Opcional: Adicionar uma mensagem de erro na sessão
        $_SESSION['error'] = "Erro ao excluir o jogo.";
    }

    $stmt->close();
    $conn->close();

    // Redireciona de volta para a página de estoque
    header("Location: estoque.php");
    exit();
} else {
    // Se nenhum ID for fornecido ou for inválido, redireciona para o estoque
    header("Location: estoque.php");
    exit();
}
?>

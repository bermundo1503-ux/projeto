<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'conn.php'; // Corrigido para conn.php

$message = '';

// Adicionar ou remover item do estoque
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $jogo_id = (int)$_POST['jogo_id'];
    $quantidade = (int)$_POST['quantidade'];
    $action = $_POST['action'];

    if ($action === 'add') {
        $sql = "UPDATE jogos SET quantidade = quantidade + ? WHERE id = ?";
        $message = "Estoque adicionado com sucesso!";
    } elseif ($action === 'remove') {
        // Prevenir que o estoque fique negativo
        $sql = "UPDATE jogos SET quantidade = GREATEST(0, quantidade - ?) WHERE id = ?";
        $message = "Estoque removido com sucesso!";
    }

    if (isset($sql)) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $quantidade, $jogo_id);
        $stmt->execute();
        header("Location: estoque.php?message=" . urlencode($message));
        exit;
    }
}

// Deletar um jogo
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_para_deletar = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM jogos WHERE id = ?");
    $stmt->bind_param("i", $id_para_deletar);
    $stmt->execute();
    $message = "Jogo deletado com sucesso!";
    header("Location: estoque.php?message=" . urlencode($message));
    exit;
}


// Pega todos os jogos para exibir
$result = mysqli_query($conn, "SELECT id, titulo, quantidade, preco FROM jogos ORDER BY titulo");
$jogos = mysqli_fetch_all($result, MYSQLI_ASSOC);

if(isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Estoque</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <li class="nav-item"><a class="nav-link active" href="estoque.php">Estoque</a></li>
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
    <h1 class="mb-4">Gerenciar Estoque e Jogos</h1>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="card mb-5">
        <div class="card-header">
            Atualizar Estoque
        </div>
        <div class="card-body">
            <form method="POST" action="estoque.php" class="row g-3 align-items-end">
                <input type="hidden" name="update_stock" value="1">
                <div class="col-md-5">
                    <label for="jogo_id" class="form-label">Jogo</label>
                    <select name="jogo_id" id="jogo_id" class="form-select" required>
                        <option value="">Selecione um Jogo</option>
                        <?php foreach ($jogos as $jogo) : ?>
                            <option value="<?php echo $jogo['id']; ?>"><?php echo htmlspecialchars($jogo['titulo']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="quantidade" class="form-label">Quantidade</label>
                    <input type="number" name="quantidade" id="quantidade" class="form-control" required min="1">
                </div>
                <div class="col-md-4">
                    <div class="btn-group" role="group">
                        <button type="submit" name="action" value="add" class="btn btn-primary">Adicionar</button>
                        <button type="submit" name="action" value="remove" class="btn btn-warning">Remover</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <h2 class="mt-5 mb-3">Estoque Atual</h2>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jogos as $jogo) : ?>
                    <tr>
                        <td><?php echo $jogo['id']; ?></td>
                        <td><?php echo htmlspecialchars($jogo['titulo']); ?></td>
                        <td>R$ <?php echo number_format($jogo['preco'], 2, ',', '.'); ?></td>
                        <td><?php echo $jogo['quantidade']; ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $jogo['id']; ?>" class="btn btn-sm btn-info">Editar</a>
                            <a href="estoque.php?action=delete&id=<?php echo $jogo['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja deletar este jogo? Esta ação não pode ser desfeita.');">Deletar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<footer class="bg-dark text-white text-center p-3 mt-5">
    <p>&copy; <?php echo date('Y'); ?> Ber. Todos os direitos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

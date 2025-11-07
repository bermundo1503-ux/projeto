<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'conn.php'; // Alterado de db.php para conn.php

// Garante que o carrinho exista na sessão
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Ação: Esvaziar o carrinho
if (isset($_GET['acao']) && $_GET['acao'] == 'esvaziar') {
    $_SESSION['carrinho'] = [];
    header("Location: carrinho.php");
    exit;
}

// Ação: Remover item do carrinho
if (isset($_GET['acao']) && $_GET['acao'] == 'remover' && isset($_GET['id'])) {
    $idParaRemover = (int)$_GET['id'];
    unset($_SESSION['carrinho'][$idParaRemover]);
    header("Location: carrinho.php");
    exit;
}

// Ação: Atualizar quantidade
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao']) && $_POST['acao'] == 'atualizar') {
    foreach ($_POST['quantidades'] as $id => $quantidade) {
        $id = (int)$id;
        $quantidade = (int)$quantidade;
        if ($quantidade > 0) {
            $_SESSION['carrinho'][$id] = $quantidade;
        } else {
            unset($_SESSION['carrinho'][$id]);
        }
    }
    header("Location: carrinho.php");
    exit;
}

$jogos_no_carrinho = [];
$total_carrinho = 0;

if (!empty($_SESSION['carrinho'])) {
    $ids_jogos = array_keys($_SESSION['carrinho']);
    $ids_string = implode(',', $ids_jogos);
    $sql = "SELECT id, titulo, preco, imagem, imagem_tipo, quantidade as estoque_disponivel FROM jogos WHERE id IN ($ids_string)";
    $result = $conn->query($sql);

    if ($result) {
        while ($jogo = $result->fetch_assoc()) {
            $jogos_no_carrinho[] = $jogo;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

    <div class="container content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="display-5">Seu Carrinho</h1>
            <?php if (!empty($jogos_no_carrinho)): ?>
                <a href="carrinho.php?acao=esvaziar" class="btn btn-danger">Esvaziar Carrinho</a>
            <?php endif; ?>
        </div>

        <?php if (empty($jogos_no_carrinho)): ?>
            <div class="alert alert-info" role="alert">
                Seu carrinho está vazio. <a href="index.php" class="alert-link">Voltar para a loja</a>.
            </div>
        <?php else: ?>
            <form action="carrinho.php" method="post">
                <input type="hidden" name="acao" value="atualizar">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col" style="width: 50%;">Produto</th>
                                <th scope="col" class="text-center">Quantidade</th>
                                <th scope="col" class="text-end">Preço Unitário</th>
                                <th scope="col" class="text-end">Subtotal</th>
                                <th scope="col" class="text-center">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jogos_no_carrinho as $jogo):
                                $id = $jogo['id'];
                                $quantidade = $_SESSION['carrinho'][$id];
                                $subtotal = $jogo['preco'] * $quantidade;
                                $total_carrinho += $subtotal;
                            ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($jogo['imagem']): ?>
                                                <img src="data:<?php echo $jogo['imagem_tipo']; ?>;base64,<?php echo base64_encode($jogo['imagem']); ?>" alt="<?php echo htmlspecialchars($jogo['titulo']); ?>" class="img-fluid rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
                                            <?php endif; ?>
                                            <h5 class="mb-0"><?php echo htmlspecialchars($jogo['titulo']); ?></h5>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <input type="number" name="quantidades[<?php echo $id; ?>]" value="<?php echo $quantidade; ?>" min="1" max="<?php echo $jogo['estoque_disponivel']; ?>" class="form-control form-control-sm" style="width: 80px; margin: auto;">
                                    </td>
                                    <td class="text-end">R$ <?php echo number_format($jogo['preco'], 2, ',', '.'); ?></td>
                                    <td class="text-end">R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></td>
                                    <td class="text-center">
                                        <a href="carrinho.php?acao=remover&id=<?php echo $id; ?>" class="btn btn-sm btn-outline-danger">Remover</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <button type="submit" class="btn btn-primary">Atualizar Carrinho</button>
                    <div class="text-end">
                        <h3 class="mb-0">Total: R$ <?php echo number_format($total_carrinho, 2, ',', '.'); ?></h3>
                    </div>
                </div>
            </form>

            <div class="text-end mt-3">
                <form action="finalizar_compra.php" method="post">
                    <button type="submit" class="btn btn-success btn-lg">Finalizar Compra</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <footer class="bg-dark text-white text-center p-3 mt-5">
        <p>&copy; <?php echo date('Y'); ?> Ber. Todos os direitos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
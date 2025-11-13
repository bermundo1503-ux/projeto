<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'conn.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header("Location: estoque.php");
    exit();
}
$message = '';
$error = '';

// Recebe a mensagem de sucesso da URL (adicionada após o redirecionamento corrigido)
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}

// Processar o formulário de atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $preco = $_POST['preco'] ?? 0;
    $quantidade = $_POST['quantidade'] ?? 0;

    if (!empty($titulo) && !empty($descricao) && is_numeric($preco) && is_numeric($quantidade)) {
        // Se uma nova imagem for enviada
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
            $imagem = file_get_contents($_FILES['imagem']['tmp_name']);
            // Tenta obter o tipo de arquivo, ou usa jpeg como fallback
            $tipo = $_FILES['imagem']['type'] ?: 'image/jpeg'; 

            $stmt = $conn->prepare("UPDATE jogos SET titulo = ?, descricao = ?, preco = ?, quantidade = ?, imagem = ?, imagem_tipo = ? WHERE id = ?");
            if ($stmt === false) {
                $error = "Erro na preparação da consulta: " . $conn->error;
            } else {
                // tipos: s (titulo), s (descricao), d (preco), i (quantidade), s (imagem blob), s (imagem_tipo), i (id)
                $stmt->bind_param("ssdissi", $titulo, $descricao, $preco, $quantidade, $imagem, $tipo, $id);
            }
        } else {
            // Se nenhuma imagem for enviada, mantenha a existente
            $stmt = $conn->prepare("UPDATE jogos SET titulo = ?, descricao = ?, preco = ?, quantidade = ? WHERE id = ?");
            if ($stmt === false) {
                $error = "Erro na preparação da consulta: " . $conn->error;
            } else {
                $stmt->bind_param("ssdii", $titulo, $descricao, $preco, $quantidade, $id);
            }
        }

        if (isset($stmt) && $stmt !== false) {
            if ($stmt->execute()) {
                // 🏆 CORREÇÃO: Redireciona para a mesma página, AGORA INCLUINDO A MENSAGEM DE SUCESSO na URL
                $success_message = urlencode("Jogo alterado com sucesso!");
                header("Location: edit.php?id=" . urlencode($id) . "&message=" . $success_message . "&t=" . time());
                exit();
            } else {
                $error = "Erro ao atualizar o jogo: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $error = "Por favor, preencha todos os campos corretamente.";
    }
}

// Buscar os dados do jogo para preencher o formulário
$stmt = $conn->prepare("SELECT * FROM jogos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$game = $result->fetch_assoc();
$stmt->close();

if (!$game) {
    header("Location: estoque.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Jogo</title>
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

    <div class="container content mt-5">
        <h1 class="mb-4">Editar Jogo: <?= htmlspecialchars($game['titulo']) ?></h1>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título do Jogo</label>
                        <input type="text" name="titulo" id="titulo" class="form-control" value="<?= htmlspecialchars($game['titulo']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea name="descricao" id="descricao" class="form-control" rows="3" required><?= htmlspecialchars($game['descricao']) ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="preco" class="form-label">Preço</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" name="preco" id="preco" class="form-control" step="0.01" min="0" value="<?= htmlspecialchars($game['preco']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="quantidade" class="form-label">Quantidade em Estoque</label>
                            <input type="number" name="quantidade" id="quantidade" class="form-control" min="0" value="<?= htmlspecialchars($game['quantidade']) ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Imagem Atual</label>
                        <div>
                            <img src="imagem.php?id=<?= $id ?>&t=<?= isset($_GET['t']) ? (int)$_GET['t'] : time() ?>" alt="Imagem atual" class="img-thumbnail" style="max-width: 150px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="imagem" class="form-label">Trocar Imagem (opcional)</label>
                        <input type="file" name="imagem" id="imagem" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary">Atualizar Jogo</button>
                    <a href="estoque.php" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center p-3 mt-5">
        <p>&copy; <?php echo date('Y'); ?> Ber. Todos os direitos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
require_once 'conn.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = $_POST['preco'] ?? '';
    $quantidade = $_POST['quantidade'] ?? '';

    if ($titulo === '' || $descricao === '' || $preco === '' || $quantidade === '') {
        $error = 'Por favor, preencha todos os campos.';
    } elseif (!is_numeric($preco) || !is_numeric($quantidade)) {
        $error = 'Preço e quantidade devem ser números.';
    } else {
        // Processar imagem se enviada
        $imagem = null;
        $tipo = '';
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
            $imagem = file_get_contents($_FILES['imagem']['tmp_name']);
            $tipo = $_FILES['imagem']['type'];
        }

        $stmt = $conn->prepare("INSERT INTO jogos (titulo, descricao, preco, quantidade, imagem, imagem_tipo) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $null = NULL;
            // bind: titulo(s), descricao(s), preco(d), quantidade(i), imagem(b), imagem_tipo(s)
            $stmt->bind_param("ssdiss", $titulo, $descricao, $preco, $quantidade, $null, $tipo);
            if ($imagem !== null) {
                // send_long_data expects parameter number (0-based) -> imagem is 4th index (0..5)
                $stmt->send_long_data(4, $imagem);
            }
            if ($stmt->execute()) {
                $success = '🎮 Jogo cadastrado com sucesso!';
            } else {
                $error = 'Erro ao cadastrar jogo: ' . $stmt->error;
            }
        } else {
            $error = 'Erro ao preparar a query: ' . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Jogos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Fase Bônus</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Início</a></li>
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

    <div class="content container d-flex justify-content-center align-items-center py-5">
        <div class="card p-4 shadow-lg" style="width: 500px;">
            <h2 class="mb-3">Cadastrar Jogo</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Título:</label>
                    <input type="text" name="titulo" class="form-control" required value="<?php echo isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : ''; ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Descrição:</label>
                    <textarea name="descricao" rows="3" class="form-control" required><?php echo isset($_POST['descricao']) ? htmlspecialchars($_POST['descricao']) : ''; ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Preço:</label>
                        <input type="number" name="preco" step="0.01" class="form-control" required value="<?php echo isset($_POST['preco']) ? htmlspecialchars($_POST['preco']) : ''; ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Quantidade:</label>
                        <input type="number" name="quantidade" class="form-control" required value="<?php echo isset($_POST['quantidade']) ? htmlspecialchars($_POST['quantidade']) : ''; ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Imagem:</label>
                    <input type="file" name="imagem" accept="image/*" class="form-control">
                </div>

                <div class="d-grid mt-3">
                    <button type="submit" class="btn btn-info text-dark fw-bold">SALVAR</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

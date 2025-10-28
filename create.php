<?php
require_once 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $quantidade = $_POST['quantidade'];

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $imagem = file_get_contents($_FILES['imagem']['tmp_name']);
        $tipo = $_FILES['imagem']['type'];

        $stmt = $conn->prepare("INSERT INTO jogos (titulo, descricao, preco, quantidade, imagem, imagem_tipo) VALUES (?, ?, ?, ?, ?, ?)");
        $null = NULL;
        $stmt->bind_param("ssdisb", $titulo, $descricao, $preco, $quantidade, $null, $tipo);
        $stmt->send_long_data(4, $imagem);
        $stmt->execute();

        echo "<div class='alert alert-success text-center mt-3'>🎮 Jogo cadastrado com sucesso!</div>";
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
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url('img/background2.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-family: 'Press Start 2P', cursive;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: -1;
        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            border-bottom: 2px solid #0dcaf0;
            box-shadow: 0 0 10px #0dcaf0;
            background-color: rgba(0, 0, 0, 0.85) !important;
        }

        .navbar-brand {
            color: #0dcaf0 !important;
            text-shadow: 0 0 5px #0dcaf0;
            font-size: 12px;
        }

        .nav-link {
            color: #fff !important;
            font-size: 10px;
            transition: 0.3s;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #0dcaf0 !important;
            text-shadow: 0 0 10px #0dcaf0;
        }

        .content {
            margin-top: 90px;
            flex: 1;
        }
        .card {
            background-color: rgba(33, 37, 41, 0.85);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.125);
            border-radius: 20px;
        }

        h2 {
            text-align: center;
            color: #0dcaf0;
            font-size: 14px;
            margin-bottom: 20px;
        }

        input, textarea {
            background-color: rgba(0, 0, 0, 0.7);
            border: 2px solid #0dcaf0;
            color: #fff;
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            font-size: 10px;
        }

        input:focus, textarea:focus {
            outline: none;
            box-shadow: 0 0 10px #0dcaf0;
        }

        button {
            background-color: #0dcaf0;
            color: #000;
            border: none;
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            font-size: 10px;
            margin-top: 20px;
            transition: 0.3s;
        }

        button:hover {
            background-color: #09a5cb;
            transform: scale(1.05);
        }

        @media (max-width: 576px) {
            .card {
                width: 90%;
                margin: auto;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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
            </div>
        </div>
    </nav>

    <!-- 🎮 Conteúdo principal -->
    <div class="content container d-flex justify-content-center align-items-center py-5">
        <div class="card p-4 shadow-lg" style="width: 500px;">
            <h2>CADASTRAR JOGO</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <label class="form-label">Título:</label>
                <input type="text" name="titulo" required>

                <label class="form-label mt-3">Descrição:</label>
                <textarea name="descricao" rows="3" required></textarea>

                <label class="form-label mt-3">Preço:</label>
                <input type="number" name="preco" step="0.01" required>

                <label class="form-label mt-3">Quantidade:</label>
                <input type="number" name="quantidade" required>

                <label class="form-label mt-3">Imagem:</label>
                <input type="file" name="imagem" accept="image/*" required>

                <button type="submit" class="mt-4">SALVAR</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

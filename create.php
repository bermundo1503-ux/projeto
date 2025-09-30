<?php
include 'db.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Adicionar Novo Jogo</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body>
  <header>
    <h1>Adicionar Jogo</h1>
    <nav>
      <ul>
        <li><a href="index.php">Início</a></li>
        <li><a href="create.php">Adicionar Jogo</a></li>
      </ul>
    </nav>
  </header>

  <section class="hero">
    <h2>Cadastro de Jogo</h2>
    <p>Preencha os dados abaixo para adicionar um novo jogo</p>
  </section>

  <form method="POST" enctype="multipart/form-data">
    <input type="text" name="titulo" placeholder="Título do Jogo" required>
    <textarea name="descricao" placeholder="Descrição do Jogo" required></textarea>
    <input type="file" name="imagem" accept="image/*" required>
    <button type="submit">Salvar Jogo</button>
  </form>

  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $imagem = file_get_contents($_FILES['imagem']['tmp_name']);
    $tipo = $_FILES['imagem']['type'];

    $stmt = mysqli_prepare($conn, "INSERT INTO jogos (titulo, descricao, imagem, imagem_tipo) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $titulo, $descricao, $imagem, $tipo);
    mysqli_stmt_send_long_data($stmt, 2, $imagem);
    mysqli_stmt_execute($stmt);

    echo "<p style='text-align:center;'>Jogo cadastrado com sucesso!</p>";
    echo "<div style='text-align:center;'><a href='index.php'>⬅ Voltar para a lista</a></div>";
  }
  ?>

  <footer>
    <p>&copy; 2025 Ber. Todos os direitos reservados.</p>
  </footer>
</body>
</html>

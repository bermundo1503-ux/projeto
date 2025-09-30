 <?php include 'db.php'; ?>
<?php
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM jogos WHERE id = ?");
$stmt->execute([$id]);
$game = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Editar Jogo</title>
</head>
<body>
  <h1>Editar Jogo</h1>
  <form action="" method="POST" enctype="multipart/form-data">
    <input type="text" name="titulo" value="<?= $game['titulo'] ?>" required><br>
    <textarea name="descricao" required><?= $game['descricao'] ?></textarea><br>
    <input type="file" name="imagem"><br>
    <button type="submit">Atualizar</button>
  </form>

  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $imagem = $game['imagem'];

    if (!empty($_FILES['imagem']['name'])) {
      $imagem = $_FILES['imagem']['name'];
      $tmp = $_FILES['imagem']['tmp_name'];
      move_uploaded_file($tmp, "uploads/$imagem");
    }

    $stmt = $pdo->prepare("UPDATE jogos SET titulo = ?, descricao = ?, imagem = ? WHERE id = ?");
    $stmt->execute([$titulo, $descricao, $imagem, $id]);
    echo "Jogo atualizado!";
  }
  ?>
</body>
</html>

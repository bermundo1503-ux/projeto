  <?php include 'db.php'; ?>
  <!DOCTYPE html>
  <html lang="pt-br">
  <head>
    <meta charset="UTF-8">
    <title>Jogos Cadastrados</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
  </head>
  <body>
    <header>
      <h1>Soulbound Clone</h1>
      <nav>
        <ul>
          <li><a href="index.php">Início</a></li>
          <li><a href="create.php">Adicionar Jogo</a></li>
        </ul>
      </nav>
    </header>

    <section class="hero">
      <h2>Jogos Cadastrados</h2>
      <p>Veja os jogos adicionados com imagens direto do banco!</p>
    </section>

    <div class="games">
      <?php
      $result = mysqli_query($conn, "SELECT * FROM jogos ORDER BY id DESC");
      while ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='game'>
                <h3>{$row['titulo']}</h3>
                <p>{$row['descricao']}</p>
                <img src='imagem.php?id={$row['id']}' width='200'>
              </div>";
      }
      ?>
    </div>

    <footer>
      <p>&copy; 2025 Ber. Todos os direitos reservados.</p>
    </footer>
  </body>
  </html>

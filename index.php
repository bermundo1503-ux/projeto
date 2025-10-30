<?php
session_start();
// Exigir login para acessar a página inicial
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Conexão com o banco
include 'conn.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fase Bônus - Loja de Jogos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ✅ NAVBAR DEVE VIR PRIMEIRO -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow">
  <div class="container-fluid">
      <a class="navbar-brand" href="index.php">Fase Bônus</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
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

<!-- ✅ CARROSSEL -->
<div class="container content">
  <div class="card" id="carousel">
      <div class="carousel" aria-label="Carrossel de exemplo">
          <div class="slides" id="slides">
              <?php
              $slides = [];
              $preferred = ['slide1', 'slide2', 'slide3'];
              foreach ($preferred as $name) {
                  foreach (['jpg','jpeg','png','webp'] as $ext) {
                      $path = __DIR__ . '/img/' . $name . '.' . $ext;
                      if (file_exists($path)) {
                          $slides[] = 'img/' . $name . '.' . $ext;
                          break 2;
                      }
                  }
              }

              if (count($slides) < 3) {
                  $files = glob(__DIR__ . '/img/*.{jpg,jpeg,png,webp}', GLOB_BRACE);
                  foreach ($files as $f) {
                      $basename = basename($f);
                      if (in_array($basename, ['background2.jpg','no-image.png'])) continue;
                      $url = 'img/' . $basename;
                      if (!in_array($url, $slides)) $slides[] = $url;
                      if (count($slides) >= 3) break;
                  }
              }

              if (empty($slides)) {
                  echo '<div class="slide"><div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#00fff2">Sem imagens no carrossel</div></div>';
              } else {
                  foreach ($slides as $s) {
                      echo '<div class="slide"><img src="' . htmlspecialchars($s) . '" alt="Slide" /></div>';
                  }
              }
              ?>
          </div>
      </div>
      <div class="controls">
          <button id="prev" class="small">◀</button>
          <button id="next" class="small">▶</button>
      </div>
  </div>
</div>
<!-- JOGOS -->
<div class="container content">
<div class="text-center my-5">
    <img src="img/Copilot_20251003_075822.png" class="img-fluid mb-3" style="max-height:150px;">
    <h1 class="text-info">Jogos Disponíveis</h1>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
<?php
$result = mysqli_query($conn, "SELECT * FROM jogos WHERE quantidade>0 ORDER BY id DESC");
if(mysqli_num_rows($result)>0){
    while($row=mysqli_fetch_assoc($result)){ ?>
    <div class="col">
        <div class="card h-100 shadow-sm">
            <img src="imagem.php?id=<?php echo $row['id']; ?>" class="card-img-top" style="height:200px; object-fit:cover; border-radius:20px 20px 0 0;">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title text-info"><?php echo htmlspecialchars($row['titulo']); ?></h5>
                <p class="card-text flex-grow-1">
                    <?php 
                    $desc = htmlspecialchars($row['descricao']);
                    if(strlen($desc)>100){
                        echo '<span class="short-desc">'.substr($desc,0,100).'...</span>';
                        echo '<span class="full-desc">'.substr($desc,100).'</span>';
                        echo ' <button type="button" class="read-more">Leia mais</button>';
                    }else{
                        echo $desc;
                    }
                    ?>
                </p>
                <p><strong>Preço:</strong> R$ <?php echo number_format($row['preco'],2,',','.'); ?></p>
                <p><small>Estoque: <?php echo $row['quantidade']; ?></small></p>
                <form action="adicionar_carrinho.php" method="post" class="mt-auto">
                    <input type="hidden" name="jogo_id" value="<?php echo $row['id']; ?>">
                    <div class="input-group">
                        <input type="number" name="quantidade" value="1" min="1" max="<?php echo $row['quantidade']; ?>" class="form-control input-compact">
                        <button type="submit" class="btn btn-info text-dark fw-bold">Adicionar</button>
                    </div>
                </form>
                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-light btn-sm mt-2">Editar</a>
            </div>
        </div>
    </div>
<?php }}else{ echo '<p class="text-center text-light">Nenhum jogo disponível no momento.</p>';} ?>
</div>
</div>

<!-- RODAPÉ -->
<footer class="bg-dark text-white text-center p-3 mt-5">
<p>&copy; <?php echo date('Y'); ?> - Ber. Todos os direitos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// CARROSSEL
const slides=document.getElementById('slides');
const total=slides.children.length;
let idx=0;
function showSlide(i){ slides.style.transform='translateX('+(-i*100)+'%)'; }
document.getElementById('next').addEventListener('click',()=>{ idx=(idx+1)%total; showSlide(idx); });
document.getElementById('prev').addEventListener('click',()=>{ idx=(idx-1+total)%total; showSlide(idx); });

// LEIA MAIS
document.querySelectorAll('.read-more').forEach(btn=>{
    btn.addEventListener('click',function(e){
        e.preventDefault();
        const parent=this.parentElement;
        const shortDesc=parent.querySelector('.short-desc');
        const fullDesc=parent.querySelector('.full-desc');
        if(fullDesc.style.display==='inline'){
            fullDesc.style.display='none';
            shortDesc.style.display='inline';
            this.textContent='Leia mais';
        }else{
            fullDesc.style.display='inline';
            shortDesc.style.display='none';
            this.textContent='Leia menos';
        }
    });
});
</script>
</body>
</html>

<?php
session_start();
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
<style>
/* === FUNDO RETRÔ === */
body {
    margin:0; padding:0;
    background-image:url('img/background2.jpg');
    background-size:cover;
    background-attachment:fixed;
    background-position:center;
    font-family:'Press Start 2P',cursive;
    color:#00fff2;
    text-shadow:0 0 5px #00fff2;
}
body::before {
    content:''; position:fixed; top:0; left:0; right:0; bottom:0;
    background-color:rgba(0,0,10,0.85); z-index:-1;
}

/* === NAVBAR === */
.navbar { background-color: rgba(0,0,0,0.95)!important; border-bottom:3px solid #00fff2; box-shadow:0 0 20px #00fff2; font-size:10px; letter-spacing:1px; }
.navbar-brand, .nav-link { color:#00fff2!important; text-shadow:0 0 10px #00fff2; }
.nav-link:hover, .nav-link.active { color:#00fff2!important; text-shadow:0 0 15px #00fff2, 0 0 20px #00fff2; }

/* === CARDS === */
.card { background-color: rgba(0,0,0,0.85); border:2px solid #00fff2; border-radius:8px; box-shadow:0 0 10px rgba(0,255,242,0.2); text-align:left; transition: transform 0.2s, box-shadow 0.2s; }
.card:hover { transform:scale(1.03); box-shadow:0 0 20px #00fff2; }
.card-title { color:#00fff2; text-shadow:0 0 8px #00fff2; }

/* === INPUT COMPACTO === */
.input-compact { width:50px; padding:2px 4px; font-size:10px; border:2px solid #00fff2; border-radius:3px; background-color:rgba(0,0,0,0.9); color:#00fff2; text-align:center; }
.input-compact:focus { outline:none; box-shadow:0 0 8px #00fff2; }

/* === LEIA MAIS === */
.full-desc { display:none; color:#00fff2; }
.read-more { color:#00fff2; cursor:pointer; font-size:10px; text-decoration:underline; }

/* === CARROSSEL === */
.carousel { position:relative; height:160px; overflow:hidden; border-radius:8px; margin-bottom:40px; }
.slides { display:flex; height:100%; transition:transform .45s ease; }
.slide { min-width:100%; display:flex; align-items:center; justify-content:center; font-size:22px; font-weight:700; color:#00fff2; text-shadow:0 0 8px #00fff2; }
.slide-a { background: linear-gradient(45deg,#ffd6a5,#fdffb6); }
.slide-b { background: linear-gradient(45deg,#a0c4ff,#bdb2ff); }
.slide-c { background: linear-gradient(45deg,#caffbf,#9bf6ff); }
.controls { display:flex; gap:8px; justify-content:center; margin-top:8px; }
button.small { padding:4px 10px; font-size:16px; border:none; border-radius:4px; background-color:#00fff2; color:#000; cursor:pointer; font-family:'Press Start 2P',cursive; }
button.small:hover { background-color:#02c1c1; color:#fff; }

/* === RODAPÉ === */
footer { background-color: rgba(0,0,0,0.95); color:#00fff2; border-top:3px solid #00fff2; text-align:center; font-size:10px; letter-spacing:1px; padding:15px 0; text-shadow:0 0 10px #00fff2; }
</style>
</head>
<body>

<!-- CARROSSEL -->
<div class="container content">
<div class="card" id="carousel">
    <div class="example-title">Carrossel / Slider</div>
    <p>Troca de "slides" com botões.</p>
    <div class="carousel" aria-label="Carrossel de exemplo">
        <div class="slides" id="slides">
            <div class="slide slide-a">Slide 1 — Texto ou imagem</div>
            <div class="slide slide-b">Slide 2 — Promoção</div>
            <div class="slide slide-c">Slide 3 — Contato</div>
        </div>
    </div>
    <div class="controls">
        <button id="prev" class="small">◀</button>
        <button id="next" class="small">▶</button>
    </div>
</div>
</div>

<!-- NAVBAR -->
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
                        echo ' <a href="#" class="read-more">Leia mais</a>';
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

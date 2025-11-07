<?php
session_start();

// RECOMENDAÇÃO DE SEGURANÇA 1: Função para validação de sessão mais robusta.
// O ideal é que esta função verifique mais do que apenas a existência,
// como, por exemplo, o IP do usuário ou o tempo de inatividade.
function is_logged_in()
{
    return isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
}

// Exigir login para acessar a página inicial
if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

// Conexão com o banco
// RECOMENDAÇÃO DE SEGURANÇA 2: Garanta que 'conn.php' não possa ser acessado via navegador.
// Mova-o para fora do diretório raiz do servidor web.
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

   <div class="container content mt-5 pt-4">
    <!-- CORREÇÃO 1: Renomeada para a classe correta do CSS (.carousel) -->
    <div class="carousel"> 
        <?php
            // Buscar imagens do carrossel
            $carousel_images = [];

            // Prioridade: slide1, slide2, slide3
            for ($i = 1; $i <= 3; $i++) {
                $found = false;
                foreach (['jpg', 'jpeg', 'png', 'webp', 'gif'] as $ext) {
                    $filename = "slide{$i}.{$ext}";
                    $filepath = __DIR__ . '/img/' . $filename;
                    if (file_exists($filepath)) {
                        $carousel_images[] = 'img/' . $filename;
                        $found = true;
                        break;
                    }
                }
            }

            // Se não encontrou 3 imagens, busca qualquer imagem na pasta
            if (count($carousel_images) < 3) {
                $all_images = glob(__DIR__ . '/img/*.{jpg,jpeg,png,webp,gif}', GLOB_BRACE);
                $exclude = ['background2.jpg', 'no-image.png', 'Copilot_20251003_075822.png'];

                foreach ($all_images as $img_path) {
                    $basename = basename($img_path);
                    if (!in_array($basename, $exclude) && !in_array('img/' . $basename, $carousel_images)) {
                        $carousel_images[] = 'img/' . $basename;
                    }
                    if (count($carousel_images) >= 3) break;
                }
            }

            if (!empty($carousel_images)) :
            ?>
                <!-- CORREÇÃO 2: Renomeada para a classe correta do CSS (.slides) -->
                <div class="slides" id="carouselSlides"> 
                    <?php foreach ($carousel_images as $img): ?>
                        <!-- CORREÇÃO 3: Renomeada para a classe correta do CSS (.slide) -->
                        <div class="slide">
                            <img src="<?php echo htmlspecialchars($img); ?>" alt="Slide do Carrossel">
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="carousel-controls">
                    <button id="prevBtn">◀</button>
                    <button id="nextBtn">▶</button>
                </div>

                <div class="carousel-indicators" id="carouselIndicators">
                    <?php foreach ($carousel_images as $index => $img): ?>
                        <button data-slide="<?php echo $index; ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>"></button>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="carousel-no-images">
                     Nenhuma imagem encontrada no carrossel<br>
                    <small style="font-size: 14px; margin-top: 10px;">Adicione imagens chamadas slide1.jpg, slide2.jpg e slide3.jpg na pasta /img/</small>
                </div>
            <?php endif; ?>
    </div>
</div>


    <div class="container content">
        <div class="text-center my-5">
            <img src="img/Copilot_20251003_075822.png" class="img-fluid mb-3" style="max-height:150px;" onerror="this.style.display='none'">
            <h1 class="text-info">Jogos Disponíveis</h1>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php
            
            $sql = "SELECT * FROM jogos WHERE quantidade > 0 ORDER BY id DESC";
            $result = null;

            if ($stmt = mysqli_prepare($conn, $sql)) {
            
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        
            ?>
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <img src="imagem.php?id=<?php echo $row['id']; ?>" class="card-img-top" style="height:200px; object-fit:cover; border-radius:20px 20px 0 0;">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title text-info"><?php echo htmlspecialchars($row['titulo']); ?></h5>
                                    <p class="card-text flex-grow-1">
                                        <?php
                                        $desc = htmlspecialchars($row['descricao']);
                                        if (strlen($desc) > 100) {
                                            echo '<span class="short-desc">' . substr($desc, 0, 100) . '...</span>';
                                            echo '<span class="full-desc" style="display:none">' . substr($desc, 100) . '</span>';
                                            echo ' <button type="button" class="read-more btn btn-link btn-sm p-0">Leia mais</button>';
                                        } else {
                                            echo $desc;
                                        }
                                        ?>
                                    </p>
                                    <p><strong>Preço:</strong> R$ <?php echo number_format($row['preco'], 2, ',', '.'); ?></p>
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
            <?php
                    } 
                } else {
                    echo '<p class="text-center text-light">Nenhum jogo disponível no momento.</p>';
                }

            
                mysqli_stmt_close($stmt);
            } else {
                
                echo '<p class="text-center text-danger">Erro de banco de dados. Tente novamente mais tarde.</p>';
               
            }
            ?>
        </div>
    </div>
<body class="d-flex flex-column vh-100">
    <main class="flex-shrink-0">
        </main>

    <footer class="footer mt-auto bg-dark text-white text-center p-3">
        <p>&copy; <?php echo date('Y'); ?> - Ber. Todos os direitos reservados.</p>
    </footer>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      
        const carouselSlides = document.getElementById('carouselSlides');
        const carouselIndicators = document.getElementById('carouselIndicators');

        if (carouselSlides) {
            const slides = carouselSlides.children;
            const totalSlides = slides.length;
            let currentIndex = 0;
            let autoPlayInterval;

          
            function showSlide(index) {
                currentIndex = (index + totalSlides) % totalSlides;
                carouselSlides.style.transform = `translateX(-${currentIndex * 100}%)`;

                
                if (carouselIndicators) {
                    const indicators = carouselIndicators.children;
                    for (let i = 0; i < indicators.length; i++) {
                        indicators[i].classList.toggle('active', i === currentIndex);
                    }
                }
            }

           
            const nextBtn = document.getElementById('nextBtn');
            if (nextBtn) {
                nextBtn.addEventListener('click', () => {
                    showSlide(currentIndex + 1);
                    resetAutoPlay();
                });
            }

        
            const prevBtn = document.getElementById('prevBtn');
            if (prevBtn) {
                prevBtn.addEventListener('click', () => {
                    showSlide(currentIndex - 1);
                    resetAutoPlay();
                });
            }

           
            if (carouselIndicators) {
                const indicators = carouselIndicators.children;
                for (let i = 0; i < indicators.length; i++) {
                    indicators[i].addEventListener('click', () => {
                        showSlide(i);
                        resetAutoPlay();
                    });
                }
            }

           
            function startAutoPlay() {
                autoPlayInterval = setInterval(() => {
                    showSlide(currentIndex + 1);
                }, 4000); 
            }

            function resetAutoPlay() {
                clearInterval(autoPlayInterval);
                startAutoPlay();
            }

            carouselSlides.parentElement.addEventListener('mouseenter', () => {
                clearInterval(autoPlayInterval);
            });

            carouselSlides.parentElement.addEventListener('mouseleave', () => {
                startAutoPlay();
            });

            
            if (totalSlides > 1) {
                startAutoPlay();
            }

           
            let touchStartX = 0;
            let touchEndX = 0;

            carouselSlides.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            });

            carouselSlides.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                if (touchStartX - touchEndX > 50) {
                    showSlide(currentIndex + 1);
                    resetAutoPlay();
                } else if (touchEndX - touchStartX > 50) {
                    showSlide(currentIndex - 1);
                    resetAutoPlay();
                }
            });
        }

        document.querySelectorAll('.read-more').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const parent = this.parentElement;
                const shortDesc = parent.querySelector('.short-desc');
                const fullDesc = parent.querySelector('.full-desc');

                if (fullDesc.style.display === 'inline') {
                    fullDesc.style.display = 'none';
                    shortDesc.style.display = 'inline';
                    this.textContent = 'Leia mais';
                } else {
                    fullDesc.style.display = 'inline';
                    shortDesc.style.display = 'none';
                    this.textContent = 'Leia menos';
                }
            });
        });
    </script>
</body>

</html>
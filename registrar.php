<?php
session_start();
require_once 'conn.php';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

    if (empty($nome) || empty($email) || empty($senha) || empty($confirmar_senha)) {
        $error_message = "Por favor, preencha todos os campos.";
    } elseif ($senha !== $confirmar_senha) {
        $error_message = "As senhas não coincidem.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Este email já está cadastrado.";
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nome, $email, $senhaHash);
            if ($stmt->execute()) {
                $success_message = "Usuário registrado com sucesso! Você já pode fazer o <a href='login.php' class='alert-link'>login</a>.";
            } else {
                $error_message = "Erro ao registrar o usuário.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar - Fase Bônus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

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
                <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container content py-5">
    <div class="register-container p-4 mx-auto">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title text-center mb-4">Registrar</h1>
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?= $error_message ?></div>
                <?php endif; ?>
                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?= $success_message ?></div>
                <?php endif; ?>
                
                <?php if (!$success_message): // Oculta o formulário em caso de sucesso ?>
                <form action="registrar.php" method="POST">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome:</label>
                        <input type="text" id="nome" name="nome" class="form-control" required value="<?= htmlspecialchars($nome ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" required value="<?= htmlspecialchars($email ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha:</label>
                        <input type="password" id="senha" name="senha" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmar_senha" class="form-label">Confirmar Senha:</label>
                        <input type="password" id="confirmar_senha" name="confirmar_senha" class="form-control" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">Já tem uma conta? <a href="login.php">Faça o login</a></p>
            </div>
        </div>
    </div>
</div>

<footer class="bg-dark text-white text-center p-3">
    <p>&copy; <?php echo date('Y'); ?> Ber. Todos os direitos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

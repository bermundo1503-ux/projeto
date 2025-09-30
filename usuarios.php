    <?php
    session_start();
    require_once 'conn.php';

    // Verificação de login
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // Funções CRUD para Usuários
    function criarUsuario($conn, $nome, $email, $senha) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nome, $email, $senhaHash);
        return $stmt->execute();
    }

    function lerUsuarios($conn) {
        $sql = "SELECT * FROM usuarios";
        $result = $conn->query($sql);
        $usuarios = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $usuarios[] = $row;
            }
        }
        return $usuarios;
    }

    function atualizarUsuario($conn, $id, $nome, $email, $senha) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET nome=?, email=?, senha=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nome, $email, $senhaHash, $id);
        return $stmt->execute();
    }

    function deletarUsuario($conn, $id) {
        $sql = "DELETE FROM usuarios WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Exemplo de uso (para testar)
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['acao'])) {
            $acao = $_POST['acao'];
            switch ($acao) {
                case 'criar':
                    criarUsuario($conn, $_POST['nome'], $_POST['email'], $_POST['senha']);
                    break;
                case 'atualizar':
                    atualizarUsuario($conn, $_POST['id'], $_POST['nome'], $_POST['email'], $_POST['senha']);
                    break;
                case 'deletar':
                    deletarUsuario($conn, $_POST['id']);
                    break;
            }
            header("Location: usuarios.php");
            exit();
        }
    }

    $usuarios = lerUsuarios($conn);
    ?>

    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title>Gerenciamento de Usuários</title>
    </head>
    <body>
        <h1>Gerenciamento de Usuários</h1>
        <a href="index.php">Voltar ao Início</a>

        <h2>Adicionar Usuário</h2>
        <form method="POST">
            <input type="hidden" name="acao" value="criar">
            <input type="text" name="nome" placeholder="Nome" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Adicionar</button>
        </form>

        <h2>Lista de Usuários</h2>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= $usuario['id'] ?></td>
                    <td><?= $usuario['nome'] ?></td>
                    <td><?= $usuario['email'] ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="acao" value="deletar">
                            <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
                            <button type="submit">Deletar</button>
                        </form>
                        </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </body>
    </html>
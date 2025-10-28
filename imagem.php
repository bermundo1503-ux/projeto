<?php
include 'conn.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    header('Content-Type: image/png');
    readfile(__DIR__ . '/img/no-image.png');
    exit;
}

$stmt = $conn->prepare("SELECT imagem, imagem_tipo FROM jogos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    header('Content-Type: image/png');
    readfile(__DIR__ . '/img/no-image.png');
    exit;
}

$stmt->bind_result($imagem, $tipo);
$stmt->fetch();

if (empty($imagem)) {
    header('Content-Type: image/png');
    readfile(__DIR__ . '/img/no-image.png');
    exit;
}

// Tipo padrão se não houver tipo salvo
if (empty($tipo)) {
    $tipo = 'image/jpeg';
}

header("Content-Type: $tipo");
echo $imagem;
?>

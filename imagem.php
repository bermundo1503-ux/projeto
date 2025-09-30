<?php
include 'db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
  http_response_code(400);
  echo "ID inválido.";
  exit;
}

$stmt = mysqli_prepare($conn, "SELECT imagem, imagem_tipo FROM jogos WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) === 0) {
  http_response_code(404);
  echo "Imagem não encontrada.";
  exit;
}

mysqli_stmt_bind_result($stmt, $imagem, $tipo);
mysqli_stmt_fetch($stmt);

header("Content-Type: $tipo");
echo $imagem;
?>

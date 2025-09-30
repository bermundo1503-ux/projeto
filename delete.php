<?php include 'db.php'; ?>
<?php
$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM jogos WHERE id = ?");
$stmt->execute([$id]);
header("Location: index.php");
?>

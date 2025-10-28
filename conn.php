<?php
$host = 'localhost';
$user = 'root';
$pass = 'root';
$dbname = 'jogos_blob';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
  die("Erro na conexão: " . mysqli_connect_error());
}
?>
<?php
// check_db.php - Mostra a estrutura da tabela 'jogos'
include 'conn.php';

$result = $conn->query("DESCRIBE jogos");
if (!$result) {
    die('Erro ao descrever a tabela: ' . $conn->error);
}

echo '<h2>Estrutura da tabela jogos</h2>';
echo '<table border="1" cellpadding="5"><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>';
while ($row = $result->fetch_assoc()) {
    echo '<tr>';
    foreach ($row as $cell) {
        echo '<td>' . htmlspecialchars($cell) . '</td>';
    }
    echo '</tr>';
}
echo '</table>';

$conn->close();
?>
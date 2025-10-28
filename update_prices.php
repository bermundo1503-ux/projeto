<?php
include 'db.php';

$sql = "UPDATE jogos SET preco = 59.99 WHERE preco IS NULL OR preco = 0";

if (mysqli_query($conn, $sql)) {
    $num_rows = mysqli_affected_rows($conn);
    echo "<h2>Sucesso!</h2>";
    echo "<p>{$num_rows} jogos foram atualizados com o preço padrão de R$ 59,99.</p>";
    echo "<p>As advertências na página inicial devem ter desaparecido.</p>";
    echo "<p><strong>Importante:</strong> Agora você pode deletar este arquivo (<code>update_prices.php</code>) do seu projeto.</p>";
    echo "<a href='index.php'>Voltar para a página inicial</a>";
} else {
    echo "<h2>Erro!</h2>";
    echo "<p>Ocorreu um erro ao atualizar os preços: " . mysqli_error($conn) . "</p>";
}

mysqli_close($conn);
?>
<style>
    body {
        font-family: sans-serif;
        line-height: 1.6;
        padding: 20px;
        background-color: #f4f4f4;
        color: #333;
    }
    a {
        color: #007BFF;
    }
</style>

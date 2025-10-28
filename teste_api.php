<?php
$url = "https://www.freetogame.com/api/games?sort-by=popularity";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "Erro cURL: " . $error;
} else {
    echo $response;
}

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Seçilen özellikleri al
    $selectedFeatures = $_POST['woocommerce_features'] ?? [];

    // Özellikleri bir JSON dosyasına kaydedelim
    $outputFile = __DIR__ . '/../config/woocommerce_features.json';
    file_put_contents($outputFile, json_encode($selectedFeatures));

    echo "Seçilen özellikler başarıyla kaydedildi!";
    echo "<br><a href='../index.php?page=product_selection'>Geri Dön</a>";
}

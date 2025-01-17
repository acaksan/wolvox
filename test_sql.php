<?php
require_once __DIR__ . '/vendor/autoload.php';

$wolvoxConfig = require __DIR__ . '/config/wolvox_config.php';
$pdo = new PDO(
    "firebird:dbname={$wolvoxConfig['database']};charset=UTF8",
    $wolvoxConfig['user'],
    $wolvoxConfig['password']
);

// STOK tablosunun yapısını kontrol et
$sql = "
    SELECT FIRST 1
        s.*
    FROM STOK s
    WHERE s.STOKKODU = 'PET-100-70-13-175-4000'
";

$stmt = $pdo->query($sql);
$stok = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<h2>STOK Tablosu Yapısı:</h2>";
echo "<pre>";
print_r($stok);
echo "</pre>";

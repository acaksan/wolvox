<?php
require_once __DIR__ . '/vendor/autoload.php';

$wolvoxConfig = require __DIR__ . '/config/wolvox_config.php';
$pdo = new PDO(
    "firebird:dbname={$wolvoxConfig['database']};charset=UTF8",
    $wolvoxConfig['user'],
    $wolvoxConfig['password']
);

$stokkodu = 'PET-100-70-13-175-4000';

// STOK_FIYAT tablosundaki tüm fiyatları kontrol et
$sql = "
    SELECT 
        f.TANIMI,
        f.FIYATI,
        s.KDV_ORANI
    FROM STOK_FIYAT f
    INNER JOIN STOK s ON s.BLKODU = f.BLSTKODU
    WHERE s.STOKKODU = :stokkodu
    ORDER BY f.TANIMI
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['stokkodu' => $stokkodu]);
$prices = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Fiyat Detayları ($stokkodu):</h2>";
echo "<pre>";
foreach ($prices as $price) {
    echo "Fiyat Tipi: {$price['TANIMI']}\n";
    echo "Fiyat: " . number_format($price['FIYATI'], 2, ',', '.') . " TL\n";
    echo "KDV Oranı: %{$price['KDV_ORANI']}\n";
    echo "-------------------\n";
}
echo "</pre>";

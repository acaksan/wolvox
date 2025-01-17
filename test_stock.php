<?php
require_once __DIR__ . '/vendor/autoload.php';

use Classes\WolvoxDatabaseClient;

try {
    $wolvoxConfig = require __DIR__ . '/config/wolvox_config.php';
    $wolvoxDb = new WolvoxDatabaseClient(
        'localhost',
        $wolvoxConfig['database'],
        $wolvoxConfig['user'],
        $wolvoxConfig['password'],
        $wolvoxConfig['company_code'],
        $wolvoxConfig['period_code']
    );

    // Test bağlantıyı
    echo "Veritabanı bağlantısı test ediliyor...\n";
    $testQuery = "SELECT COUNT(*) FROM STOK WHERE WEBDE_GORUNSUN = 1 AND AKTIF = 1";
    $stmt = $wolvoxDb->getPdo()->prepare($testQuery);
    $stmt->execute();
    $count = $stmt->fetchColumn();
    echo "Aktif ve webde görünen toplam ürün sayısı: " . $count . "\n\n";

    // İlk 10 ürünü listele
    $sql = "
        SELECT FIRST 10
            s.BLKODU,
            s.STOKKODU,
            s.STOK_ADI,
            s.GRUBU,
            s.MARKASI,
            s.MODELI
        FROM STOK s
        WHERE s.WEBDE_GORUNSUN = 1 AND s.AKTIF = 1
        ORDER BY s.STOK_ADI
    ";

    $stmt = $wolvoxDb->getPdo()->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "İlk 10 Aktif Ürün:\n";
    echo str_repeat('-', 100) . "\n";
    printf("%-15s | %-30s | %-20s | %-15s\n", 'Stok Kodu', 'Ürün Adı', 'Grup', 'Marka');
    echo str_repeat('-', 100) . "\n";
    
    foreach ($products as $product) {
        printf(
            "%-15s | %-30s | %-20s | %-15s\n",
            $product['STOKKODU'],
            substr($product['STOK_ADI'], 0, 30),
            $product['GRUBU'],
            $product['MARKASI']
        );
    }
    echo str_repeat('-', 100) . "\n\n";

    // PTMP_DEPO_ENVANTERI tablosunu kontrol et
    echo "PTMP_DEPO_ENVANTERI tablosu kontrol ediliyor...\n";
    $testQuery = "SELECT COUNT(*) FROM PTMP_DEPO_ENVANTERI";
    $stmt = $wolvoxDb->getPdo()->prepare($testQuery);
    $stmt->execute();
    $count = $stmt->fetchColumn();
    echo "Mevcut kayıt sayısı: " . $count . "\n\n";

    // Tabloyu temizle
    echo "Tablo temizleniyor...\n";
    $sql = "DELETE FROM PTMP_DEPO_ENVANTERI";
    $stmt = $wolvoxDb->getPdo()->prepare($sql);
    $stmt->execute();
    echo "Tablo temizlendi.\n\n";

    // Stok hareketlerini kontrol et
    echo "Stok hareketleri kontrol ediliyor...\n";
    $sql = "
        SELECT COUNT(*) 
        FROM STOKHR h
        JOIN STOK s ON s.BLKODU = h.BLSTKODU
        WHERE h.SILINDI = 0 
        AND s.WEBDE_GORUNSUN = 1 
        AND s.AKTIF = 1
        AND h.DEPO_ADI IS NOT NULL 
        AND h.DEPO_ADI <> ''
    ";
    $stmt = $wolvoxDb->getPdo()->prepare($sql);
    $stmt->execute();
    $count = $stmt->fetchColumn();
    echo "Toplam stok hareketi sayısı: " . $count . "\n\n";

    // Stok bakiyelerini kontrol et
    $sql = "
        SELECT FIRST 10
            s.STOKKODU,
            s.STOK_ADI,
            s.GRUBU,
            s.MARKASI,
            d.DEPO_ADI,
            d.BAKIYE,
            d.DEPO_KODU
        FROM STOK s
        JOIN STOKDP d ON d.BLSTKODU = s.BLKODU
        WHERE s.WEBDE_GORUNSUN = 1 
        AND s.AKTIF = 1
        AND d.BAKIYE <> 0
        ORDER BY s.STOK_ADI, d.DEPO_ADI
    ";

    $stmt = $wolvoxDb->getPdo()->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($results)) {
        echo "Stok bakiyesi bulunamadı!\n";
    } else {
        echo "Stok Bakiyeleri (İlk 10 Kayıt):\n";
        echo str_repeat('-', 120) . "\n";
        printf("%-15s | %-30s | %-15s | %-15s | %10s\n", 
            'Stok Kodu', 'Ürün Adı', 'Depo', 'Depo Kodu', 'Bakiye');
        echo str_repeat('-', 120) . "\n";

        foreach ($results as $row) {
            printf(
                "%-15s | %-30s | %-15s | %-15s | %10.2f\n",
                $row['STOKKODU'],
                substr($row['STOK_ADI'], 0, 30),
                $row['DEPO_ADI'],
                $row['DEPO_KODU'],
                $row['BAKIYE']
            );
        }
        echo str_repeat('-', 120) . "\n";
    }

} catch (Exception $e) {
    echo "Hata: " . $e->getMessage() . "\n";
    echo "Hata Detayı: " . print_r($e, true) . "\n";
}
?>

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

    // STOK tablosunun tüm sütunlarını listeleyelim
    $sql = "
        SELECT r.RDB\$FIELD_NAME as COLUMN_NAME
        FROM RDB\$RELATION_FIELDS r
        WHERE r.RDB\$RELATION_NAME = 'STOK'
        ORDER BY r.RDB\$FIELD_POSITION
    ";
    
    $stmt = $wolvoxDb->getPdo()->prepare($sql);
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>STOK Tablosundaki Tüm Sütunlar:</h2>";
    echo "<pre>";
    foreach ($columns as $column) {
        echo trim($column['COLUMN_NAME']) . "\n";
    }
    echo "</pre>";

    // STOK tablosundaki liste fiyatı alanlarını kontrol ediyorum
    $sql = "
        SELECT 
            s.STOKKODU,
            s.STOK_ADI,
            s.MUH_ALIS,
            s.MUH_SATIS_YI,
            s.MUH_SATIS_YD,
            s.MUH_SATIS_IND,
            s.KDV_ORANI
        FROM STOK s
        WHERE s.STOKKODU = 'PET-100-70-13-175-4000'
    ";
    
    $stmt = $wolvoxDb->getPdo()->prepare($sql);
    $stmt->execute();
    $stok = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "<h2>STOK Tablosundaki Liste Fiyatı Alanları:</h2>";
    echo "<pre>";
    print_r($stok);
    echo "</pre>";

    if ($stok) {
        // Son hareketleri kontrol edelim
        $sql = "
            SELECT FIRST 5
                h.TARIHI,
                h.TUTAR_TURU,
                h.MIKTARI,
                h.KPB_FIYATI,
                h.KPB_TUTARI,
                h.EVRAK_NO,
                h.ACIKLAMA
            FROM STOKHR h
            WHERE h.BLSTKODU = (
                SELECT BLKODU 
                FROM STOK 
                WHERE STOKKODU = 'PET-100-70-13-175-4000'
            )
            AND h.SILINDI = 0
            ORDER BY h.TARIHI DESC
        ";
        
        $stmt = $wolvoxDb->getPdo()->prepare($sql);
        $stmt->execute();
        $hareketler = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<h2>200/2024 - STOKHR Tablosundaki Son 5 Hareket:</h2>";
        echo "<pre>";
        print_r($hareketler);
        echo "</pre>";

        // STOK_FIYAT tablosunu kontrol ediyorum
        $sql = "
            SELECT f.*
            FROM STOK_FIYAT f
            INNER JOIN STOK s ON s.BLKODU = f.BLSTKODU
            WHERE s.STOKKODU = 'PET-100-70-13-175-4000'
        ";
        
        $stmt = $wolvoxDb->getPdo()->prepare($sql);
        $stmt->execute();
        $fiyatlar = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<h2>STOK_FIYAT Tablosundaki Fiyatlar:</h2>";
        echo "<pre>";
        print_r($fiyatlar);
        echo "</pre>";
    }

} catch (Exception $e) {
    echo "<h2 style='color: red;'>Hata!</h2>";
    echo "<pre style='color: red;'>" . $e->getMessage() . "</pre>";
}

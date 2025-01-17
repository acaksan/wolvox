<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/classes/WolvoxDatabaseClient.php';

use Classes\WolvoxDatabaseClient;

$wolvoxConfig = require __DIR__ . '/config/wolvox_config.php';

try {
    $wolvoxClient = new WolvoxDatabaseClient(
        'localhost',
        $wolvoxConfig['database'],
        $wolvoxConfig['user'],
        $wolvoxConfig['password'],
        $wolvoxConfig['company_code'],
        $wolvoxConfig['period_code']
    );
    
    // STOK tablosunun yapısını kontrol et
    $sql = "
        SELECT 
            TRIM(r.RDB\$FIELD_NAME) as FIELD_NAME,
            f.RDB\$FIELD_LENGTH as FIELD_LENGTH,
            CASE f.RDB\$FIELD_TYPE
                WHEN 7 THEN 'SMALLINT'
                WHEN 8 THEN 'INTEGER'
                WHEN 10 THEN 'FLOAT'
                WHEN 12 THEN 'DATE'
                WHEN 13 THEN 'TIME'
                WHEN 14 THEN 'CHAR'
                WHEN 16 THEN 'BIGINT'
                WHEN 27 THEN 'DOUBLE PRECISION'
                WHEN 35 THEN 'TIMESTAMP'
                WHEN 37 THEN 'VARCHAR'
                WHEN 261 THEN 'BLOB'
                ELSE 'UNKNOWN'
            END as FIELD_TYPE
        FROM RDB\$RELATION_FIELDS r
        LEFT JOIN RDB\$FIELDS f ON r.RDB\$FIELD_SOURCE = f.RDB\$FIELD_NAME
        WHERE r.RDB\$RELATION_NAME = 'STOK'
        ORDER BY r.RDB\$FIELD_POSITION
    ";

    $stmt = $wolvoxClient->getPdo()->query($sql);
    $stokFields = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>STOK Tablosu Yapısı:</h2>";
    echo "<pre>";
    print_r($stokFields);
    echo "</pre>";

    echo "<h2>STOK_FIYAT Tablosu Yapısı:</h2>";
    $sql = "
        SELECT 
            TRIM(r.RDB\$FIELD_NAME) as FIELD_NAME,
            f.RDB\$FIELD_LENGTH as FIELD_LENGTH,
            CASE f.RDB\$FIELD_TYPE
                WHEN 7 THEN 'SMALLINT'
                WHEN 8 THEN 'INTEGER'
                WHEN 10 THEN 'FLOAT'
                WHEN 12 THEN 'DATE'
                WHEN 13 THEN 'TIME'
                WHEN 14 THEN 'CHAR'
                WHEN 16 THEN 'BIGINT'
                WHEN 27 THEN 'DOUBLE PRECISION'
                WHEN 35 THEN 'TIMESTAMP'
                WHEN 37 THEN 'VARCHAR'
                WHEN 261 THEN 'BLOB'
                ELSE 'UNKNOWN'
            END as FIELD_TYPE
        FROM RDB\$RELATION_FIELDS r
        LEFT JOIN RDB\$FIELDS f ON r.RDB\$FIELD_SOURCE = f.RDB\$FIELD_NAME
        WHERE r.RDB\$RELATION_NAME = 'STOK_FIYAT'
        ORDER BY r.RDB\$FIELD_POSITION
    ";

    $stmt = $wolvoxClient->getPdo()->query($sql);
    $fiyatFields = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<pre>";
    print_r($fiyatFields);
    echo "</pre>";

    echo "<h2>STOKHR Tablosu Yapısı:</h2>";
    $sql = "
        SELECT 
            TRIM(r.RDB\$FIELD_NAME) as FIELD_NAME,
            f.RDB\$FIELD_LENGTH as FIELD_LENGTH,
            CASE f.RDB\$FIELD_TYPE
                WHEN 7 THEN 'SMALLINT'
                WHEN 8 THEN 'INTEGER'
                WHEN 10 THEN 'FLOAT'
                WHEN 12 THEN 'DATE'
                WHEN 13 THEN 'TIME'
                WHEN 14 THEN 'CHAR'
                WHEN 16 THEN 'BIGINT'
                WHEN 27 THEN 'DOUBLE PRECISION'
                WHEN 35 THEN 'TIMESTAMP'
                WHEN 37 THEN 'VARCHAR'
                WHEN 261 THEN 'BLOB'
                ELSE 'UNKNOWN'
            END as FIELD_TYPE
        FROM RDB\$RELATION_FIELDS r
        LEFT JOIN RDB\$FIELDS f ON r.RDB\$FIELD_SOURCE = f.RDB\$FIELD_NAME
        WHERE r.RDB\$RELATION_NAME = 'STOKHR'
        ORDER BY r.RDB\$FIELD_POSITION
    ";

    $stmt = $wolvoxClient->getPdo()->query($sql);
    $stokhrFields = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<pre>";
    print_r($stokhrFields);
    echo "</pre>";

} catch (Exception $e) {
    echo "<h2 style='color: red;'>Hata!</h2>";
    echo "<p><strong>Hata:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

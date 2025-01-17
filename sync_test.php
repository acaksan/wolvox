<?php

require_once __DIR__ . '/vendor/autoload.php';

use Classes\WolvoxDatabaseClient;
use Classes\SyncManager;
use Automattic\WooCommerce\Client;

// Wolvox veritabanı bağlantısı
$wolvoxConfig = require __DIR__ . '/config/wolvox_config.php';
$wolvoxDb = new WolvoxDatabaseClient(
    $wolvoxConfig['host'],
    $wolvoxConfig['database'],
    $wolvoxConfig['user'],
    $wolvoxConfig['password'],
    $wolvoxConfig['company_code'],
    $wolvoxConfig['period_code']
);

// WooCommerce API bağlantısı
$wooConfig = require __DIR__ . '/config/b2c_woocommerce.php';
$woocommerce = new Client(
    $wooConfig['url'],
    $wooConfig['consumer_key'],
    $wooConfig['consumer_secret'],
    [
        'version' => 'wc/v3',
        'verify_ssl' => false
    ]
);

// Senkronizasyon yöneticisi
$syncManager = new SyncManager($wolvoxDb, $woocommerce);

try {
    // İlk 10 ürünü senkronize et
    $result = $syncManager->syncProducts(10, 0);

    echo "<h2>Senkronizasyon Sonucu:</h2>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";

} catch (Exception $e) {
    echo "<h2 style='color: red;'>Senkronizasyon Hatası!</h2>";
    echo "<p><strong>Hata:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>";
    print_r($e->getTrace());
    echo "</pre>";
}

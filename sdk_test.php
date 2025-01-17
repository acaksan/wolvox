<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/classes/WooCommerceClient.php';
require_once __DIR__ . '/classes/WolvoxSdkClient.php';
require_once __DIR__ . '/classes/SyncManager.php';

$wooConfig = require_once __DIR__ . '/config/b2c_woocommerce.php';
$wolvoxConfig = require_once __DIR__ . '/config/wolvox_sdk_config.php';

try {
    // WooCommerce istemcisini oluştur
    $wooClient = new \Classes\WooCommerceClient(
        $wooConfig['url'],
        $wooConfig['consumer_key'],
        $wooConfig['consumer_secret']
    );

    // Wolvox SDK istemcisini oluştur
    $wolvoxClient = new \Classes\WolvoxSdkClient($wolvoxConfig);

    // Test için bir ürün bilgisini al
    $product = $wolvoxClient->getProductByCode('TEST001');
    
    echo "<h2>SDK Test Sonucu:</h2>";
    echo "<pre>";
    print_r($product);
    echo "</pre>";

} catch (Exception $e) {
    echo "<h2 style='color: red;'>SDK Test Hatası!</h2>";
    echo "<p><strong>Hata:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>";
    print_r($e->getTrace());
    echo "</pre>";
}

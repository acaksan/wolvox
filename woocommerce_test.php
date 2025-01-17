<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/classes/WooCommerceClient.php';
require_once __DIR__ . '/config/b2c_woocommerce.php';

$config = require_once __DIR__ . '/config/b2c_woocommerce.php';

try {
    $wooClient = new \Classes\WooCommerceClient(
        $config['url'],
        $config['consumer_key'],
        $config['consumer_secret']
    );

    $response = $wooClient->getProducts(['per_page' => 1]);
    echo "<h2>WooCommerce Bağlantı Testi Başarılı!</h2>";
    echo "<pre>";
    print_r($response);
    echo "</pre>";
} catch (Exception $e) {
    echo "<h2 style='color: red;'>WooCommerce Bağlantı Testi Başarısız!</h2>";
    echo "<p><strong>Hata:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>";
    print_r($e->getTrace());
    echo "</pre>";
}

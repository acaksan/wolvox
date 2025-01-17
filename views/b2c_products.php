<?php

require_once __DIR__ . '/../classes/WooCommerceClient.php';
$config = require_once __DIR__ . '/../config/b2c_woocommerce.php';

$wooClient = new \Classes\WooCommerceClient(
    $config['url'],
    $config['consumer_key'],
    $config['consumer_secret']
);

try {
    $products = $wooClient->getProducts(['per_page' => 20]);

    echo "<h2>B2C Ürünleri</h2>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Ürün ID</th><th>Adı</th><th>Fiyat</th><th>Stok</th></tr>";

    foreach ($products as $product) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($product->id) . "</td>";
        echo "<td>" . htmlspecialchars($product->name) . "</td>";
        echo "<td>" . htmlspecialchars($product->price) . " TL</td>";
        echo "<td>" . htmlspecialchars($product->stock_quantity) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} catch (Exception $e) {
    echo "<p style='color: red;'>B2C Ürünleri Yüklenemedi: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

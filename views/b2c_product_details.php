<?php

require_once __DIR__ . '/../classes/WooCommerceClient.php';
$config = require_once __DIR__ . '/../config/b2c_woocommerce.php';

// WooCommerce API istemcisini başlat
$wooClient = new \Classes\WooCommerceClient(
    $config['url'],
    $config['consumer_key'],
    $config['consumer_secret']
);

// Ürün ID'sini alın
$productId = $_GET['id'] ?? null;

if (!$productId) {
    echo "<p style='color: red;'>Ürün ID'si belirtilmedi!</p>";
    exit;
}

try {
    // WooCommerce'den ürün detaylarını çek
    $product = $wooClient->get("products/$productId");

    echo "<h2>Ürün Detayları</h2>";
    echo "<p><strong>ID:</strong> " . htmlspecialchars($product->id) . "</p>";
    echo "<p><strong>Adı:</strong> " . htmlspecialchars($product->name) . "</p>";
    echo "<p><strong>Açıklama:</strong> " . htmlspecialchars($product->description) . "</p>";
    echo "<p><strong>Fiyat:</strong> " . htmlspecialchars($product->price) . " TL</p>";
    echo "<p><strong>Stok:</strong> " . htmlspecialchars($product->stock_quantity) . "</p>";
    echo "<p><strong>Kategori:</strong> ";
    foreach ($product->categories as $category) {
        echo htmlspecialchars($category->name) . ", ";
    }
    echo "</p>";

    echo "<a href='index.php?page=b2c_products'>Geri Dön</a>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Ürün Detayları Yüklenemedi: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

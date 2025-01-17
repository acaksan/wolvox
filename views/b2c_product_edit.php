<?php

require_once __DIR__ . '/../classes/WooCommerceClient.php';
$config = require_once __DIR__ . '/../config/b2c_woocommerce.php';

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

// POST isteği kontrolü
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedData = [
        'name' => $_POST['name'],
        'price' => $_POST['price'],
        'stock_quantity' => $_POST['stock_quantity'],
    ];

    try {
        $wooClient->put("products/$productId", $updatedData);
        echo "<p style='color: green;'>Ürün başarıyla güncellendi!</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>Ürün güncellenemedi: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

try {
    // Ürün detaylarını çek
    $product = $wooClient->get("products/$productId");
} catch (Exception $e) {
    echo "<p style='color: red;'>Ürün bilgileri yüklenemedi: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

?>

<h2>Ürün Düzenleme</h2>
<form method="post">
    <p>Ad: <input type="text" name="name" value="<?php echo htmlspecialchars($product->name); ?>"></p>
    <p>Fiyat: <input type="text" name="price" value="<?php echo htmlspecialchars($product->price); ?>"></p>
    <p>Stok: <input type="text" name="stock_quantity" value="<?php echo htmlspecialchars($product->stock_quantity); ?>"></p>
    <button type="submit">Güncelle</button>
</form>
<a href="index.php?page=b2c_products">Geri Dön</a>

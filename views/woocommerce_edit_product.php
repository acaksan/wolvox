<?php

require_once __DIR__ . '/../classes/WooCommerceClient.php';
require_once __DIR__ . '/../config/database.php';

$config = require __DIR__ . '/../config/database.php';

// WooCommerce API istemcisi
$wooClient = new \Classes\WooCommerceClient(
    $config['woocommerce']['url'],
    $config['woocommerce']['consumer_key'],
    $config['woocommerce']['consumer_secret']
);

$product = null;
$message = null;

// Ürün ID'si alınır
$productId = $_GET['id'] ?? null;

if ($productId) {
    try {
        // Ürün bilgilerini API'den al
        $product = $wooClient->getProducts(['include' => [$productId]])[0];
    } catch (Exception $e) {
        $message = "Hata: Ürün bilgisi alınamadı. " . $e->getMessage();
    }
}

// Ürün güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedProduct = [
        'name' => $_POST['name'],
        'regular_price' => $_POST['regular_price'],
        'stock_quantity' => $_POST['stock_quantity'],
    ];

    try {
        $wooClient->updateData('products', $productId, $updatedProduct);
        $message = "Ürün başarıyla güncellendi!";
        // Güncellenen ürünü yeniden al
        $product = $wooClient->getProducts(['include' => [$productId]])[0];
    } catch (Exception $e) {
        $message = "Hata: Ürün güncellenemedi. " . $e->getMessage();
    }
}

?>

<h2>WooCommerce Ürün Güncelleme</h2>

<?php if ($message): ?>
    <p style="color: green;"><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<?php if ($product): ?>
    <form method="POST" action="">
        <label for="name">Ürün Adı:</label><br>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($product->name); ?>" required><br><br>

        <label for="regular_price">Fiyat:</label><br>
        <input type="text" name="regular_price" id="regular_price" value="<?php echo htmlspecialchars($product->regular_price); ?>" required><br><br>

        <label for="stock_quantity">Stok Miktarı:</label><br>
        <input type="number" name="stock_quantity" id="stock_quantity" value="<?php echo htmlspecialchars($product->stock_quantity); ?>" required><br><br>

        <button type="submit">Güncelle</button>
    </form>
<?php else: ?>
    <p>Ürün bulunamadı!</p>
<?php endif; ?>

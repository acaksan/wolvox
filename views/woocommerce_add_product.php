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

$message = null;

// Ürün ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newProduct = [
        'name' => $_POST['name'],
        'regular_price' => $_POST['regular_price'],
        'stock_quantity' => $_POST['stock_quantity'],
        'description' => $_POST['description'],
        'categories' => isset($_POST['categories']) ? array_map(function ($category) {
            return ['id' => (int) $category];
        }, $_POST['categories']) : [],
        'images' => isset($_POST['images']) ? array_map(function ($url) {
            return ['src' => $url];
        }, explode(',', $_POST['images'])) : [],
    ];

    try {
        $wooClient->postData('products', $newProduct);
        $message = "Ürün başarıyla eklendi!";
    } catch (Exception $e) {
        $message = "Hata: Ürün eklenemedi. " . $e->getMessage();
    }
}

?>

<h2>WooCommerce Yeni Ürün Ekle</h2>

<?php if ($message): ?>
    <p style="color: green;"><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label for="name">Ürün Adı:</label><br>
    <input type="text" name="name" id="name" required><br><br>

    <label for="regular_price">Fiyat:</label><br>
    <input type="text" name="regular_price" id="regular_price" required><br><br>

    <label for="stock_quantity">Stok Miktarı:</label><br>
    <input type="number" name="stock_quantity" id="stock_quantity" required><br><br>

    <label for="description">Açıklama:</label><br>
    <textarea name="description" id="description" rows="4" cols="50"></textarea><br><br>

    <label for="categories">Kategoriler (ID olarak, virgülle ayırın):</label><br>
    <input type="text" name="categories" id="categories" placeholder="1,2,3"><br><br>

    <label for="images">Resim URL'leri (virgülle ayırın):</label><br>
    <input type="text" name="images" id="images" placeholder="https://example.com/image1.jpg,https://example.com/image2.jpg"><br><br>

    <button type="submit">Ekle</button>
</form>

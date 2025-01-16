<?php
require_once __DIR__ . '/../classes/Database.php';
$config = require_once __DIR__ . '/../config/database.php';

$products = []; // Varsayılan boş dizi
$exampleProductCode = 'PET-100-70-13-175-6050'; // Örnek stok kodu
$exampleProduct = []; // Varsayılan boş dizi

try {
    $db = new Database();
    $firebird = $db->connectToFirebird($config['firebird']);

    // Aktif ve webde görünecek ürünleri çek
    $query = $firebird->query("SELECT STOKKODU, STOK_ADI FROM STOK WHERE WEBDE_GORUNSUN = 1 AND AKTIF = 1");
    if ($query) {
        $products = $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // Örnek ürün detaylarını çek
    $exampleQuery = $firebird->prepare("SELECT * FROM STOK WHERE STOKKODU = ?");
    $exampleQuery->execute([$exampleProductCode]);
    $exampleProduct = $exampleQuery->fetch(PDO::FETCH_ASSOC) ?: [];
} catch (PDOException $e) {
    echo "Hata: " . $e->getMessage();
}
?>

<h2>Wolvox Ürün Seçimi</h2>

<!-- Ürün Listesi -->
<h3>Ürün Listesi</h3>
<table border="1">
    <tr>
        <th>Stok Kodu</th>
        <th>Stok Adı</th>
    </tr>
    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?php echo htmlspecialchars($product['STOKKODU']); ?></td>
                <td><?php echo htmlspecialchars($product['STOK_ADI']); ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="2">Ürün bulunamadı.</td>
        </tr>
    <?php endif; ?>
</table>

<!-- Örnek Ürün Detayları -->
<h3>Örnek Ürün Detayları: <?php echo htmlspecialchars($exampleProductCode); ?></h3>
<table border="1">
    <?php if (!empty($exampleProduct)): ?>
        <?php foreach ($exampleProduct as $key => $value): ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($key); ?></strong></td>
                <td><?php echo htmlspecialchars($value); ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="2">Örnek ürün bulunamadı.</td>
        </tr>
    <?php endif; ?>
</table>

<!-- WooCommerce'e Gönderilecek Özellikler -->
<h3>WooCommerce'e Gönderilecek Özellikler</h3>
<form method="POST" action="../controllers/ProductController.php">
    <?php if (!empty($exampleProduct)): ?>
        <?php foreach ($exampleProduct as $key => $value): ?>
            <label>
                <input type="checkbox" name="woocommerce_features[]" value="<?php echo htmlspecialchars($key); ?>">
                <?php echo htmlspecialchars($key); ?>
            </label><br>
        <?php endforeach; ?>
        <button type="submit">Seçilen Özellikleri Gönder</button>
    <?php else: ?>
        <p>Gösterilecek özellik yok.</p>
    <?php endif; ?>
</form>

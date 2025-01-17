<?php
require_once __DIR__ . '/../classes/Database.php';
$config = require_once __DIR__ . '/../config/database.php';

$products = []; // Varsayılan değer

try {
    $db = new Database();
    $firebird = $db->connectToFirebird($config['firebird']);

    // Sorgu: Sadece aktif ve webde görünen ürünleri çek
    $query = $firebird->query("SELECT STOKKODU, STOK_ADI FROM STOK WHERE WEBDE_GORUNSUN = 1 AND AKTIF = 1");
    $products = $query->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Hata mesajını göster
    echo "Hata: " . $e->getMessage();
}

?>

<h2>Ürünler</h2>
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

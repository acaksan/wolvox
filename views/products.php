<?php
require_once __DIR__ . '/../classes/Database.php';
$configFile = __DIR__ . '/../config/database.php';

// Varsayılan boş ürün listesi
$products = [];

// Bağlantı bilgileri kontrolü
if (!file_exists($configFile)) {
    die("Hata: Bağlantı ayar dosyası bulunamadı.");
}

$config = require $configFile;

if (empty($config['firebird']) || empty($config['firebird']['host']) || empty($config['firebird']['database']) || empty($config['firebird']['user']) || empty($config['firebird']['password'])) {
    die("Hata: Firebird bağlantı bilgileri eksik.");
}

try {
    // Firebird bağlantısını kur
    $db = new Database();
    $firebird = $db->connectToFirebird($config['firebird']);

    // Sorguyu çalıştır ve sonuçları al
    $query = $firebird->query("SELECT STOKKODU, STOK_ADI FROM STOK WHERE WEBDE_GORUNSUN = 1 AND AKTIF = 1");

    if ($query) {
        $products = $query->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo "Hata: Ürün sorgusu başarısız.";
    }
} catch (PDOException $e) {
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

<?php
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../config/database.php';

// Mapping konfigürasyon dosyası
$configFile = __DIR__ . '/../config/mapping.php';
$mappingConfig = file_exists($configFile) ? require $configFile : [];

// Örnek ürün kodu
$exampleProductCode = 'PET-100-70-13-175-6050';
$exampleProduct = [];

// Firebird bağlantısı
try {
    $db = new Database();
    $firebird = $db->connectToFirebird($config['firebird']);

    // Örnek ürünü al
    $exampleQuery = $firebird->prepare("SELECT * FROM STOK WHERE STOKKODU = ?");
    $exampleQuery->execute([$exampleProductCode]);
    $exampleProduct = $exampleQuery->fetch(PDO::FETCH_ASSOC) ?: [];
} catch (PDOException $e) {
    echo "Firebird bağlantı hatası: " . $e->getMessage();
}

// WooCommerce alanları
$woocommerceFields = [
    'name' => 'Ürün Adı',
    'slug' => 'URL Slug',
    'type' => 'Ürün Türü',
    'status' => 'Durum (Yayınlanmış, Taslak)',
    'description' => 'Açıklama',
    'short_description' => 'Kısa Açıklama',
    'sku' => 'Stok Kodu',
    'regular_price' => 'Satış Fiyatı',
    'sale_price' => 'İndirimli Fiyat',
    'stock_quantity' => 'Stok Miktarı',
    'manage_stock' => 'Stok Yönetimi (Evet/Hayır)',
    'stock_status' => 'Stok Durumu',
    'categories' => 'Kategoriler',
    'tags' => 'Etiketler',
    'images' => 'Resim URL',
    'dimensions' => 'Boyutlar (Uzunluk, Genişlik, Yükseklik)',
    'weight' => 'Ağırlık',
    'shipping_class' => 'Kargo Sınıfı',
    'attributes' => 'Özellikler',
    'price' => 'Fiyat',
];

// Eşleştirme ayarlarını kaydet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_mapping'])) {
    $mapping = $_POST['mapping'] ?? [];
    file_put_contents($configFile, "<?php\nreturn " . var_export($mapping, true) . ";\n");
    echo "<p style='color: green;'>Eşleştirme ayarları başarıyla kaydedildi!</p>";
}

// Güvenli HTML çıktı fonksiyonu
function safeHtml($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<h2>WooCommerce Eşleştirme Ayarları</h2>

<form method="POST" action="">
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Wolvox Alanı</th>
            <th>Örnek Değer</th>
            <th>WooCommerce Alanı</th>
        </tr>

        <?php if (!empty($exampleProduct)): ?>
            <?php foreach ($exampleProduct as $key => $value): ?>
                <tr>
                    <td><?php echo safeHtml($key); ?></td>
                    <td><?php echo safeHtml($value); ?></td>
                    <td>
                        <select name="mapping[<?php echo safeHtml($key); ?>]">
                            <option value="">-- Eşleştirilecek Alanı Seçin --</option>
                            <?php foreach ($woocommerceFields as $wcKey => $wcLabel): ?>
                                <option value="<?php echo safeHtml($wcKey); ?>" 
                                    <?php echo (isset($mappingConfig[$key]) && $mappingConfig[$key] === $wcKey) ? 'selected' : ''; ?>>
                                    <?php echo safeHtml($wcLabel); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">Örnek ürün bulunamadı.</td>
            </tr>
        <?php endif; ?>
    </table>
    <br>
    <button type="submit" name="save_mapping">Eşleştirme Ayarlarını Kaydet</button>
</form>

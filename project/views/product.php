<?php
require_once __DIR__ . '/../classes/Database.php';

// Wolvox veritabanı bağlantısını başlat
$config = require_once __DIR__ . '/../config/database.php';
$db = new Database();
$firebird = $db->connectToFirebird($config['firebird']);

// Webde görünecek ve aktif olan ürünleri sorgula
$query = $firebird->query("SELECT STOK_KODU, STOK_ADI, WEBDE_GORUNSUN, AKTIF FROM STOKLAR WHERE WEBDE_GORUNSUN = 1 AND AKTIF = 1");
$products = $query->fetchAll(PDO::FETCH_ASSOC);

// Örnek bir ürün detaylarını çek
$exampleProductQuery = $firebird->prepare("SELECT * FROM STOKLAR WHERE STOK_KODU = ?");
$exampleProductQuery->execute(['PET-100-70-13-175-6050']);
$exampleProduct = $exampleProductQuery->fetch(PDO::FETCH_ASSOC);
?>

<h2>Ürün Yönetimi</h2>

<!-- Webde görünecek ve aktif ürünlerin listesi -->
<h3>Webde Görünecek Ürünler</h3>
<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>Stok Kodu</th>
            <th>Stok Adı</th>
            <th>Webde Görünsün</th>
            <th>Aktif</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?php echo $product['STOK_KODU']; ?></td>
                <td><?php echo $product['STOK_ADI']; ?></td>
                <td><?php echo $product['WEBDE_GORUNSUN'] ? 'Evet' : 'Hayır'; ?></td>
                <td><?php echo $product['AKTIF'] ? 'Evet' : 'Hayır'; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<hr>

<!-- Örnek ürün detayları -->
<h3>Örnek Ürün Detayları (Stok Kodu: PET-100-70-13-175-6050)</h3>
<?php if ($exampleProduct): ?>
    <table border="1" cellpadding="10">
        <tr>
            <th>Stok Kodu</th>
            <td><?php echo $exampleProduct['STOK_KODU']; ?></td>
        </tr>
        <tr>
            <th>Stok Adı</th>
            <td><?php echo $exampleProduct['STOK_ADI']; ?></td>
        </tr>
        <tr>
            <th>Fiyat</th>
            <td><?php echo $exampleProduct['FIYAT']; ?></td>
        </tr>
        <tr>
            <th>KDV Oranı</th>
            <td><?php echo $exampleProduct['KDV_ORANI']; ?></td>
        </tr>
        <tr>
            <th>Açıklama</th>
            <td><?php echo $exampleProduct['ACIKLAMA']; ?></td>
        </tr>
    </table>
<?php else: ?>
    <p>Örnek ürün bulunamadı!</p>
<?php endif; ?>

<hr>

<!-- WooCommerce'e Gönderilecek Özelliklerin Seçimi -->
<h3>WooCommerce'e Gönderilecek Özellikler</h3>
<form method="POST" action="controllers/ProductsController.php">
    <input type="hidden" name="stock_code" value="PET-100-70-13-175-6050">
    <label><input type="checkbox" name="features[]" value="STOK_ADI" checked> Stok Adı</label><br>
    <label><input type="checkbox" name="features[]" value="FIYAT" checked> Fiyat</label><br>
    <label><input type="checkbox" name="features[]" value="KDV_ORANI"> KDV Oranı</label><br>
    <label><input type="checkbox" name="features[]" value="ACIKLAMA"> Açıklama</label><br>
    <button type="submit" name="save_features">Özellikleri Kaydet</button>
</form>

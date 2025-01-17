<?php
// UTF-8 karakter kodlaması
header('Content-Type: text/html; charset=utf-8');

// Composer autoload dosyasını dahil et (WooCommerce SDK için gerekli)
require_once __DIR__ . '/vendor/autoload.php';

// Sınıflar ve genel yapı dosyalarını dahil et
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Helpers.php';
require_once __DIR__ . '/classes/WooCommerceClient.php';

// Gelen `page` parametresine göre sayfa belirleme (varsayılan: 'home')
$page = $_GET['page'] ?? 'home';

// Dinamik menü oluşturma fonksiyonu
function renderMenu() {
    $menuItems = [
        'home' => 'Anasayfa',
        'b2c_products' => 'B2C Ürün Yönetimi',
        'b2c_product_details' => 'Ürün Detayları', // Dinamik, genelde linkle gidilir
        'b2c_product_edit' => 'Ürün Düzenle',      // Dinamik, genelde linkle gidilir
        'woocommerce_orders' => 'WooCommerce Siparişleri',
        'wolvox' => 'Wolvox Entegrasyonu',
    ];

    echo '<nav class="navbar navbar-expand-lg navbar-dark bg-dark">';
    echo '<div class="container">';
    echo '<a class="navbar-brand" href="index.php">Wolvox Entegrasyon</a>';
    echo '<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">';
    echo '<span class="navbar-toggler-icon"></span>';
    echo '</button>';
    echo '<div class="collapse navbar-collapse" id="navbarNav">';
    echo '<ul class="navbar-nav">';
    foreach ($menuItems as $key => $label) {
        $active = ($page === $key) ? ' active' : '';
        echo "<li class='nav-item'><a class='nav-link$active' href='index.php?page=$key'>$label</a></li>";
    }
    echo '</ul>';
    echo '</div>';
    echo '</div>';
    echo '</nav>';
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wolvox Entegrasyon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body>
<?php
// Dinamik sayfa yönlendirme
if ($page === 'home') {
    // Anasayfa
    renderMenu();
    echo '<div class="container mt-4">';
    echo "<h1>Yönetim Paneline Hoş Geldiniz</h1>";
    echo "<p>B2C işlemleri için yukarıdaki menüyü kullanabilirsiniz.</p>";
    echo '</div>';
} elseif ($page === 'b2c_products') {
    // B2C Ürün Yönetimi
    renderMenu();
    include __DIR__ . '/views/b2c_products.php';
} elseif ($page === 'b2c_product_details') {
    // B2C Ürün Detayları
    renderMenu();
    include __DIR__ . '/views/b2c_product_details.php';
} elseif ($page === 'b2c_product_edit') {
    // B2C Ürün Düzenleme
    renderMenu();
    include __DIR__ . '/views/b2c_product_edit.php';
} elseif ($page === 'woocommerce_orders') {
    // WooCommerce Siparişleri
    renderMenu();
    include __DIR__ . '/views/woocommerce_orders.php';
} elseif ($page === 'wolvox') {
    // Wolvox Entegrasyonu
    renderMenu();
    include __DIR__ . '/views/wolvox.php';
} else {
    // 404 - Sayfa bulunamadı
    renderMenu();
    http_response_code(404);
    echo '<div class="container mt-4">';
    echo "<h2>404 - Sayfa Bulunamadı!</h2>";
    echo '</div>';
}
?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
</body>
</html>
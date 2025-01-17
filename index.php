<?php
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

    echo '<nav>';
    foreach ($menuItems as $key => $label) {
        echo "<a href='index.php?page=$key'>$label</a> | ";
    }
    echo '</nav>';
    echo '<hr>';
}

// Dinamik sayfa yönlendirme
if ($page === 'home') {
    // Anasayfa
    renderMenu();
    echo "<h1>Yönetim Paneline Hoş Geldiniz</h1>";
    echo "<p>B2C işlemleri için yukarıdaki menüyü kullanabilirsiniz.</p>";
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
    echo "<h2>404 - Sayfa Bulunamadı!</h2>";
}

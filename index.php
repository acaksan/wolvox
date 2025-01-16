<?php
// Konfigürasyon dosyalarını ve sınıfları dahil et
require_once __DIR__ . '/classes/Database.php';
$config = require_once __DIR__ . '/config/database.php';

// Gelen `page` parametresine göre sayfa belirleme
$page = $_GET['page'] ?? 'home';

// Genel header
function renderMenu() {
    echo '<nav>';
    echo '<a href="index.php?page=home">Anasayfa</a> | ';
    echo '<a href="index.php?page=settings">Ayarlar</a> | ';
    echo '<a href="index.php?page=products">Ürünler</a> | ';
    echo '<a href="index.php?page=product_selection">Ürün Seçimi</a>';
    echo '</nav>';
    echo '<hr>';
}

// Sayfa içeriklerini yönlendirme
if ($page === 'home') {
    renderMenu();
    include __DIR__ . '/views/home.php'; // Anasayfa
} elseif ($page === 'settings') {
    renderMenu();
    include __DIR__ . '/views/settings.php'; // Ayarlar Sayfası
} elseif ($page === 'products') {
    renderMenu();
    include __DIR__ . '/views/products.php'; // Ürünler Sayfası
} elseif ($page === 'product_selection') {
    renderMenu();
    include __DIR__ . '/views/product_selection.php'; // Ürün Seçimi Sayfası
} else {
    renderMenu();
    // 404 Hatası için çıktı
    http_response_code(404);
    echo "<h2>404 - Sayfa Bulunamadı!</h2>";
}

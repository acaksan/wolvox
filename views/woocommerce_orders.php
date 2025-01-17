<?php

require_once __DIR__ . '/../classes/WooCommerceClient.php';
require_once __DIR__ . '/../config/woocommerce.php';

// WooCommerce API yapılandırması
$config = require __DIR__ . '/../config/woocommerce.php';
$wooClient = new \Classes\WooCommerceClient(
    $config['url'],
    $config['consumer_key'],
    $config['consumer_secret']
);

try {
    // WooCommerce'den siparişleri al (İlk 20 siparişi alıyoruz)
    $orders = $wooClient->getOrders(['per_page' => 20]);

    // Gelen veriyi kontrol et
    if (!is_array($orders)) {
        echo "<p style='color: red;'>WooCommerce API'den dönen veri beklenen formatta değil.</p>";
        var_dump($orders); // Hata ayıklama için veriyi inceleyin
        exit;
    }

    echo "<h2>WooCommerce Siparişleri</h2>";

    // Siparişleri tablo halinde göster
    echo "<table border='1' cellpadding='5'>";
    echo "<tr>
            <th>Sipariş ID</th>
            <th>Tarih</th>
            <th>Müşteri</th>
            <th>Toplam</th>
            <th>Durum</th>
            <th>Detaylar</th>
          </tr>";

    foreach ($orders as $order) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($order->id) . "</td>";
        echo "<td>" . htmlspecialchars($order->date_created) . "</td>";
        echo "<td>" . htmlspecialchars($order->billing->first_name . ' ' . $order->billing->last_name) . "</td>";
        echo "<td>" . htmlspecialchars($order->total) . " " . htmlspecialchars($order->currency) . "</td>";
        echo "<td>" . htmlspecialchars($order->status) . "</td>";
        echo "<td><a href='index.php?page=woocommerce_order_details&id=" . htmlspecialchars($order->id) . "'>Gör</a></td>";
        echo "</tr>";
    }

    echo "</table>";
} catch (Exception $e) {
    // Hata mesajını göster
    echo "<p style='color: red;'>WooCommerce Siparişleri Yüklenemedi: " . htmlspecialchars($e->getMessage()) . "</p>";
}

?>

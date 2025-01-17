<?php

require_once __DIR__ . '/../classes/WooCommerceClient.php';
require_once __DIR__ . '/../config/woocommerce.php';

$config = require __DIR__ . '/../config/woocommerce.php';
$wooClient = new \Classes\WooCommerceClient(
    $config['url'],
    $config['consumer_key'],
    $config['consumer_secret']
);

$orderId = $_GET['id'] ?? null;

if (!$orderId) {
    echo "<p style='color: red;'>Sipariş ID belirtilmedi!</p>";
    exit;
}

try {
    // Sipariş detaylarını çek
    $order = $wooClient->getOrders(['include' => [$orderId]])[0];

    echo "<h2>Sipariş Detayları (ID: " . htmlspecialchars($order->id) . ")</h2>";
    echo "<p><strong>Tarih:</strong> " . htmlspecialchars($order->date_created) . "</p>";
    echo "<p><strong>Müşteri:</strong> " . htmlspecialchars($order->billing->first_name . ' ' . $order->billing->last_name) . "</p>";
    echo "<p><strong>Toplam:</strong> " . htmlspecialchars($order->total) . " " . htmlspecialchars($order->currency) . "</p>";
    echo "<p><strong>Durum:</strong> " . htmlspecialchars($order->status) . "</p>";

    echo "<h3>Ürünler</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Ürün Adı</th><th>Miktar</th><th>Fiyat</th></tr>";
    foreach ($order->line_items as $item) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($item->name) . "</td>";
        echo "<td>" . htmlspecialchars($item->quantity) . "</td>";
        echo "<td>" . htmlspecialchars($item->total) . "</td>";
        echo "</tr>";
    }
    echo "</table>";

} catch (Exception $e) {
    echo "<p style='color: red;'>Sipariş Detayları Yüklenemedi: " . htmlspecialchars($e->getMessage()) . "</p>";
}

?>

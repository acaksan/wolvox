<?php

require_once __DIR__ . '/../classes/WooCommerceClient.php';
require_once __DIR__ . '/../config/database.php';

$config = require __DIR__ . '/../config/database.php';

// WooCommerce API istemcisi
$wooClient = new \Classes\WooCommerceClient(
    $config['woocommerce']['url'],
    $config['woocommerce']['consumer_key'],
    $config['woocommerce']['consumer_secret']
);

// WooCommerce ürünlerini getir
try {
    $products = $wooClient->getProducts(['per_page' => 20]); // İlk 20 ürünü al
    echo "<h2>WooCommerce Ürünleri</h2>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr>
            <th>ID</th>
            <th>Ad</th>
            <th>Fiyat</th>
            <th>Stok</th>
            <th>Kategoriler</th>
            <th>Resimler</th>
            <th>Açıklama</th>
            <th>Durum</th>
            <th>Tür</th>
            <th>Düzenle</th>
          </tr>";

    foreach ($products as $product) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($product->id) . "</td>";
        echo "<td>" . htmlspecialchars($product->name) . "</td>";
        echo "<td>" . htmlspecialchars($product->regular_price ?? '0.00') . "</td>";
        echo "<td>" . htmlspecialchars($product->stock_quantity ?? '0') . "</td>";

        // Kategorileri listele
        if (!empty($product->categories)) {
            $categories = array_map(function ($category) {
                return $category->name;
            }, $product->categories);
            echo "<td>" . htmlspecialchars(implode(', ', $categories)) . "</td>";
        } else {
            echo "<td>---</td>";
        }

        // Resimleri listele
        if (!empty($product->images)) {
            echo "<td>";
            foreach ($product->images as $image) {
                echo "<img src='" . htmlspecialchars($image->src) . "' alt='' style='width: 50px; height: 50px; margin-right: 5px;'>";
            }
            echo "</td>";
        } else {
            echo "<td>---</td>";
        }

        echo "<td>" . htmlspecialchars(strip_tags($product->description ?? '')) . "</td>"; // HTML etiketi temizlenmiş açıklama
        echo "<td>" . htmlspecialchars($product->status) . "</td>";
        echo "<td>" . htmlspecialchars($product->type) . "</td>";

        // Düzenleme linki
        echo "<td><a href='index.php?page=woocommerce_edit_product&id=" . htmlspecialchars($product->id) . "'>Düzenle</a></td>";
        echo "</tr>";
    }

    echo "</table>";
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}

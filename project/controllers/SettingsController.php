<?php
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../vendor/autoload.php';

$config = require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Wolvox Bağlantısını Test Et
    if (isset($_POST['test_firebird'])) {
        try {
            $db = new Database();
            $firebird = $db->connectToFirebird($config['firebird']);
            echo "<p>Wolvox (Firebird) bağlantısı başarılı!</p>";
        } catch (PDOException $e) {
            echo "<p>Wolvox (Firebird) bağlantı hatası: " . $e->getMessage() . "</p>";
        }
    }

    // WooCommerce Bağlantısını Test Et
    if (isset($_POST['test_woocommerce'])) {
        try {
            $woocommerce = new Automattic\WooCommerce\Client(
                $config['woocommerce']['url'],
                $config['woocommerce']['consumer_key'],
                $config['woocommerce']['consumer_secret'],
                ['version' => $config['woocommerce']['version']]
            );

            // WooCommerce'den örnek bir API isteği (örneğin mağaza bilgisi çekme)
            $storeInfo = $woocommerce->get('');
            echo "<p>WooCommerce bağlantısı başarılı!</p>";
        } catch (Exception $e) {
            echo "<p>WooCommerce bağlantı hatası: " . $e->getMessage() . "</p>";
        }
    }
}
 

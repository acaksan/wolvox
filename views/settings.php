<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/WooCommerceIntegration.php';

$configFile = __DIR__ . '/../config/database.php';

$config = file_exists($configFile) ? require $configFile : [];

$firebirdConfig = $config['firebird'] ?? [];
$woocommerceConfig = $config['woocommerce'] ?? [];

$firebirdTestMessage = '';
$woocommerceTestMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_firebird'])) {
    try {
        $db = new Database();
        $firebird = $db->connectToFirebird($firebirdConfig);
        $firebirdTestMessage = "Firebird bağlantısı başarılı!";
    } catch (PDOException $e) {
        $firebirdTestMessage = "Firebird bağlantı hatası: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_woocommerce'])) {
    try {
        $woocommerce = new WooCommerceIntegration(
            $woocommerceConfig['url'],
            $woocommerceConfig['consumer_key'],
            $woocommerceConfig['consumer_secret']
        );
        $products = $woocommerce->getProducts();
        $woocommerceTestMessage = "WooCommerce bağlantısı başarılı!";
    } catch (Exception $e) {
        $woocommerceTestMessage = "WooCommerce bağlantı hatası: " . $e->getMessage();
    }
}
?>
<h2>Ayarlar</h2>
<form method="POST" action="">
    <h3>Firebird Ayarları</h3>
    <label>Host:</label><input type="text" name="firebird_host" value="<?php echo htmlspecialchars($firebirdConfig['host'] ?? ''); ?>"><br>
    <label>Database:</label><input type="text" name="firebird_database" value="<?php echo htmlspecialchars($firebirdConfig['database'] ?? ''); ?>"><br>
    <label>Kullanıcı Adı:</label><input type="text" name="firebird_user" value="<?php echo htmlspecialchars($firebirdConfig['user'] ?? ''); ?>"><br>
    <label>Şifre:</label><input type="password" name="firebird_password" value="<?php echo htmlspecialchars($firebirdConfig['password'] ?? ''); ?>"><br>
    <button type="submit" name="test_firebird">Wolvox Bağlantısını Test Et</button>
    <p><?php echo $firebirdTestMessage; ?></p>

    <h3>WooCommerce Ayarları</h3>
    <label>Site URL:</label><input type="text" name="woocommerce_url" value="<?php echo htmlspecialchars($woocommerceConfig['url'] ?? ''); ?>"><br>
    <label>Tüketici Anahtar:</label><input type="text" name="woocommerce_consumer_key" value="<?php echo htmlspecialchars($woocommerceConfig['consumer_key'] ?? ''); ?>"><br>
    <label>Tüketici Gizli Anahtarı:</label><input type="text" name="woocommerce_consumer_secret" value="<?php echo htmlspecialchars($woocommerceConfig['consumer_secret'] ?? ''); ?>"><br>
    <button type="submit" name="test_woocommerce">WooCommerce Bağlantısını Test Et</button>
    <p><?php echo $woocommerceTestMessage; ?></p>
</form>

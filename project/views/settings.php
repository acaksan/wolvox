<?php
require_once __DIR__ . '/../classes/Database.php';
$configFile = __DIR__ . '/../config/database.php';

// Varsayılan değerler
$host = '';
$database = '';
$user = '';
$password = '';
$charset = '';
$port = '';
$woocommerceUrl = '';
$consumerKey = '';
$consumerSecret = '';
$apiVersion = '';

// Config dosyasını yükleme
if (file_exists($configFile)) {
    $config = require $configFile;
    $host = $config['firebird']['host'] ?? '';
    $database = $config['firebird']['database'] ?? '';
    $user = $config['firebird']['user'] ?? '';
    $password = $config['firebird']['password'] ?? '';
    $charset = $config['firebird']['charset'] ?? '';
    $port = $config['firebird']['port'] ?? '';

    $woocommerceUrl = $config['woocommerce']['url'] ?? '';
    $consumerKey = $config['woocommerce']['consumer_key'] ?? '';
    $consumerSecret = $config['woocommerce']['consumer_secret'] ?? '';
    $apiVersion = $config['woocommerce']['version'] ?? '';
}

// Bağlantı testleri
$firebirdTestMessage = '';
$woocommerceTestMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_firebird'])) {
    try {
        $db = new Database();
        $firebird = $db->connectToFirebird($config['firebird']);
        $firebirdTestMessage = "Firebird bağlantısı başarılı!";
    } catch (PDOException $e) {
        $firebirdTestMessage = "Firebird bağlantı hatası: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_woocommerce'])) {
    try {
        $woocommerce = new Automattic\WooCommerce\Client(
            $woocommerceUrl,
            $consumerKey,
            $consumerSecret,
            ['version' => $apiVersion]
        );
        $woocommerceTestMessage = "WooCommerce bağlantısı başarılı!";
    } catch (Exception $e) {
        $woocommerceTestMessage = "WooCommerce bağlantı hatası: " . $e->getMessage();
    }
}
?>

<h2>Ayarlar</h2>

<!-- Firebird Ayarları -->
<h3>Firebird (Wolvox) Bağlantı Ayarları</h3>
<form method="POST" action="">
    <label>Host:</label>
    <input type="text" name="firebird_host" value="<?php echo htmlspecialchars($host); ?>"><br>
    <label>Database:</label>
    <input type="text" name="firebird_database" value="<?php echo htmlspecialchars($database); ?>"><br>
    <label>Kullanıcı Adı:</label>
    <input type="text" name="firebird_user" value="<?php echo htmlspecialchars($user); ?>"><br>
    <label>Şifre:</label>
    <input type="password" name="firebird_password" value="<?php echo htmlspecialchars($password); ?>"><br>
    <label>Charset:</label>
    <input type="text" name="firebird_charset" value="<?php echo htmlspecialchars($charset); ?>"><br>
    <label>Port:</label>
    <input type="text" name="firebird_port" value="<?php echo htmlspecialchars($port); ?>"><br>
    <button type="submit" name="test_firebird">Wolvox Bağlantısını Test Et</button>
    <p><?php echo $firebirdTestMessage; ?></p>
</form>

<!-- WooCommerce Ayarları -->
<h3>WooCommerce API Bağlantı Ayarları</h3>
<form method="POST" action="">
    <label>Site URL:</label>
    <input type="text" name="woocommerce_url" value="<?php echo htmlspecialchars($woocommerceUrl); ?>"><br>
    <label>Tüketici Anahtar:</label>
    <input type="text" name="woocommerce_consumer_key" value="<?php echo htmlspecialchars($consumerKey); ?>"><br>
    <label>Tüketici Gizli Anahtarı:</label>
    <input type="text" name="woocommerce_consumer_secret" value="<?php echo htmlspecialchars($consumerSecret); ?>"><br>
    <label>API Versiyonu:</label>
    <input type="text" name="woocommerce_version" value="<?php echo htmlspecialchars($apiVersion); ?>"><br>
    <button type="submit" name="test_woocommerce">WooCommerce Bağlantısını Test Et</button>
    <p><?php echo $woocommerceTestMessage; ?></p>
</form>

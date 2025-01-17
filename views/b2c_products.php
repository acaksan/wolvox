<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Classes\Database;

function getProducts($limit = null) {
    $wolvoxConfig = require __DIR__ . '/../config/wolvox_config.php';
    
    $db = new Database();
    $pdo = $db->connectToFirebird($wolvoxConfig);

    // STOK_FIYAT tablosundan liste fiyatını al
    $sql = "
        SELECT 
            s.STOKKODU,
            s.STOK_ADI,
            s.GRUBU,
            s.MARKASI,
            s.MODELI,
            s.KDV_ORANI,
            COALESCE(f.FIYATI, 0) as LISTE_FIYATI
        FROM STOK s
        LEFT JOIN STOK_FIYAT f ON s.BLKODU = f.BLSTKODU AND f.TANIMI = 'SATIS FİYATI -1'
        WHERE s.WEBDE_GORUNSUN = 1 
        AND s.AKTIF = 1
    ";

    if ($limit) {
        $sql .= " FETCH FIRST $limit ROWS ONLY";
    }

    try {
        $stmt = $pdo->query($sql);
        $products = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Platform bazlı iskonto oranları
            $platformIskontoOranlari = [
                'WOOCOMMERCE' => 10, // %10 iskonto
                'TRENDYOL' => 15,    // %15 iskonto
                'N11' => 12,         // %12 iskonto
            ];

            // WooCommerce için iskontolu fiyat hesapla
            $listeFiyati = $row['LISTE_FIYATI'];
            $iskontoOrani = $platformIskontoOranlari['WOOCOMMERCE'];
            $satisFiyati = $listeFiyati * (1 - ($iskontoOrani / 100));

            $products[] = [
                'id' => $row['STOKKODU'],
                'name' => $row['STOK_ADI'],
                'category' => $row['GRUBU'],
                'brand' => $row['MARKASI'],
                'model' => $row['MODELI'],
                'list_price' => $listeFiyati,
                'sale_price' => round($satisFiyati, 2),
                'vat_rate' => $row['KDV_ORANI']
            ];
        }

        return $products;
    } catch (PDOException $e) {
        error_log("Veritabanı hatası: " . $e->getMessage());
        return [];
    }
}

// Ana sayfa için ürünleri getir
$products = getProducts();
?>

<div class="container mt-4">
    <h2>Ürün Listesi</h2>
    
    <?php if (empty($products)): ?>
        <div class="alert alert-warning">Henüz ürün bulunmamaktadır.</div>
    <?php else: ?>
    <div class="table-responsive">
        <table id="productsTable" class="table table-striped">
            <thead>
                <tr>
                    <th>Kod</th>
                    <th>Ad</th>
                    <th>Kategori</th>
                    <th>Marka</th>
                    <th>Model</th>
                    <th>Liste Fiyatı</th>
                    <th>Satış Fiyatı</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['id']) ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['category']) ?></td>
                        <td><?= htmlspecialchars($product['brand']) ?></td>
                        <td><?= htmlspecialchars($product['model']) ?></td>
                        <td class="text-end"><?= number_format($product['list_price'], 2, ',', '.') ?> ₺</td>
                        <td class="text-end"><?= number_format($product['sale_price'], 2, ',', '.') ?> ₺</td>
                        <td>
                            <div class="btn-group">
                                <a href="index.php?page=b2c_product_details&id=<?= urlencode($product['id']) ?>" 
                                   class="btn btn-sm btn-info">Detay</a>
                                <button class="btn btn-sm btn-success sync-product" 
                                        data-id="<?= htmlspecialchars($product['id']) ?>">Senkronize Et</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#productsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/tr.json'
            },
            order: [[1, 'asc']] // Ürün adına göre sırala
        });

        $('.sync-product').click(function() {
            const productId = $(this).data('id');
            if (confirm('Bu ürünü WooCommerce ile senkronize etmek istediğinize emin misiniz?')) {
                // AJAX ile senkronizasyon işlemi yapılacak
                alert('Senkronizasyon başlatıldı');
            }
        });
    });
</script>

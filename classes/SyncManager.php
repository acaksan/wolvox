<?php

namespace Classes;

use Exception;
use Automattic\WooCommerce\Client;

class SyncManager {
    private $wolvoxDb;
    private $woocommerce;

    public function __construct(WolvoxDatabaseClient $wolvoxDb, Client $woocommerce) {
        $this->wolvoxDb = $wolvoxDb;
        $this->woocommerce = $woocommerce;
    }

    public function syncProducts($limit = 100, $offset = 0) {
        try {
            // Wolvox'tan ürünleri al
            $products = $this->wolvoxDb->getProducts($limit, $offset);
            $result = ['success' => true, 'message' => 'Senkronizasyon başarılı'];
            $syncedProducts = 0;

            foreach ($products as $product) {
                try {
                    // Ürün özelliklerini al
                    $features = $this->wolvoxDb->getProductFeatures($product['code']);
                    $images = $this->wolvoxDb->getProductImages($product['code']);
                    $stock = $this->wolvoxDb->getProductStock($product['code']);
                    $alternatives = $this->wolvoxDb->getProductAlternatives($product['code']);

                    // WooCommerce ürün verilerini hazırla
                    $productData = [
                        'name' => $product['name'],
                        'type' => 'simple',
                        'regular_price' => (string)$product['price1'],
                        'description' => $product['description'],
                        'short_description' => '',
                        'categories' => [
                            ['id' => 1] // Varsayılan kategori
                        ],
                        'images' => [],
                        'attributes' => [],
                        'meta_data' => [
                            ['key' => '_wolvox_code', 'value' => $product['code']],
                            ['key' => '_wolvox_brand', 'value' => $product['brand']],
                            ['key' => '_wolvox_model', 'value' => $product['model']],
                            ['key' => '_wolvox_special_code', 'value' => $product['special_code']],
                            ['key' => '_wolvox_special_code2', 'value' => $product['special_code2']],
                            ['key' => '_wolvox_special_code3', 'value' => $product['special_code3']],
                            ['key' => '_wolvox_unit1', 'value' => $product['unit1']],
                            ['key' => '_wolvox_unit2', 'value' => $product['unit2']],
                            ['key' => '_wolvox_unit3', 'value' => $product['unit3']],
                            ['key' => '_wolvox_unit1_ratio', 'value' => $product['unit1_ratio']],
                            ['key' => '_wolvox_unit2_ratio', 'value' => $product['unit2_ratio']],
                            ['key' => '_wolvox_unit3_ratio', 'value' => $product['unit3_ratio']],
                            ['key' => '_wolvox_currency', 'value' => $product['currency']],
                        ]
                    ];

                    // Fiyatları ekle
                    if ($product['price2']) {
                        $productData['meta_data'][] = ['key' => '_wolvox_price2', 'value' => $product['price2']];
                    }
                    if ($product['price3']) {
                        $productData['meta_data'][] = ['key' => '_wolvox_price3', 'value' => $product['price3']];
                    }
                    if ($product['discount']) {
                        $productData['meta_data'][] = ['key' => '_wolvox_discount', 'value' => $product['discount']];
                        // İndirimli fiyatı hesapla
                        $salePrice = $product['price1'] * (1 - ($product['discount'] / 100));
                        $productData['sale_price'] = (string)round($salePrice, 2);
                    }

                    // Özellikleri ekle
                    if (!empty($features)) {
                        $attributes = [];
                        foreach ($features as $feature) {
                            $attributes[] = [
                                'name' => $feature['feature_name'],
                                'options' => [$feature['feature_value']],
                                'visible' => true,
                                'variation' => false
                            ];
                        }
                        $productData['attributes'] = $attributes;
                    }

                    // Stok bilgisini ekle
                    $totalStock = 0;
                    $stockNotes = [];
                    foreach ($stock as $stockItem) {
                        $totalStock += $stockItem['quantity'];
                        if ($stockItem['lot_no'] || $stockItem['serial_no']) {
                            $stockNotes[] = sprintf(
                                'Depo: %s, Lot: %s, Seri: %s, Miktar: %s',
                                $stockItem['warehouse_code'],
                                $stockItem['lot_no'],
                                $stockItem['serial_no'],
                                $stockItem['quantity']
                            );
                        }
                    }
                    $productData['stock_quantity'] = $totalStock;
                    $productData['manage_stock'] = true;
                    if (!empty($stockNotes)) {
                        $productData['meta_data'][] = [
                            'key' => '_wolvox_stock_details',
                            'value' => implode("\n", $stockNotes)
                        ];
                    }

                    // Alternatif ürünleri ekle
                    if (!empty($alternatives)) {
                        $productData['meta_data'][] = [
                            'key' => '_wolvox_alternatives',
                            'value' => array_column($alternatives, 'alternative_code')
                        ];
                    }

                    // Ürünü WooCommerce'e gönder
                    $existingProduct = $this->findWooCommerceProduct($product['code']);
                    if ($existingProduct) {
                        $productData['id'] = $existingProduct->id;
                        $this->woocommerce->put('products/' . $existingProduct->id, $productData);
                    } else {
                        $this->woocommerce->post('products', $productData);
                    }

                    $syncedProducts++;
                } catch (Exception $e) {
                    // Ürün senkronizasyonunda hata
                    $result['message'] = "Bazı ürünler senkronize edilemedi. Son hata: " . $e->getMessage();
                    $result['success'] = false;
                }
            }

            $result['synced_count'] = $syncedProducts;
            $result['total_count'] = count($products);
            return $result;

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "Senkronizasyon hatası: " . $e->getMessage()
            ];
        }
    }

    private function findWooCommerceProduct($wolvoxCode) {
        $products = $this->woocommerce->get('products', [
            'meta_key' => '_wolvox_code',
            'meta_value' => $wolvoxCode
        ]);

        return !empty($products) ? $products[0] : null;
    }

    private function base64EncodeImage($imagePath) {
        if (!file_exists($imagePath)) {
            return null;
        }
        return base64_encode(file_get_contents($imagePath));
    }
}

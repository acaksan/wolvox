<?php

namespace Classes;

use Automattic\WooCommerce\Client;

class WooCommerceService
{
    private $client;

    public function __construct($config)
    {
        $this->client = new Client(
            $config['url'],
            $config['consumer_key'],
            $config['consumer_secret'],
            [
                'version' => 'wc/v3',
                'timeout' => 30,
                'verify_ssl' => true,
            ]
        );
    }

    // Ürünleri listele
    public function getProducts($params = [])
    {
        return $this->client->get('products', $params);
    }

    // Yeni ürün ekle
    public function createProduct($data)
    {
        return $this->client->post('products', $data);
    }

    // Ürünü güncelle
    public function updateProduct($id, $data)
    {
        return $this->client->put('products/' . $id, $data);
    }

    // Ürün sil
    public function deleteProduct($id)
    {
        return $this->client->delete('products/' . $id, ['force' => true]);
    }

    // Siparişleri listele
    public function getOrders($params = [])
    {
        return $this->client->get('orders', $params);
    }

    // Müşterileri listele
    public function getCustomers($params = [])
    {
        return $this->client->get('customers', $params);
    }
}

<?php

namespace Classes;

use Automattic\WooCommerce\Client;

class WooCommerceClient {
    private $client;

    public function __construct($url, $consumerKey, $consumerSecret) {
        $this->client = new Client(
            $url,
            $consumerKey,
            $consumerSecret,
            [
                'version' => 'wc/v3',       // WooCommerce API versiyonu
                'timeout' => 30,           // API isteği zaman aşımı
                'verify_ssl' => true,      // SSL sertifikasını doğrula
            ]
        );
    }

    // Ürünleri çekmek için genel bir yöntem
    public function getProducts($params = []) {
        return $this->client->get('products', $params);
    }

    // Siparişleri çekmek için genel bir yöntem
    public function getOrders($params = []) {
        return $this->client->get('orders', $params);
    }

    // API'den herhangi bir endpoint için veri çekmek
    public function get($endpoint, $params = []) {
        return $this->client->get($endpoint, $params);
    }

    // API'ye veri eklemek için bir yöntem
    public function post($endpoint, $data = []) {
        return $this->client->post($endpoint, $data);
    }

    // API'deki veriyi güncellemek için bir yöntem
    public function put($endpoint, $data = []) {
        return $this->client->put($endpoint, $data);
    }

    // API'deki veriyi silmek için bir yöntem
    public function delete($endpoint, $force = true) {
        return $this->client->delete($endpoint, ['force' => $force]);
    }
}

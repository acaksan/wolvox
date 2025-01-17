<?php

use Automattic\WooCommerce\Client;

class WooCommerceIntegration
{
    private $client;

    public function __construct($url, $consumerKey, $consumerSecret, $version = 'wc/v3')
    {
        try {
            $this->client = new Client(
                $url,
                $consumerKey,
                $consumerSecret,
                ['version' => $version]
            );
        } catch (Exception $e) {
            throw new Exception("WooCommerce bağlantısı başarısız: " . $e->getMessage());
        }
    }

    public function getProducts($params = [])
    {
        return $this->client->get('products', $params);
    }

    public function getOrders($params = [])
    {
        return $this->client->get('orders', $params);
    }

    public function getCustomers($params = [])
    {
        return $this->client->get('customers', $params);
    }

    public function createProduct($data)
    {
        return $this->client->post('products', $data);
    }

    public function createOrder($data)
    {
        return $this->client->post('orders', $data);
    }

    public function createCustomer($data)
    {
        return $this->client->post('customers', $data);
    }

    public function get($endpoint, $params = [])
    {
        return $this->client->get($endpoint, $params);
    }

    public function post($endpoint, $data)
    {
        return $this->client->post($endpoint, $data);
    }
}

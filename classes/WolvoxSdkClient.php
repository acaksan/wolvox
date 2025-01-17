<?php

namespace Classes;

class WolvoxSdkClient {
    private $sdk;
    private $config;

    public function __construct(array $config) {
        $this->config = $config;
        $this->connect();
    }

    private function connect() {
        try {
            // SDK'yı başlat
            $this->sdk = new \COM('WOLVOX8.Application');
            
            // Oturum aç
            $loginResult = $this->sdk->Login(
                $this->config['username'],
                $this->config['password'],
                $this->config['company_code'],
                $this->config['period_code']
            );

            if (!$loginResult) {
                throw new \Exception("SDK oturum açma hatası");
            }
        } catch (\Exception $e) {
            throw new \Exception("Wolvox SDK bağlantı hatası: " . $e->getMessage());
        }
    }

    public function getProducts() {
        try {
            $products = [];
            $stockCards = $this->sdk->GetStockCards();
            
            if ($stockCards) {
                while (!$stockCards->EOF) {
                    $products[] = [
                        'STOK_KODU' => $stockCards->Fields["StockCode"]->Value,
                        'STOK_ADI' => $stockCards->Fields["StockName"]->Value,
                        'SATIS_FIYATI1' => $stockCards->Fields["SalesPrice1"]->Value,
                        'KDV_ORANI' => $stockCards->Fields["VATRate"]->Value,
                        'STOK_MIKTARI' => $this->getStockQuantity($stockCards->Fields["StockCode"]->Value)
                    ];
                    $stockCards->MoveNext();
                }
            }

            return $products;
        } catch (\Exception $e) {
            throw new \Exception("Ürünler getirilirken hata: " . $e->getMessage());
        }
    }

    public function getStockQuantity($stockCode) {
        try {
            $quantity = $this->sdk->GetStockQuantity($stockCode);
            return $quantity ?: 0;
        } catch (\Exception $e) {
            throw new \Exception("Stok miktarı getirilirken hata: " . $e->getMessage());
        }
    }

    public function getProductByCode($stockCode) {
        try {
            $stockCard = $this->sdk->GetStockCard($stockCode);
            
            if ($stockCard) {
                return [
                    'STOK_KODU' => $stockCard->Fields["StockCode"]->Value,
                    'STOK_ADI' => $stockCard->Fields["StockName"]->Value,
                    'SATIS_FIYATI1' => $stockCard->Fields["SalesPrice1"]->Value,
                    'KDV_ORANI' => $stockCard->Fields["VATRate"]->Value,
                    'STOK_MIKTARI' => $this->getStockQuantity($stockCode)
                ];
            }
            
            return null;
        } catch (\Exception $e) {
            throw new \Exception("Ürün detayı getirilirken hata: " . $e->getMessage());
        }
    }

    public function __destruct() {
        try {
            if ($this->sdk) {
                $this->sdk->Logout();
            }
        } catch (\Exception $e) {
            // Oturum kapatma hatası yoksay
        }
    }
}

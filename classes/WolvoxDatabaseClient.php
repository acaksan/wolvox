<?php

namespace Classes;

use PDO;
use PDOException;

class WolvoxDatabaseClient
{
    private PDO $pdo;
    private string $companyCode;
    private string $periodCode;

    public function __construct(
        string $host,
        string $database,
        string $username,
        string $password,
        string $companyCode,
        string $periodCode
    ) {
        $this->companyCode = $companyCode;
        $this->periodCode = $periodCode;

        try {
            $dsn = "firebird:dbname={$host}:{$database};charset=UTF8";
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new \Exception("Veritabanı bağlantı hatası: " . $e->getMessage());
        }
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    public function getProducts($productId = null): array
    {
        try {
            $sql = "
                SELECT 
                    s.BLKODU,
                    s.STOKKODU,
                    s.STOK_ADI,
                    s.GRUBU,
                    s.MARKASI,
                    s.MODELI,
                    s.BARKODU,
                    s.WEBDE_GORUNSUN,
                    s.AKTIF,
                    s.DEPO_ADI,
                    CAST(s.SATIS_ISKONTO_ORANI AS DECIMAL(15,2)) as SATIS_ISKONTO_ORANI,
                    CAST(COALESCE(
                        (
                            SELECT FIRST 1 h.KPB_FIYATI
                            FROM STOKHR h
                            WHERE h.BLSTKODU = s.BLKODU
                            AND h.SILINDI = 0
                            AND h.TUTAR_TURU = 0
                            ORDER BY h.TARIHI DESC
                        ), s.MUH_SATIS_YI
                    ) AS DECIMAL(15,2)) as SATIS_FIYATI,
                    CAST(COALESCE(
                        (
                            SELECT SUM(
                                CASE 
                                    WHEN h.TUTAR_TURU = 0 THEN -h.MIKTARI 
                                    ELSE h.MIKTARI 
                                END
                            )
                            FROM STOKHR h 
                            WHERE h.BLSTKODU = s.BLKODU 
                            AND h.SILINDI = 0
                        ), 0
                    ) AS DECIMAL(15,2)) as TOPLAM_STOK
                FROM STOK s
                WHERE s.WEBDE_GORUNSUN = 1 
                AND s.AKTIF = 1
            ";

            if ($productId !== null) {
                $sql .= " AND s.BLKODU = :productId";
            }

            $sql .= " ORDER BY s.STOK_ADI";

            $stmt = $this->pdo->prepare($sql);
            
            if ($productId !== null) {
                $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
            }

            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Her ürün için depo bazlı stok miktarlarını alalım
            foreach ($products as &$product) {
                // Depo bazlı stok miktarlarını al
                $sql = "
                    SELECT 
                        h.DEPO_ADI,
                        CAST(COALESCE(
                            SUM(
                                CASE 
                                    WHEN h.TUTAR_TURU = 0 THEN -h.MIKTARI 
                                    ELSE h.MIKTARI 
                                END
                            ), 0
                        ) AS DECIMAL(15,2)) as MIKTAR
                    FROM STOKHR h
                    WHERE h.BLSTKODU = :blstkodu
                    AND h.SILINDI = 0
                    AND h.DEPO_ADI IS NOT NULL
                    AND h.DEPO_ADI <> ''
                    GROUP BY h.DEPO_ADI
                ";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':blstkodu', $product['BLKODU'], PDO::PARAM_INT);
                $stmt->execute();
                
                $depoStoklari = [];
                while ($depo = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if (!empty($depo['DEPO_ADI'])) {
                        $depoStoklari[] = $depo['DEPO_ADI'] . ': ' . number_format($depo['MIKTAR'], 2, ',', '.');
                    }
                }
                
                $product['DEPO_STOKLARI'] = $depoStoklari;
                unset($product['DEPO_ADI']);
            }

            return $products;
        } catch (PDOException $e) {
            error_log("Wolvox getProducts error: " . $e->getMessage());
            throw new \Exception("Ürünler alınırken bir hata oluştu: " . $e->getMessage());
        }
    }

    public function getProductStock($productId): array
    {
        try {
            $sql = "
                SELECT 
                    e.DEPO_ADI,
                    e.MIKTAR_DEVIR,
                    e.MIKTAR_GIREN,
                    e.MIKTAR_CIKAN,
                    e.MIKTAR_KALAN,
                    e.MIKTAR_TERMIN,
                    e.MIKTAR_BLOKE,
                    e.MIKTAR_KULBILIR,
                    e.BIRIM_FIYATI,
                    e.ENV_TUTARI
                FROM PTMP_DEPO_ENVANTERI e
                WHERE e.BLSTKODU = :productId
                ORDER BY e.DEPO_ADI
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Wolvox getProductStock error: " . $e->getMessage());
            throw new \Exception("Ürün stok bilgisi alınırken bir hata oluştu: " . $e->getMessage());
        }
    }

    public function getProductCurrentStock($productId): float
    {
        try {
            $sql = "
                SELECT 
                    COALESCE(SUM(e.MIKTAR_KALAN), 0) as STOK_MIKTARI
                FROM PTMP_DEPO_ENVANTERI e
                WHERE e.BLSTKODU = :productId
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['STOK_MIKTARI'];
        } catch (PDOException $e) {
            error_log("Wolvox getProductCurrentStock error: " . $e->getMessage());
            throw new \Exception("Ürün stok miktarı alınırken bir hata oluştu: " . $e->getMessage());
        }
    }

    public function getProductFeatures(int $productId): array
    {
        $sql = "
            SELECT 
                MARKASI,
                MODELI,
                OZEL_KODU1,
                OZEL_KODU2,
                OZEL_KODU3
            FROM STOK
            WHERE BLKODU = :product_id
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['product_id' => $productId]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $features = [];
            if ($data) {
                if (!empty($data['MARKASI'])) {
                    $features[] = ['ozellik' => 'Marka', 'deger' => $data['MARKASI']];
                }
                if (!empty($data['MODELI'])) {
                    $features[] = ['ozellik' => 'Model', 'deger' => $data['MODELI']];
                }
                if (!empty($data['OZEL_KODU1'])) {
                    $features[] = ['ozellik' => 'Özel Kod 1', 'deger' => $data['OZEL_KODU1']];
                }
                if (!empty($data['OZEL_KODU2'])) {
                    $features[] = ['ozellik' => 'Özel Kod 2', 'deger' => $data['OZEL_KODU2']];
                }
                if (!empty($data['OZEL_KODU3'])) {
                    $features[] = ['ozellik' => 'Özel Kod 3', 'deger' => $data['OZEL_KODU3']];
                }
            }
            
            return $features;
        } catch (PDOException $e) {
            throw new \Exception("Ürün özellikleri alınamadı: " . $e->getMessage());
        }
    }

    public function getProductImages(int $productId): array
    {
        // Resim yolunu STOK tablosundan alalım
        $sql = "
            SELECT 
                1 as RESIM_NO,
                RESIM_YOLU
            FROM STOK
            WHERE BLKODU = :product_id
            AND RESIM_YOLU IS NOT NULL
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['product_id' => $productId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \Exception("Ürün resimleri alınamadı: " . $e->getMessage());
        }
    }

    public function getProductAlternatives(int $productId): array
    {
        // Önce ürünün marka ve modelini alalım
        $sql = "
            SELECT MARKASI, MODELI
            FROM STOK
            WHERE BLKODU = :product_id
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['product_id' => $productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                return [];
            }

            // Sonra aynı marka ve modeldeki diğer ürünleri bulalım
            $sql = "
                SELECT 
                    STOKKODU as ALTERNATIF_STOK_KODU,
                    STOK_ADI as ALTERNATIF_STOK_ADI
                FROM STOK
                WHERE MARKASI = :marka
                AND MODELI = :model
                AND BLKODU != :product_id
                AND WEBDE_GORUNSUN = 1
                AND AKTIF = 1
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'marka' => $product['MARKASI'],
                'model' => $product['MODELI'],
                'product_id' => $productId
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \Exception("Alternatif ürünler alınamadı: " . $e->getMessage());
        }
    }

    public function getWarehouses(): array
    {
        $sql = "
            SELECT DISTINCT
                D.DEPO_KODU as code,
                D.DEPO_ADI as name,
                D.ADRES as address
            FROM DEPOLAR D
            WHERE D.AKTIF = 1
        ";

        try {
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \Exception("Depolar alınamadı: " . $e->getMessage());
        }
    }
}

<?php

namespace Classes;

use PDO;

class FirebirdService
{
    private $connection;

    public function __construct($config)
    {
        $dsn = "firebird:dbname={$config['host']}:{$config['database']};charset={$config['charset']}";
        $this->connection = new PDO($dsn, $config['user'], $config['password']);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Örnek: Stok bilgilerini getir
    public function getStock($stockCode)
    {
        $query = $this->connection->prepare("SELECT * FROM STOK WHERE STOKKODU = ?");
        $query->execute([$stockCode]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    // Tüm stokları getir
    public function getAllStocks()
    {
        $query = $this->connection->query("SELECT * FROM STOK");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}

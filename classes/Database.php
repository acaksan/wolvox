<?php

namespace Classes;

use PDO;
use PDOException;
use Exception;

class Database
{
    private $firebirdConnection;

    public function connectToFirebird($config)
    {
        // Bağlantı bilgilerini kontrol et
        if (empty($config['database']) || empty($config['user']) || empty($config['password'])) {
            throw new Exception("Firebird bağlantı bilgileri eksik.");
        }

        $dsn = "firebird:dbname={$config['database']};charset=UTF8";

        try {
            $this->firebirdConnection = new PDO($dsn, $config['user'], $config['password']);
            $this->firebirdConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->firebirdConnection;
        } catch (PDOException $e) {
            throw new PDOException("Firebird bağlantı hatası: " . $e->getMessage());
        }
    }
}

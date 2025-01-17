<?php

class Database
{
    private $firebirdConnection;

    public function connectToFirebird($config)
    {
        // Bağlantı bilgilerini kontrol et
        if (empty($config['host']) || empty($config['database']) || empty($config['user']) || empty($config['password']) || empty($config['charset'])) {
            throw new Exception("Firebird bağlantı bilgileri eksik.");
        }

        $dsn = "firebird:dbname={$config['host']}:{$config['database']};charset={$config['charset']}";

        try {
            $this->firebirdConnection = new PDO($dsn, $config['user'], $config['password']);
            $this->firebirdConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->firebirdConnection;
        } catch (PDOException $e) {
            throw new PDOException("Firebird bağlantı hatası: " . $e->getMessage());
        }
    }
}

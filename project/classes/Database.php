<?php

class Database
{
    private $firebirdConnection;

    public function connectToFirebird($config)
    {
        // Firebird bağlantısı için DSN oluşturma
        $dsn = "firebird:dbname={$config['host']}:{$config['database']};charset={$config['charset']}";

        try {
            // PDO ile bağlantı
            $this->firebirdConnection = new PDO($dsn, $config['user'], $config['password']);
            $this->firebirdConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->firebirdConnection;
        } catch (PDOException $e) {
            // Hata yakalama
            throw new PDOException("Firebird bağlantı hatası: " . $e->getMessage());
        }
    }
}

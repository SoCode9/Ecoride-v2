<?php

namespace App\Database; 
use PDO;

class DbConnection
{

    private static $instance = null;

    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = new \PDO($_ENV['DB_DSN'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    }


    public static function getInstance(): DbConnection
    {
        return self::createOrReturnInstance();
    }

    //connexion à la  base de données
    public static function getPdo(): PDO
    {
        return self::createOrReturnInstance()->pdo;
    }

    private  static function createOrReturnInstance(): DbConnection
    {
        if (!self::$instance) {
            self::$instance = new DbConnection();
        }
        return self::$instance;
    }
}

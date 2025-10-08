<?php

namespace App\Database;

use PDO;
use PDOException;

class DbConnection
{

    private static $instance = null;

    private PDO $pdo;

    public function __construct()
    {
        try {
            $host    = getenv('DB_HOST') ?: 'db';
            $port    = (int)(getenv('DB_PORT') ?: '3306');
            $name    = getenv('DB_NAME') ?: 'ecoride2';
            $user    = getenv('DB_USER') ?: 'root';
            $pass    = getenv('DB_PASS') ?: '';
            $charset = getenv('DB_CHARSET') ?: 'utf8mb4';

            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $name, $charset);
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            error_log("Connection MySQL error : " . $e->getMessage());
            $_SESSION['error_message'] = "Une erreur est survenue";
            //header('Location: ../index.php');
            exit;
        }
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

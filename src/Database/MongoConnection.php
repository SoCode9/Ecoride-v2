<?php

namespace App\Database;

use Exception;

use MongoDB\Client as MongoClient;
use MongoDB\Database as MongoDatabase;

class MongoConnection
{
    private static ?MongoConnection $instance = null;
    private ?MongoDatabase $mongoDb = null;

    private function __construct()
    {
        try {
            $uri  = $_ENV['MONGO_URI'] ?: 'mongodb://127.0.0.1:27017';
            $name = $_ENV['MONGO_DB'] ?: 'ecoride';

            if ($uri && $name) {
                $client = new MongoClient($uri);
                $this->mongoDb = $client->selectDatabase($name);
            }
         
        } catch (Exception $e) {
            error_log("Connection MongoDB error : " . $e->getMessage());
            $_SESSION['error_message'] = "Une erreur est survenue";
            header('Location: ' . BASE_URL . '/');
            exit;
        }
    }

    public static function getMongoDb(): ?MongoDatabase
    {
        if (!self::$instance) {
            self::$instance = new MongoConnection();
        }
        return self::$instance->mongoDb;
    }
}

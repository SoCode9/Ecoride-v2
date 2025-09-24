<?php

namespace App\Driver\Repository;

use App\Database\DbConnection;
use PDO;

use App\User\Controller\UserController;

use PDOException;
use Exception;

class DriverRepository extends UserController
{
    /**
     * Loads all validated ratings for the current driver, including the rater's pseudo and photo.
     *
     * @throws Exception If the driver ID is not set or the query fails
     * @return array An array of ratings with user information
     */
    public static function loadValidatedRatings($driverId): array
    {
        if (empty($driverId)) {
            throw new Exception("Impossible de charger les avis sans identifiant conducteur");
        }

        try {
            $sql = "
            SELECT ratings.*, users.pseudo, users.photo
            FROM ratings
            JOIN driver ON driver.user_id = ratings.driver_id
            JOIN users ON users.id = ratings.user_id
            WHERE ratings.driver_id = :driver_id AND ratings.status = 'validated'
            ORDER BY ratings.created_at DESC
        ";

            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':driver_id', $driverId, PDO::PARAM_STR);
            $statement->execute();

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in loadValidatedRatings() (driver ID: {$driverId}) : " . $e->getMessage());
            throw new Exception("Impossible de charger les évaluations du conducteur");
        }
    }

    
}

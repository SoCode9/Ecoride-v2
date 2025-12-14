<?php

namespace App\Rating\Repository;

use App\Database\DbConnection;
use PDO;
use PDOException;
use Exception;

use App\Rating\Entity\Rating;

class RatingRepository
{
    /**
     * Saves a new rating from a user to a driver into the database
     * @param string $userId The ID of the user giving the rating
     * @param string $driverId The ID of the driver receiving the rating
     * @param float $newRating The rating value (e.g. 4.5)
     * @param string|null $newComment Optional comment left by the user
     * @throws \Exception If a database error occurs
     * @return bool True on success, false on failure
     */
    public function new(string $userId, string $driverId, float $newRating, ?string $newComment = null): bool
    {
        try {
            $sql = 'INSERT INTO ratings (user_id, driver_id, rating, description) VALUES (:userId, :driverId, :rating, :comment)';
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':userId', $userId, PDO::PARAM_STR);
            $statement->bindParam(':driverId', $driverId, PDO::PARAM_STR);
            $statement->bindValue(':rating', $newRating);
            $statement->bindParam(':comment', $newComment, PDO::PARAM_STR);
            return $statement->execute();
        } catch (PDOException $e) {
            error_log("RatingRepository - Database error in new() : " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de l'enregistrement de la note");
        }
    }

    public function findByDriverId(int $id): ?Rating
    {
        try {
            $sql = "SELECT ratings.* FROM ratings 
            WHERE id = :id";
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            if (!$row)
                return null;

            return new Rating(
                $row['id'],
                $row['user_id'],
                $row['driver_id'],
                $row['rating'],
                $row['description'],
                $row['status'],
                $row['created_at']
            );
        } catch (PDOException $e) {
            error_log("RatingRepository - Database error in findByDriverId() : " . $e->getMessage());
            throw new Exception("Une erreur est survenue");
        }
    }

    public function findAllByDriver(string $driverId, bool $takeValidatedRatings)
    {
        try {
            $sql = "SELECT ratings.* FROM ratings 
            WHERE driver_id = :driverId";

            if ($takeValidatedRatings) {
                $sql .= " AND (status = 'validated')";
            }

            $sql .= " ORDER BY created_at DESC";

            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':driverId', $driverId, PDO::PARAM_STR);
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            if (!$rows)
                return [];
            $ratings = [];
            foreach ($rows as $rating) {
                $ratings[] = new Rating(
                    $rating['id'],
                    $rating['user_id'],
                    $rating['driver_id'],
                    $rating['rating'],
                    $rating['description'],
                    $rating['status'],
                    $rating['created_at']
                );
            }

            return $ratings;
        } catch (PDOException $e) {
            error_log("RatingRepository - Database error in findAllByDriver() : " . $e->getMessage());
            throw new Exception("Une erreur est survenue");
        }
    }

    /**
     * Loads a paginated list of ratings that are pending validation, with associated user pseudonyms
     * @param int $limit The number of ratings to fetch (default: 5)
     * @param int $offset The offset for pagination (default: 0)
     * @return array An array of associative results (each including passenger and driver pseudonyms)
     * @throws Exception If a database error occurs
     */
    public function loadRatingsInValidation(int $limit = 5, int $offset = 0): array
    {
        try {
            $sql = "SELECT passenger.pseudo AS passenger_pseudo, driver.pseudo AS driver_pseudo, ratings.* FROM ratings 
                JOIN users AS passenger ON ratings.user_id = passenger.id
                JOIN users AS driver ON ratings.driver_id = driver.id
                WHERE status = 'in validation'
                ORDER BY created_at ASC
                LIMIT :limit OFFSET :offset";

            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
            $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
            $statement->execute();

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in loadRatingsInValidation() : " . $e->getMessage());
            throw new Exception("Impossible de charger les évaluations en attente de validation");
        }
    }

    /**
     * Saves a new rating from a user to a driver into the database.
     * @throws \Exception If the database query fails
     * @return int Number of ratings with status 'in validation'
     */
    public function countAllRatingsInValidation(): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM ratings WHERE status = 'in validation'";

            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->execute();
            return (int) $statement->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database error in countAllRatingsInValidation(): " . $e->getMessage());
            throw new Exception("Impossible de compter les évaluations en attente de validation");
        }
    }
}

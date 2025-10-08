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
            if (!$row) return null;

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
            if (!$rows) return [];
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
}

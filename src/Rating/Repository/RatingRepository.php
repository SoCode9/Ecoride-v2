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

    /**
     * When an employee validate a rating, 
     * @param int $idRating rating id that is validated
     * @param string $newStatus Can be "refused" or "validated"
     * @return void
     */
    public function validateRating(int $idRating, string $newStatus): void
    {
        try {
            $sql = "UPDATE ratings SET status = :newStatus WHERE id = :idRating";
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam('idRating', $idRating, PDO::PARAM_INT);
            $statement->bindParam('newStatus', $newStatus, PDO::PARAM_STR);
            $statement->execute();
        } catch (PDOException $e) {
            error_log("Database error in validateRating(): " . $e->getMessage());
            throw new Exception("Erreur lors du changement de statut de l'avis");
        }
    }

    /**
     * Retrieves reservations that include a bad comment not yet validated
     * @param int $limit Number of results to return (default: 5)
     * @param int $offset Number of results to skip (default: 0)
     * @return array Array of associative results including passenger and driver info, carpool date and cities, and reservation details
     * @throws Exception If a database error occurs
     */
    public function getBadComments(int $limit = 5, int $offset = 0): array
    {
        try {
            $sql = 'SELECT reservations.*,passenger.pseudo AS pseudoPassenger, passenger.mail AS mailPassenger, driver.pseudo AS pseudoDriver, driver.mail AS mailDriver, driver.id AS idDriver, carpool.date, carpool.departure_city, carpool.arrival_city, carpool.id AS carpoolId FROM reservations 
        JOIN users AS passenger ON passenger.id = reservations.user_id
        JOIN carpool ON carpool.id = reservations.carpool_id
        JOIN users AS driver ON driver.id = carpool.driver_id
        WHERE bad_comment IS NOT NULL AND bad_comment_validated =0
        ORDER BY carpool.date ASC
        LIMIT :limit OFFSET :offset';
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':limit', $limit, PDO::PARAM_INT);
            $statement->bindParam(':offset', $offset, PDO::PARAM_INT);
            $statement->execute();

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getBadComments() : " . $e->getMessage());
            throw new Exception("Impossible de récupérer les mauvais commentaires");
        }
    }

    /**
     * Count all bad comments not yet validated
     * @return int
     * @throws Exception If a database error occurs
     */
    public function countAllBadComments(): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM reservations  WHERE bad_comment IS NOT NULL AND bad_comment_validated =0";
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->execute();
            return (int) $statement->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database error in countAllBadComments() : " . $e->getMessage());
            throw new Exception("Impossible de compter les mauvais commentaires");
        }
    }

    /**
     * Marks a bad comment as validated in the database.
     *
     * @param int $reservationId The reservation ID concerned by the bad comment
     * @throws Exception If the update fails or no reservation is affected
     * @return void
     */
    public function markBadCommentAsValidated(int $reservationId): void
    {
        try {
            $sql = 'UPDATE reservations SET bad_comment_validated = 1  WHERE id = :reservationId';
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':reservationId', $reservationId, PDO::PARAM_INT);
            if (!$statement->execute()) {
                error_log("markBadCommentAsValidated() failed to execute query for reservation ID $reservationId");
                throw new Exception("Échec de la résolution du litige");
            }
        } catch (PDOException $e) {
            error_log("Database error in markBadCommentAsValidated() (reservation ID: $reservationId): " . $e->getMessage());
            throw new Exception("Erreur lors de la validation du mauvais commentaire");
        }
    }
}

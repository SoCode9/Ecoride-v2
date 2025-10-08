<?php

namespace App\Reservation\Repository;

use App\Database\DbConnection;
use PDO;
use PDOException;
use Exception;

class ReservationRepository
{

    /**
     * Create a new reservation for a user on a given carpool
     * @param string $userId Passenger user UUID
     * @param string $carpoolId Carpool UUID to join
     * @param int $creditsSspent Amount of credits recorded as spent for this reservation
     * @throws \Exception If a database error occurs
     * @return bool True on successful insert, false otherwise.
     */
    public function new(string $userId, string $carpoolId, int $creditsSspent)
    {
        try {
            $sql = 'INSERT INTO reservations (user_id, carpool_id, credits_spent) VALUES (:userId, :carpoolId, :creditSpent)';
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':userId', $userId, PDO::PARAM_STR);
            $statement->bindParam(':carpoolId', $carpoolId, PDO::PARAM_STR);
            $statement->bindValue(':creditSpent', $creditsSspent, PDO::PARAM_INT);
            return $statement->execute();
        } catch (PDOException $e) {
            error_log("ReservationRepository - Database error in new() : " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de l'enregistrement de la réservation");
        }
    }

    /**
     * count the number of passengers in a carpool
     * @param string $carpoolId
     * @return int
     */
    public function countPassengers(string $carpoolId): int
    {
        try {
            $sql = "SELECT COUNT(*) AS 'seats_allocated' FROM reservations WHERE carpool_id = :carpoolId";
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':carpoolId', $carpoolId, PDO::PARAM_STR);
            $statement->execute();

            return (int) $statement->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database error in countPassengers() : " . $e->getMessage());
            throw new Exception("Impossible de compter les passagers");
        }
    }

    /**
     * Check if a reservation already exists for a given user and carpool.
     * @param int $userId    The passenger's user ID
     * @param int $carpoolId  The carpool (carpool) ID
     * @return bool          True if a reservation exists, false otherwise
     * @throws Exception     If a database error occurs
     */
    public function existsForUserAndCarpool(string $userId, string $carpoolId): bool
    {
        try {
            $sql = 'SELECT COUNT(*) FROM reservations WHERE user_id = :userId AND carpool_id = :carpoolId';

            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':userId', $userId, PDO::PARAM_STR);
            $statement->bindParam(':carpoolId', $carpoolId, PDO::PARAM_STR);
            $statement->execute();

            return (int)$statement->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Database error in existsForUserAndCarpool(user:$userId, carpool:$carpoolId): " . $e->getMessage());
            throw new Exception("Impossible de vérifier l'existance de la réservation");
        }
    }
}

<?php

namespace App\Reservation\Repository;

use App\Database\DbConnection;
use PDO;
use PDOException;
use Exception;

class ReservationRepository
{

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
     * Check if a reservation already exists for a given user and travel.
     * @param int $userId    The passenger's user ID
     * @param int $carpoolId  The travel (carpool) ID
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

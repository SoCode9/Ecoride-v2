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
}

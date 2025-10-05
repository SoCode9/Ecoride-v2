<?php

namespace App\Driver\Repository;

use App\Database\DbConnection;
use PDO;
use PDOException;
use Exception;

use App\Driver\Entity\Driver;
use App\User\Repository\UserRepository;

class DriverRepository
{

    public function __construct(private UserRepository $userRepo) {}

    private function findById(string $id): array|null
    {
        try {
            $sql = "SELECT * FROM driver WHERE user_id = :id";
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':id', $id, PDO::PARAM_STR);
            $statement->execute();
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            if (!$row) return null;

            return array(
                "user_id" => $row['user_id'] ?? null,
                "food" => $row['food'] ?? null,
                "music" => $row['music'] ?? null,
                "pets" => $row['pets'] ?? null,
                "smoker" => $row['smoker'] ?? null,
                "speaker" => $row['speaker'] ?? null
            );
        } catch (PDOException $e) {
            error_log("DriverRepository - Database error in findById() : " . $e->getMessage());
            throw new Exception("Une erreur est survenue");
        }
    }

    public function makeFromUserId(string $id): Driver
    {
        $u = $this->userRepo->findById($id);
        $driver = $this->findById($u->getId());

        return new Driver(
            $driver['food'] ?? null,
            $driver['music'] ?? null,
            $driver['pets'] ?? null,
            $driver['smoker'] ?? null,
            $driver['speaker'] ?? null,
            $u->getId(),
            $u->getPseudo(),
            $u->getMail(),
            $u->getPassword(),
            $u->getCredit(),
            $u->getPhoto(),
            $u->getIdRole(),
            $u->IsActivated()
        );
    }

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
            $sql = "SELECT ratings.*, users.pseudo, users.photo
            FROM ratings
            JOIN driver ON driver.user_id = ratings.driver_id
            JOIN users ON users.id = ratings.user_id
            WHERE ratings.driver_id = :driver_id AND ratings.status = 'validated'
            ORDER BY ratings.created_at DESC";

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

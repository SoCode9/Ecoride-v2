<?php

namespace App\Driver\Repository;

use App\Database\DbConnection;
use App\Database\MongoConnection;
use PDO;
use PDOException;
use Exception;

use App\Driver\Entity\Driver;
use App\User\Repository\UserRepository;

class DriverRepository
{

    public function __construct(private UserRepository $userRepo)
    {
    }

    private function findById(string $id): array|null
    {
        try {
            $sql = "SELECT * FROM driver WHERE user_id = :id";
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':id', $id, PDO::PARAM_STR);
            $statement->execute();
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            if (!$row)
                return null;

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
        $otherPref = $this->findCustomPreferences($id);
        //var_dump($otherPref);
        return new Driver(
            $driver['food'] ?? null,
            $driver['music'] ?? null,
            $driver['pets'] ?? null,
            $driver['smoker'] ?? null,
            $driver['speaker'] ?? null,
            $otherPref ?? [],
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
     * find the custom preferences of the current driver.
     * 
     * @throws Exception If the user ID is not set or the preferences cannot be loaded
     * @return array 
     */
    public function findCustomPreferences(string $userId): array
    {
        if (empty($userId)) {
            error_log("findCustomPreferences() failed: driver ID is empty");
            throw new Exception("Impossible de charger les préférences sans identifiant utilisateur");
        }

        try {
            $mongo = MongoConnection::getMongoDb();
            $preferenceCollection = $mongo->preferences;
            $result = $preferenceCollection->find([
                'id_user' => $userId
            ])->toArray();

            return array_map(fn($doc) => (array) $doc, $result);
        } catch (Exception $e) {
            error_log("Database error in findCustomPreferences() (user ID: {$userId}) : " . $e->getMessage());
            return [];
        }
    }


    /**
     * Add a custom preference for the driver
     * This method inserts a new custom preference into the MongoDB collection.
     * @param string $customPrefToAdd The new preference to insert
     * @throws \Exception If the user ID is not set or a database error occurs
     * @return void
     */
    public function newCustomPreference(string $userId, string $customPrefToAdd): void
    {
        if (empty($userId)) {
            error_log("newCustomPreference() failed: user ID is empty.");
            throw new Exception("Impossible d'ajouter une préférence sans identifiant utilisateur");
        }

        try {
            $mongo = MongoConnection::getMongoDb();
            $preferenceCollection = $mongo->preferences;
            $preferenceCollection->insertOne([
                'id_user' => $userId,
                'custom_preference' => $customPrefToAdd,
            ]);

            return;
        } catch (Exception $e) {
            error_log("Database error in addCustomPreference() (user ID: {$userId}) : " . $e->getMessage());
            throw new Exception("Une erreur est survenue");
        }
    }

    /**
     * Deletes a custom preference for the driver
     * This method removes a specific custom preference from the MongoDB collection.
     * @param string $customPrefToDelete The preference to delete
     * @throws \Exception If the user ID is not set or a database error occurs
     * @return void
     */
    public function deleteCustomPreference(string $userId, string $customPrefToDelete): void
    {
        if (empty($userId)) {
            error_log("deleteCustomPreference() failed: user ID is empty.");
            throw new Exception("Impossible de supprimer une préférence sans identifiant utilisateur");
        }

        try {
            $mongo = MongoConnection::getMongoDb();
            $preferenceCollection = $mongo->preferences;

            $preferenceCollection->deleteOne([
                'id_user' => $userId,
                'custom_preference' => $customPrefToDelete,
            ]);
        } catch (Exception $e) {
            error_log("Database error in deleteCustomPreference() (user ID: {$userId}) : " . $e->getMessage());
            throw new Exception("Une erreur est survenue");
        }
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

    public function updateDriverPreference(string $userId, string $column, $value): void
    {
        if (empty($userId)) {
            throw new Exception("Impossible de modifier la préférence sans identifiant utilisateur");
        }

        try {
            $sql = "UPDATE driver SET $column = :value WHERE user_id = :userId";
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            if ($value === null) {
                $statement->bindValue(':value', null, PDO::PARAM_NULL);
            } else {
                $statement->bindValue(':value', $value, PDO::PARAM_INT);
            }
            $statement->bindParam(':userId', $userId, PDO::PARAM_STR);
            $statement->execute();
        } catch (PDOException $e) {
            error_log("Database error in updateDriverPreference() [$column] (user ID: {$userId}) : " . $e->getMessage());
            throw new Exception("Impossible d'ajouter la préférence");
        }
    }
}

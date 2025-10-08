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

    /**
     * Get the carpool id of a reservation
     * @param int $reservationId
     * @throws \Exception
     */
    public function getCarpoolIdFromReservation(int $reservationId): string
    {
        $sql = 'SELECT carpool_id FROM reservations WHERE id = :reservationId ';
        $pdo = DbConnection::getPdo();
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':reservationId', $reservationId, PDO::PARAM_INT);

        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$result || !isset($result['carpool_id'])) {
            error_log("Database error in getCarpoolIdFromReservation() ");
            throw new Exception("Erreur lors du chargement des informations de la réservation");
        }

        return $result['carpool_id'];
    }

    /**
     * Get the driver id of a reservation
     * @param int $reservationId
     * @throws \Exception
     * @return string
     */
    public function getDriverIdFromReservation(int $reservationId): string
    {
        $carpoolId = $this->getCarpoolIdFromReservation($reservationId);

        $sql = 'SELECT driver_id FROM carpool WHERE id = :carpoolId';
        $pdo = DbConnection::getPdo();
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':carpoolId', $carpoolId, PDO::PARAM_STR);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$result || !isset($result['driver_id'])) {
            error_log("Database error in getDriverIdFromReservation() for the reservation : {$reservationId} ");
            throw new Exception("Impossible de récupérer le chauffeur de cette réservation");
        }

        return $result['driver_id'];
    }

    /**
     * Get the reservations not validated of a carpool
     * @param string $carpoolId
     * @return array
     * @throws Exception If a database error occurs
     */
    public function getReservationsNotValidatedOfACarpool(string $carpoolId): array
    {
        try {
            $sql = 'SELECT * FROM reservations WHERE (is_validated = 0 OR bad_comment_validated = 0) AND carpool_id = :carpoolId';
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam('carpoolId', $carpoolId, PDO::PARAM_STR);
            $statement->execute();

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getReservationsNotValidatedOfACarpool() (carpool ID: $carpoolId): " . $e->getMessage());
            throw new Exception("Impossible de récupérer les réservations non validées du covoiturage");
        }
    }


    /**
     * Get the credits spent on a reservation
     * @param int $reservationId
     * @throws \Exception
     * @return int
     */
    public function getCreditSpent(int $reservationId): int
    {
        $sql = 'SELECT credits_spent FROM reservations WHERE id = :reservationId';
        $pdo = DbConnection::getPdo();
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':reservationId', $reservationId, PDO::PARAM_INT);

        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$result || !isset($result['credits_spent'])) {
            error_log("Database error in getCreditSpent() ");
            throw new Exception("Erreur lors de la récupération des crédits");
        }

        return $result['credits_spent'];
    }



    /**
     * Set the reservation as validated
     * @param int $reservationId
     * @throws \Exception If the update fails 
     * @return void
     */
    public function setValidated(int $reservationId): void
    {
        try {
            $sql = 'UPDATE reservations SET is_validated = 1 WHERE id = :reservationId';
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':reservationId', $reservationId, PDO::PARAM_INT);
            if (!$statement->execute()) {
                error_log("setValidated() failed to execute query for reservation ID $reservationId");
                throw new Exception("Échec de la validation du covoiturage");
            }
        } catch (PDOException $e) {
            error_log("Database error in setValidated() (reservation ID: $reservationId): " . $e->getMessage());
            throw new Exception("Erreur lors de la validation du covoiturage");
        }
    }

    /**
     * Stores a bad comment made by a passenger on a specific reservation.
     *
     * @param int $reservationId The ID of the reservation
     * @param string $badComment The comment content
     * @throws Exception If the update fails
     * @return void
     */
    public function addBadComment(int $reservationId, string $badComment): void
    {
        try {
            $sql = 'UPDATE reservations SET bad_comment =:badComment, bad_comment_validated = 0  WHERE id = :reservationId';
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':badComment', $badComment, PDO::PARAM_STR);
            $statement->bindParam(':reservationId', $reservationId, PDO::PARAM_INT);
            if (!$statement->execute()) {
                error_log("addBadComment() failed to execute query for reservation ID $reservationId");
                throw new Exception("Échec de l'enregistrement du litige");
            }
        } catch (PDOException $e) {
            error_log("Database error in addBadComment() (reservation ID: $reservationId): " . $e->getMessage());
            throw new Exception("Erreur lors l'ajout d'un mauvais commentaire");
        }
    }
}

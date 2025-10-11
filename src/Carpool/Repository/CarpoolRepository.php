<?php

namespace App\Carpool\Repository;

use App\Database\DbConnection;
use PDO;
use PDOException;
use Exception;

use App\Carpool\Entity\Carpool;
use App\Utils\Formatting\DateFormatter;

class CarpoolRepository
{
    public function findById(string $id): ?Carpool
    {
        try {
            $sql = "SELECT * FROM carpool WHERE id = :id";
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':id', $id, PDO::PARAM_STR);
            $statement->execute();
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            if (!$row) return null;

            return new Carpool(
                $row['id'],
                $row['driver_id'],
                $row['date'],               // 'Y-m-d' ou 'Y-m-d H:i:s'
                $row['departure_city'],
                $row['arrival_city'],
                $row['departure_time'],   // 'Y-m-d H:i:s' conseillé
                $row['arrival_time'],
                (int)$row['price'],
                (int)$row['car_id'],
                null,
                $row['description'],
                $row['status']
            );
        } catch (PDOException $e) {
            error_log("CarpoolRepository - Database error in findById() : " . $e->getMessage());
            throw new Exception("Une erreur est survenue");
        }
    }

    /**
     * Creates a new carpool in the database
     * @param string $driverId the id driver who add a carpool
     * @param string $travelDate the date of the carpool
     * @param string $travelDepartureCity the departure city of the carpool
     * @param string $travelArrivalCity the arrival city of the carpool
     * @param string $travelDepartureTime the departure time of the carpool
     * @param string $travelArrivalTime the arrival time of the carpool
     * @param int $travelPrice the price for each passenger of the carpool
     * @param int $carId the is car used for the carpool
     * @param string $travelComment the optionnal description of the carpool
     * @throws \Exception If a database error occurs
     * @return bool
     */
    public function createNewCarpool(string $driverId, string $travelDate, string $travelDepartureCity, string $travelArrivalCity, string $travelDepartureTime, string $travelArrivalTime, int $travelPrice, int $carId, ?string $travelComment = null): void
    {
        try {
            $sql = "INSERT INTO carpool (id, driver_id, date, departure_city, arrival_city, departure_time, arrival_time, price, car_id, description) VALUES (UUID(), :driverId,:travel_date, :travel_departure_city,:travel_arrival_city,:travel_departure_time,:travel_arrival_time,:travel_price,:car_id,:travelComment)";
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':driverId', $driverId, PDO::PARAM_STR);
            $statement->bindParam(':travel_date', $travelDate, PDO::PARAM_STR);
            $statement->bindParam(':travel_departure_city', $travelDepartureCity, PDO::PARAM_STR);
            $statement->bindParam(':travel_arrival_city', $travelArrivalCity, PDO::PARAM_STR);
            $statement->bindParam(':travel_departure_time', $travelDepartureTime, PDO::PARAM_STR);
            $statement->bindParam(':travel_arrival_time', $travelArrivalTime, PDO::PARAM_STR);
            $statement->bindParam(':travel_price', $travelPrice, PDO::PARAM_INT);
            $statement->bindParam(':car_id', $carId, PDO::PARAM_INT);
            $statement->bindParam(':travelComment', $travelComment, PDO::PARAM_STR);
            $statement->execute();
        } catch (PDOException $e) {
            error_log("Database error in createNewCarpool(): " . $e->getMessage());
            throw new Exception("Une erreur est survenue");
        }
    }

    // LISTE: des lignes “prêtes pour mapForList” (joins inclus)
    /** @return array[] */
    public function searchForList(
        ?string $date = null,
        ?string $departure = null,
        ?string $arrival = null,
        ?int $eco = null,
        ?int $maxPrice = null,
        ?int $maxDuration = null,
        ?float $driverRating = null
    ): array {
        $sql = "
        SELECT c.*,
               u.pseudo AS driver_pseudo,
               u.photo  AS driver_photo,
               d.user_id AS driver_id,
               cars.electric AS car_electric,
               cars.seats_offered,
               TIMESTAMPDIFF(MINUTE, c.departure_time, c.arrival_time)/60 AS carpool_duration,
               AVG(r.rating) AS driver_rating
        FROM carpool c
        JOIN users u   ON u.id = c.driver_id
        JOIN driver d  ON d.user_id = c.driver_id
        JOIN cars      ON cars.car_id = c.car_id
        LEFT JOIN ratings r ON r.driver_id = d.user_id
        WHERE c.status = 'not started'
        ";

        $params = [];
        if ($date) {
            $sql .= " AND c.date = :date";
            $params[':date'] = $date;
        }
        if ($departure) {
            $sql .= " AND c.departure_city = :dep";
            $params[':dep']  = $departure;
        }
        if ($arrival) {
            $sql .= " AND c.arrival_city   = :arr";
            $params[':arr']  = $arrival;
        }
        if ($eco) {
            $sql .= " AND cars.electric = 1";
        }
        if ($maxPrice) {
            $sql .= " AND c.price <= :maxPrice";
            $params[':maxPrice'] = $maxPrice;
        }
        if ($maxDuration) {
            $sql .= " AND TIMESTAMPDIFF(MINUTE, c.departure_time, c.arrival_time)/60 <= :maxDuration";
            $params[':maxDuration'] = $maxDuration;
        }

        $sql .= " GROUP BY c.id";
        if ($driverRating) {
            $sql .= " HAVING AVG(r.rating) >= :minRating";
            $params[':minRating'] = number_format($driverRating, 1, '.', '');
        }
        $sql .= " ORDER BY c.departure_time ASC";

        $pdo = DbConnection::getPdo();
        $statement = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $statement->bindValue($k, $v, is_int($v) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * To search for all carpools that meet the criteria
     * @param mixed $dateSearch date searched
     * @param mixed $departureCitySearch departure city searched
     * @param mixed $arrivalCitySearch arrival city searched
     * @param mixed $eco filter eco searched
     * @param mixed $maxPrice filter price searched
     * @param mixed $maxDuration filter maximum duration searched
     * @param mixed $driverRating filter minimum driver rating searched
     * @throws \Exception If a database error occurs
     * @return array
     */
    public function search(?string $dateSearch = null, ?string $departureCitySearch = null, ?string $arrivalCitySearch = null, ?int $eco = null, ?int $maxPrice = null, ?int $maxDuration = null, ?float $driverRating = null): array
    {
        $sql = "SELECT carpool.* , users.pseudo, users.photo AS driver_photo, driver.user_id AS driver_id ,
                cars.electric, cars.seats_offered AS seats_offered, TIMESTAMPDIFF(MINUTE, departure_time, arrival_time)/60 AS carpool_duration, AVG(ratings.rating) AS driver_rating 
                FROM carpool 
                JOIN users ON users.id = carpool.driver_id 
                JOIN driver ON driver.user_id = carpool.driver_id 
                JOIN cars ON cars.car_id = carpool.car_id  
                LEFT JOIN ratings ON ratings.driver_id = driver.user_id
                WHERE (date = :carpool_date) AND (departure_city = :departure_city) AND (arrival_city = :arrival_city) /* AND (status= 'not started') */";

        if (isset($eco)) {
            $sql .= " AND (electric = 1)";
        }

        if (!empty($maxPrice)) {
            $sql .= " AND (price <= :max_price)";
        }

        if (!empty($maxDuration)) {
            $sql .= " AND TIMESTAMPDIFF(MINUTE, carpool.departure_time, carpool.arrival_time)/60 <= :max_duration";
        }

        if (!empty($driverRating)) {
            $sql .= " GROUP BY carpool.id HAVING AVG(ratings.rating) >= :driver_rating";
        } else {
            $sql .= " GROUP BY carpool.id";
        }

        $sql .= " ORDER BY departure_time ASC";

        try {
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);

            $statement->bindParam(":carpool_date", $dateSearch, PDO::PARAM_STR);
            $statement->bindParam(":departure_city", $departureCitySearch, PDO::PARAM_STR);
            $statement->bindParam(":arrival_city", $arrivalCitySearch, PDO::PARAM_STR);

            if (!empty($maxPrice)) {
                $statement->bindParam(":max_price", $maxPrice, PDO::PARAM_INT);
            }

            if (!empty($maxDuration)) {
                $statement->bindParam(":max_duration", $maxDuration, PDO::PARAM_INT);
            }

            if (!empty($driverRating)) {
                $statement->bindValue(":driver_rating", number_format($driverRating, 1, '.', ''), PDO::PARAM_STR);
            }

            $statement->execute();
            $carpools = $statement->fetchAll(PDO::FETCH_ASSOC);

            return $carpools;
        } catch (PDOException $e) {
            error_log("Database error in CarpoolRepository::search() : " . $e->getMessage());
            throw new Exception("Une erreur est survenue");
        }
    }


    /**
     * To search the next date matching the criteria. return nothing if it doesn't exist
     * @param mixed $dateSearch date searched
     * @param mixed $departureCitySearch departure city searched
     * @param mixed $arrivalCitySearch arrival city searched
     * @param mixed $eco filter eco searched
     * @param mixed $maxPrice filter price searched
     * @param mixed $maxDuration filter maximum duration searched
     * @param mixed $driverRating filter minimum driver rating searched
     * @throws \Exception If a database error occurs
     * @return array
     */
    public function searchNextCarpoolDate(?string $dateSearch = null, ?string $departureCitySearch = null, ?string $arrivalCitySearch = null, ?int $eco = null, ?int $maxPrice = null, ?int $maxDuration = null, ?float $driverRating = null): ?array
    {
        try {

            $dateSearch = DateFormatter::toDb($dateSearch);

            $sql = "SELECT carpool.date, users.id AS driver_id FROM carpool 
            JOIN users ON users.id = carpool.driver_id JOIN driver ON driver.user_id = carpool.driver_id 
            JOIN cars ON cars.car_id = carpool.car_id  
            LEFT JOIN ratings ON ratings.driver_id = driver.user_id  
            WHERE (date > :carpool_date) AND (departure_city = :departure_city) AND (arrival_city = :arrival_city) /* AND (status= 'not started') */";
            if (isset($eco)) {
                $sql .= " AND (electric = 1)";
            }

            if (!empty($maxPrice)) {
                $sql .= " AND (price <= :max_price)";
            }

            if (!empty($maxDuration)) {
                $sql .= " AND TIMESTAMPDIFF(MINUTE, carpool.departure_time, carpool.arrival_time)/60 <= :max_duration";
            }

            if (!empty($driverRating)) {
                $sql .= " GROUP BY carpool.id HAVING AVG(ratings.rating) >= :driver_rating";
            } else {
                $sql .= " GROUP BY carpool.id";
            }

            $sql .= " ORDER BY date ASC LIMIT 1"; //return the first element (=the first date matching the criteria)

            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(":carpool_date", $dateSearch, PDO::PARAM_STR);
            $statement->bindParam(":departure_city", $departureCitySearch, PDO::PARAM_STR);
            $statement->bindParam(":arrival_city", $arrivalCitySearch, PDO::PARAM_STR);

            if (!empty($maxPrice)) {
                $statement->bindParam(":max_price", $maxPrice, PDO::PARAM_INT);
            }

            if (!empty($maxDuration)) {
                $statement->bindParam(":max_duration", $maxDuration, PDO::PARAM_INT);
            }

            if (!empty($driverRating)) {
                $statement->bindValue(":driver_rating", number_format($driverRating, 1, '.', ''), PDO::PARAM_STR);
            }

            $statement->execute();
            $nextCarpoolDate = $statement->fetch(PDO::FETCH_ASSOC);

            return $nextCarpoolDate ?: null;
        } catch (PDOException $e) {
            error_log("Database error in searchNextCarpoolDate(): " . $e->getMessage());
            throw new Exception("Une erreur est survenue");
        }
    }

    public function getCarpool(string $idCarpool)
    {
        try {
            $sql = "SELECT * FROM carpool WHERE id = :carpool_id";
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':carpool_id', $idCarpool, PDO::PARAM_STR);
            $statement->execute();
            $carpool = $statement->fetch(PDO::FETCH_ASSOC);
            return $carpool;
        } catch (PDOException $e) {
            error_log("Database error in getCarpool(): " . $e->getMessage());
            throw new Exception("Une erreur est survenue");
        }
    }




    /**
     * list of carpools "in validation" to be validated by the user (passenger or driver)
     * @param string $userId
     * @return array
     */
    public function getCarpoolsToValidate(string $userId): array
    {
        try {
            $sql = "SELECT carpool.*, users.pseudo, users.photo AS driver_photo, AVG(ratings.rating) AS rating, 
                MAX(reservations.is_validated) AS is_validated,
                MAX(reservations.id) AS reservationId
            FROM carpool
            LEFT JOIN reservations ON reservations.carpool_id = carpool.id AND reservations.user_id = :user1
            JOIN driver ON driver.user_id = carpool.driver_id
            JOIN users ON users.id = carpool.driver_id
            LEFT JOIN ratings ON ratings.driver_id = carpool.driver_id
            WHERE carpool.status = 'in validation' 
              AND (
                    (reservations.user_id = :user2 AND reservations.is_validated = 0)
                    OR (carpool.driver_id = :user3)
                  )
            GROUP BY carpool.id, users.id
            ORDER BY carpool.date ASC ";

            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(":user1", $userId, PDO::PARAM_STR);
            $statement->bindParam(":user2", $userId, PDO::PARAM_STR);
            $statement->bindParam(":user3", $userId, PDO::PARAM_STR);
            $statement->execute();

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getCarpoolsToValidate() : " . $e->getMessage());
            throw new Exception("Impossible d'obtenir les covoiturages à valider");
        }
    }

    /**
     * list of carpools "not started" or "in progress" 
     * @param string $userId
     * @return array
     */
    public function getCarpoolsNotStarted(string $userId): array
    {
        try {
            $sql = "SELECT carpool.*, users.pseudo, users.photo AS driver_photo, AVG(ratings.rating) AS rating, MAX(reservations.id) AS reservationId
            FROM carpool
            LEFT JOIN reservations ON reservations.carpool_id = carpool.id AND reservations.user_id = :userId
            JOIN driver ON driver.user_id = carpool.driver_id
            JOIN users ON users.id = carpool.driver_id
            LEFT JOIN ratings ON ratings.driver_id = carpool.driver_id
            WHERE (carpool.status IN ('not started', 'in progress'))
              AND (
                    reservations.user_id IS NOT NULL   
                    OR carpool.driver_id = :userId     
                  )
            GROUP BY carpool.id, users.id
            ORDER BY carpool.date ASC ";

            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':userId', $userId, PDO::PARAM_STR);

            $statement->execute();

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getCarpoolsNotStarted() : " . $e->getMessage());
            throw new Exception("Impossible d'obtenir les covoiturages non commencé");
        }
    }

    /**
     * list of carpools "ended" and validated or "cancelled"
     * @param string $userId
     * @return array
     */
    public function getCarpoolsCompleted(string $userId): array
    {
        try {
            $sql = "SELECT carpool.*, u.pseudo, u.photo AS driver_photo, AVG(r.rating) AS rating, MAX(res.is_validated) AS is_validated
            FROM carpool
            LEFT JOIN reservations res ON res.carpool_id = carpool.id AND res.user_id   = :userId
            JOIN driver d ON d.user_id = carpool.driver_id
            JOIN users u ON u.id = carpool.driver_id
            LEFT JOIN ratings r ON r.driver_id = carpool.driver_id
            WHERE (
                    (res.user_id IS NOT NULL 
                     AND (carpool.status = 'cancelled' OR res.is_validated = 1))
                 OR (carpool.driver_id = :userId 
                     AND (carpool.status = 'cancelled' OR carpool.status = 'ended'))
                  )
            GROUP BY carpool.id, u.id
            ORDER BY carpool.date ASC ";

            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':userId', $userId, PDO::PARAM_STR);

            $statement->execute();

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getCarpoolsCompleted() : " . $e->getMessage());
            throw new Exception("Impossible d'obtenir les covoiturages terminés");
        }
    }


    /**
     * Update the carpool status
     * @param string $newStatus the new statut given
     * @param string $carpoolId the carpool id for which the status is being changed
     * @throws \Exception If a database error occurs
     * @return void
     */
    public function setCarpoolStatus(string $newStatus, string $carpoolId): void
    {
        try {
            $sql = "UPDATE carpool SET status = :newStatus ";
            if ($newStatus === 'ended') {
                $sql .= ", validated_at = :currentDate";
            }
            $sql .= " WHERE id = :carpoolId";

            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':newStatus', $newStatus, PDO::PARAM_STR);
            $statement->bindParam(':carpoolId', $carpoolId, PDO::PARAM_STR);
            if ($newStatus === 'ended') {
                $today = date('Y-m-d H:i:s');
                $statement->bindParam(':currentDate', $today, PDO::PARAM_STR);
            }

            $statement->execute();
        } catch (Exception $e) {
            error_log("CarpoolRepository - Database error in setCarpoolStatus(): " . $e->getMessage());
            throw new Exception("Une erreur est survenue");
        }
    }
}

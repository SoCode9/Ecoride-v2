<?php

namespace App\Carpool\Repository;

use App\Database\DbConnection;
use PDO;

use App\Carpool\Entity\Carpool;
use App\Controller\BaseController;
use App\Utils\Formatting\DateFormatter;
use DateTime;
use PDOException;
use Exception;

class CarpoolRepository extends BaseController
{


    public function findById(string $id): ?Carpool
    {
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
        $sql = "SELECT carpool.* , users.pseudo AS driver_pseudo, users.photo AS driver_photo, driver.user_id AS driver_id ,
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
}

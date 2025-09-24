<?php

namespace App\Carpool\Repository;

use App\Database\DbConnection;
use PDO;

use App\Controller\BaseController;
use App\Utils\Formatting\DateFormatter;
use DateTime;
use PDOException;
use Exception;

class CarpoolRepository extends BaseController
{

    /**
     * 
     * @param string $dateSearch //
     * @param string $departureCitySearch //departureCity searched
     * @param string $arrivalCitySearch //arrivalCity searched
     * @return array //return the array of all travels meeting the criteria
     */
    /**
     * To search for all travels that meet the criteria
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
                WHERE (date = :travel_date) AND (departure_city = :departure_city) AND (arrival_city = :arrival_city) /* AND (status= 'not started') */";

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

            $statement->bindParam(":travel_date", $dateSearch, PDO::PARAM_STR);
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
    public function searchnextTravelDate(?string $dateSearch = null, ?string $departureCitySearch = null, ?string $arrivalCitySearch = null, ?int $eco = null, ?int $maxPrice = null, ?int $maxDuration = null, ?float $driverRating = null): ?array
    {
        try {

            $dateSearch = DateFormatter::toDb($dateSearch);

            $sql = "SELECT carpool.date, users.id AS driver_id FROM carpool 
            JOIN users ON users.id = carpool.driver_id JOIN driver ON driver.user_id = carpool.driver_id 
            JOIN cars ON cars.car_id = carpool.car_id  
            LEFT JOIN ratings ON ratings.driver_id = driver.user_id  
            WHERE (date > :travel_date) AND (departure_city = :departure_city) AND (arrival_city = :arrival_city) /* AND (status= 'not started') */";
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
            $statement->bindParam(":travel_date", $dateSearch, PDO::PARAM_STR);
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
            $nextTravelDate = $statement->fetch(PDO::FETCH_ASSOC);

            return $nextTravelDate ?: null;
        } catch (PDOException $e) {
            error_log("Database error in searchnextTravelDate(): " . $e->getMessage());
            throw new Exception("Une erreur est survenue");
        }
    }
}

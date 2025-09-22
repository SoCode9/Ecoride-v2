<?php

namespace App\Carpool\Repository;

use App\Database\DbConnection;
use PDO;

use App\Controller\BaseController;
use App\Utils\DateFormatter;
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
        // Normalize date dd.mm.yyyy -> yyyy-mm-dd
        if (!empty($dateSearch)) {
            $dateObject = DateTime::createFromFormat('d.m.Y', $dateSearch);
            if ($dateObject) {
                $dateSearch = $dateObject->format('Y-m-d');
            } else {
                // on peut aussi décider de renvoyer vide plutôt que d'erreur
                throw new Exception('Invalid date format. Expected dd.mm.yyyy');
            }
        }


        $sql = "SELECT * FROM carpool/* , users.pseudo AS driver_pseudo, users.photo AS driver_photo, AVG(ratings.rating) AS driver_rating, driver.user_id AS driver_id,
                cars.car_electric AS car_electric, cars.car_seats_offered AS seats_offered, TIMESTAMPDIFF(MINUTE, travel_departure_time, travel_arrival_time)/60 AS travel_duration 
                FROM travels 
                JOIN users ON users.id = travels.driver_id 
                JOIN driver ON driver.user_id = travels.driver_id 
                JOIN cars ON cars.car_id = travels.car_id  
                LEFT JOIN ratings ON ratings.driver_id = driver.user_id */
                WHERE (date = :travel_date) AND (departure_city = :departure_city) AND (arrival_city = :arrival_city) AND (status= 'not started')";

        /*   if (isset($eco)) {
                $sql .= " AND (car_electric = 1)";
            } */

        if (!empty($maxPrice)) {
            $sql .= " AND (price <= :max_price)";
        }

        if (!empty($maxDuration)) {
            $sql .= " AND TIMESTAMPDIFF(MINUTE, carpool.departure_time, carpool.arrival_time)/60 <= :max_duration";
        }

        /*  if (!empty($driverRating)) {
                $sql .= " GROUP BY travels.id HAVING AVG(ratings.rating) >= :driver_rating";
            } else {
                $sql .= " GROUP BY travels.id";
            } */

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

            /*      if (!empty($driverRating)) {
                $statement->bindValue(":driver_rating", number_format($driverRating, 1, '.', ''), PDO::PARAM_STR);
            } */

            $statement->execute();
            $carpools = $statement->fetchAll(PDO::FETCH_ASSOC);

            /*  //clean data with the right format
            foreach ($carpools as &$carpool) {
                $carpool['date'] = DateFormatter::short($carpool['date']);
                $carpool['departure_time'] = DateFormatter::time($carpool['departure_time']);
                $carpool['arrival_time'] = DateFormatter::time($carpool['arrival_time']);
            } */

            return $carpools;
        } catch (PDOException $e) {
            error_log("Database error in CarpoolRepository::search() : " . $e->getMessage());
            throw new Exception("Une erreur est survenue");
        }
    }
}

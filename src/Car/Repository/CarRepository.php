<?php

namespace App\Car\Repository;

use App\Database\DbConnection;
use PDO;

use App\Controller\BaseController;
use App\Utils\Formatting\DateFormatter;
use DateTime;
use PDOException;
use Exception;

class CarRepository extends BaseController
{

    /**
     * Returns the number of seats offered by a specific car.
     * @param int $carId The ID of the car.
     * @throws \Exception If the car does not exist.
     * @return int Number of seats offered.
     */
    public function getSeatsOfferedByCar(int $carId): int
    {
        $sql = "SELECT seats_offered FROM cars WHERE car_id = :carId";

        $pdo = DbConnection::getPdo();
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':carId', $carId, PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            throw new Exception("Aucune voiture trouvée pour cet id : $carId");
        }

        return (int) $result['seats_offered'];
    }

    /**
     *Loads car information from the database based on a carpool ID (single car) 
     * or a driver ID (multiple cars).
     * @param mixed $carpoolId If provided, loads a single car associated with this carpool.
     * @param mixed $driverId If provided, loads all cars associated with this driver.
     * @throws \Exception If no car is found when searching by carpool ID.
     * @return void
     */
    public function getCar(?string $carpoolId = null, ?string $driverId = null)
    {
        $sql = "SELECT cars.*, brands.* FROM cars 
        JOIN driver ON driver.user_id = cars.driver_id 
        JOIN brands ON brands.id = cars.brand_id";

        $conditions = [];
        $params = [];

        // If carpool ID is provided, join with carpools and filter
        if (!empty($carpoolId)) {
            $sql .= "  JOIN carpool ON carpool.car_id =cars.car_id";
            $conditions[] = "carpool.id = :carpool_id";
            $params[':carpool_id'] = $carpoolId;
        }

        // If driver ID is provided, filter accordingly
        if (!empty($driverId)) {
            $conditions[] = "driver.user_id = :driver_id";
            $params[':driver_id'] = $driverId;
        }

        // Apply WHERE clause if needed
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $pdo = DbConnection::getPdo();
        $statement = $pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $statement->bindValue($key, $value, PDO::PARAM_STR);
        }

        $statement->execute();

        if (!empty($carpoolId)) {
            // Expecting a single car result
            $car = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$car) {
                throw new Exception("Aucune voiture trouvée pour ce trajet");
            }

            return $car;
        } elseif (!empty($driverId)) {
            // Expecting multiple cars
            $cars = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $cars;
        }
    }
}

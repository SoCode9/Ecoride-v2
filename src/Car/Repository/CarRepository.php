<?php

namespace App\Car\Repository;

use App\Database\DbConnection;
use PDO;
use PDOException;
use Exception;

use App\Car\Entity\Car;

class CarRepository
{
    public function findById(int $id): ?Car
    {
        try {
            $sql = "SELECT cars.*, brands.* FROM cars 
            JOIN brands ON brands.id = cars.brand_id
            WHERE car_id = :id";
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':id', $id, PDO::PARAM_STR);
            $statement->execute();
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            if (!$row) return null;

            return new Car(
                $row['car_id'],
                $row['brand_id'],
                $row['name'],
                $row['driver_id'],
                $row['licence_plate'],
                $row['first_registration_date'],
                $row['seats_offered'],
                $row['model'],
                $row['color'],
                $row['electric'],
            );
        } catch (PDOException $e) {
            error_log("CarRepository - Database error in findById() : " . $e->getMessage());
            throw new Exception("Une erreur est survenue");
        }
    }


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

    public function findAllCars(string $driverId)
    {
        try {
            $sql = "SELECT cars.*, brands.* FROM cars 
        JOIN driver ON driver.user_id = cars.driver_id 
        JOIN brands ON brands.id = cars.brand_id
        WHERE driver.user_id = :driver_id";
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':driver_id', $driverId, PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        } catch (PDOException $e) {
            error_log("CarRepository - Database error in findAllCars() : " . $e->getMessage());
            throw new Exception("Une erreur est survenue");
        }
    }

    /**
     *Loads car information from the database based on a carpool ID (single car) 
     * or a driver ID (multiple cars).
     * @param mixed $carpoolId If provided, loads a single car associated with this carpool.
     * @param mixed $driverId If provided, loads all cars associated with this driver.
     * @throws \Exception If no car is found when searching by carpool ID.
     * @return void
     */
    /*  public function getCar(?string $carpoolId = null, ?string $driverId = null)
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
    } */
}

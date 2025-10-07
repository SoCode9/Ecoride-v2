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

    public function findAllBrands(): array
    {
        try {
            $sql = "SELECT * FROM brands";
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        } catch (PDOException $e) {
            error_log("CarRepository - Database error in findAllBrands() : " . $e->getMessage());
            throw new Exception("Une erreur est survenue");
        }
    }

    /**
     * Creates a new car for the specified user
     * @param string $userId
     * @param string $brandId
     * @param string $model
     * @param string $licencePlate
     * @param string $firstRegistrationDate
     * @param int $seatsOffered
     * @param bool $electric
     * @param string $color
     * @return array{car_id: bool|string, message: string, success: bool|array{error: string, success: bool}} Result with success status and message
     */
    public function newCar(string $driverId, string $brandId, string $model, string $licencePlate, string $firstRegistrationDate, int $seatsOffered, bool $electric, string $color): array
    {
        try {
            $this->ensureDriver($driverId);
            $sql = 'INSERT INTO cars (brand_id,driver_id,licence_plate,first_registration_date, seats_offered,model,color,electric) 
        VALUES (:brand_id, :driver_id, :licence_plate, :first_reg_date, :seats, :model, :color, :electric)';

            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':brand_id' => $brandId,
                ':driver_id' => $driverId,
                ':licence_plate' => $licencePlate,
                ':first_reg_date' => $firstRegistrationDate,
                ':seats' => $seatsOffered,
                ':model' => $model,
                ':color' => $color,
                ':electric' => $electric,
            ]);

            return [
                'success' => true,
                'message' => 'Véhicule enregistré avec succès',
                'car_id' => $pdo->lastInsertId()
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => "Erreur lors de la création du véhicule : " . $e->getMessage()
            ];
        }
    }

    /**
     * Ensures that the user is a driver. If not, adds them as a driver
     * and updates their role in the users table.
     * @param string $userId
     * @return void
     */
    private function ensureDriver(string $userId): void
    {
        $pdo = DbConnection::getPdo();

        // Check if the user is already a driver
        $statement = $pdo->prepare('SELECT user_id FROM driver WHERE user_id = :userId');
        $statement->execute([':userId' => $userId]);

        if (!$statement->fetch()) {
            // Add the user as a driver
            $pdo->prepare('INSERT INTO driver (user_id) VALUES (:userId)')
                ->execute([':userId' => $userId]);

            // Update user's role to "driver" (role_id = 2)
            $pdo->prepare('UPDATE users SET id_role = 2 WHERE id = :userId')
                ->execute([':userId' => $userId]);
        }
    }
}

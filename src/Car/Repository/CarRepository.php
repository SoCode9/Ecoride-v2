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
}

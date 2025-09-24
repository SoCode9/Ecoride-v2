<?php

namespace App\Carpool\Service;

use App\Carpool\Repository\CarpoolRepository;
use App\Utils\Formatting\DateFormatter;
use App\Utils\Formatting\OtherFormatter;

final class CarpoolService
{
    public function __construct(private CarpoolRepository $repo) {}

    public function searchWithFormatting(array $filters): array
    {
        $items = $this->repo->search(
            $filters['date'] ?? null,
            $filters['departure'] ?? null,
            $filters['arrival'] ?? null,
            $filters['eco'] ?? null,
            $filters['maxPrice'] ?? null,
            $filters['maxDuration'] ?? null,
            $filters['driverRating'] ?? null,
        );

        // formatage pour le front
     /*   foreach ($items as &$row) {
            if (!empty($row['departure_time'])) {
                $row['departure_time'] = DateFormatter::time($row['departure_time']);
            }
            if (!empty($row['arrival_time'])) {
                $row['arrival_time'] = DateFormatter::time($row['arrival_time']);
            }

             if ($row['car_electric'] === 1) {
                $row['car_electric'] = OtherFormatter::formatEco($row['car_electric']);
            } */

           /*  var_dump('<pre>');
            var_dump($row);
            var_dump('/<pre>'); 
        }
*/
        return $items;
    }
}

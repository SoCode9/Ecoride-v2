<?php

namespace App\Driver\Service;

use App\Driver\Repository\DriverRepository;

final class DriverService
{
    public function __construct(private DriverRepository $repo) {}

    /**
     * Calculates the average rating for the driver based on validated ratings
     * @return float|null The average rating (e.g., 4.2), or null if no rating is available
     */
    public function getAverageRatings($driverId): ?float
    {
        $allInfoRatings = $this->repo->loadValidatedRatings($driverId);
        if (empty($allInfoRatings)) {
            return null; // if the driver has no rating
        }
        $average = array_sum(array_column($allInfoRatings, 'rating')) / count($allInfoRatings);
        return round($average, 1);
    }
}

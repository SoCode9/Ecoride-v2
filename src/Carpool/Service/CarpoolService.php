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
        return $items;
    }
}

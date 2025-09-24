<?php

namespace App\Utils\Formatting;

final class OtherFormatter
{

    public static function formatEcoLabel(bool $eco): string
    {
        return $eco ? 'Économique' : '';
    }

    public static function formatCredits(int|float $price): string
    {
        $price = (int) $price;
        return $price <= 1 ? "$price crédit" : "$price crédits";
    }

    public static function displayPhoto(?string $fileName = null): string
    {
        if (!$fileName) {
            return ASSETS_PATH . '/icons/default-user.png';
        }

        $safeFileName = basename($fileName);
        $realPath = PHOTOS_DIR . '/' . $safeFileName;

        if (!is_file($realPath)) {
            return ASSETS_PATH . '/icons/default-user.png';
        }

        return PHOTOS_URL . '/' . rawurlencode($safeFileName);
    }

    /**
     * calculation of seatsAvailable with informations in DB
     * @param int $seatsOfferedNb //field in DB
     * @param int $seatsAllowedNb //field in DB
     * @return int
     */
    public static function seatsAvailable(int $seatsOfferedNb, int $seatsAllocatedNb): int
    {
        $seatsAvailable = $seatsOfferedNb - $seatsAllocatedNb;
        return $seatsAvailable;
    }
}

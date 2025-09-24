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
}

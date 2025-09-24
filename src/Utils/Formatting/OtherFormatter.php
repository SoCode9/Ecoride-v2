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
}

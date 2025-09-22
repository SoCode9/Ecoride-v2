<?php

namespace App\Utils\Formatting;

final class OtherFormatter
{

    public static function formatEco(bool $nbEco): string
    {
        if ($nbEco == 1) {
            return '<img src="' . ASSETS_PATH . 'icons/Arbre1.png" alt="Arbre" width="20px">' . " Economique";
        } else {
            return "";
        }
    }
}

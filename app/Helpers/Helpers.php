<?php

namespace App\Helpers;

class Helpers
{
    public static function formatearDinero($numero)
    {
        return number_format($numero, 0, ',', '.');
    }
}

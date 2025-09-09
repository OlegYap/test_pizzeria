<?php

namespace App\Enums;

enum ProductEnum: string
{
    case Pizza = 'pizza';
    case Drink = 'drink';


    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

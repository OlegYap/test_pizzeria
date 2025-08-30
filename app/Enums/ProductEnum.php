<?php

namespace App\Enums;

enum ProductEnum
{
    public const Pizza = 'pizza';
    public const Drink = 'drink';

    public function values(): array
    {
        return array_combine(self::cases(), 'value');
    }
}

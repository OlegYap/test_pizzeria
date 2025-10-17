<?php

namespace App\Enums;

enum StatusEnum: string
{
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';

    case DELIVERED = 'delivered';

    case CANCELLED = 'cancelled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function labels(): string
    {
        return match ($this) {
            self::PROCESSING => __('status.processing'),
            self::SHIPPED => __('status.shipping'),
            self::DELIVERED => __('status.delivered'),
            self::CANCELLED => __('status.cancelled'),
        };
    }

}

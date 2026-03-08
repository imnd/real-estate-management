<?php

declare(strict_types=1);

namespace App\Enums;

enum PremiseHistoryField: string
{
    case Status = 'status';
    case BasePrice = 'base_price';
    case DiscountPrice = 'discount_price';

    public static function mappedFields(): array
    {
        return [
            self::Status->value => self::Status->label(),
            self::BasePrice->value => self::BasePrice->label(),
            self::DiscountPrice->value => self::DiscountPrice->label(),
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::Status => 'Статус',
            self::BasePrice => 'Базовая цена',
            self::DiscountPrice => 'Цена со скидкой',
        };
    }

    public static function getList(): array
    {
        return [
            self::Status->value,
            self::BasePrice->value,
            self::DiscountPrice->value,
        ];
    }
}

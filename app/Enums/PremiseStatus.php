<?php

declare(strict_types=1);

namespace App\Enums;

enum PremiseStatus: string
{
    case Available = 'available';
    case Reserved = 'reserved';
    case Sold = 'sold';
    case NotForSale = 'not_for_sale';

    public static function mappedStatuses(): array
    {
        return [
            self::Available->value => self::Available->label(),
            self::Reserved->value => self::Reserved->label(),
            self::Sold->value => self::Sold->label(),
            self::NotForSale->value => self::NotForSale->label(),
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Доступно',
            self::Reserved => 'Забронировано',
            self::Sold => 'Продано',
            self::NotForSale => 'Не продается',
        };
    }

    public static function getList(): array
    {
        return [
            self::Available->value,
            self::Reserved->value,
            self::Sold->value,
            self::NotForSale->value,
        ];
    }
}

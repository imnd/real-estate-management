<?php

declare(strict_types=1);

namespace App\Enums;

enum PremiseType: string
{
    case Apartment = 'apartment';
    case Studio = 'studio';
    case Penthouse = 'penthouse';
    case Commercial = 'commercial';

    public static function mappedTypes(): array
    {
        return [
            self::Apartment->value => self::Apartment->label(),
            self::Studio->value => self::Studio->label(),
            self::Penthouse->value => self::Penthouse->label(),
            self::Commercial->value => self::Commercial->label(),
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::Apartment => 'Квартира',
            self::Studio => 'Студия',
            self::Penthouse => 'Пентхаус',
            self::Commercial => 'Коммерческое помещение',
        };
    }

    public static function getList(): array
    {
        return [
            self::Apartment->value,
            self::Studio->value,
            self::Penthouse->value,
            self::Commercial->value,
        ];
    }
}

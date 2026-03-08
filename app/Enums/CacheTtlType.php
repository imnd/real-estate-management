<?php

declare(strict_types=1);

namespace App\Enums;

enum CacheTtlType: string
{
    case Complexes = 'complexes';
    case Buildings = 'buildings';
    case Sections = 'sections';
    case Premises = 'premises';
    case Statistics = 'statistics';
    case Filters = 'filters';
    case Dashboard = 'dashboard';
    case Default = 'default';

    public static function getList(): array
    {
        return [
            self::Complexes->value,
            self::Buildings->value,
            self::Sections->value,
            self::Premises->value,
            self::Statistics->value,
            self::Filters->value,
            self::Dashboard->value,
        ];
    }
}

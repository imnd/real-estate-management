<?php

declare(strict_types=1);

namespace App\Enums;

enum ComplexStatus: string
{
    case Planning = 'planning';
    case Construction = 'construction';
    case Completed = 'completed';

    public static function mappedTypes(): array
    {
        return [
            self::Planning->value => self::Planning->label(),
            self::Construction->value => self::Construction->label(),
            self::Completed->value => self::Completed->label(),
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::Planning => 'Планирование',
            self::Construction => 'Строительство',
            self::Completed => 'Завершен',
        };
    }

    public static function getList(): array
    {
        return [
            self::Planning->value,
            self::Construction->value,
            self::Completed->value,
        ];
    }
}

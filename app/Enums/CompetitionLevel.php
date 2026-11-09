<?php

namespace App\Enums;

enum CompetitionLevel: string
{
    case REGIONAL = 'REGIONAL';
    case NASIONAL = 'NASIONAL';
    case INTERNASIONAL = 'INTERNASIONAL';

    public function label(): string
    {
        return match ($this) {
            self::REGIONAL => 'Regional',
            self::NASIONAL => 'Nasional',
            self::INTERNASIONAL => 'Internasional',
        };
    }
}

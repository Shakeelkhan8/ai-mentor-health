<?php

namespace App\Domains\Identity\Enums;

enum HealthFocusArea: string
{
    case Fitness = 'fitness';
    case Nutrition = 'nutrition';
    case Sleep = 'sleep';
    case Stress = 'stress';
    case Preventive = 'preventive';

    public function label(): string
    {
        return match ($this) {
            self::Fitness => 'Fitness',
            self::Nutrition => 'Nutrition',
            self::Sleep => 'Sleep',
            self::Stress => 'Stress management',
            self::Preventive => 'Preventive health',
        };
    }
}

<?php

namespace App\Domains\Plans\Enums;

enum PlanStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Completed = 'completed';
    case Archived = 'archived';
}

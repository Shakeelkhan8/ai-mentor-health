<?php

namespace App\Domains\Habits\Enums;

enum CheckInStatus: string
{
    case Done = 'done';
    case Skipped = 'skipped';
    case Partial = 'partial';
}

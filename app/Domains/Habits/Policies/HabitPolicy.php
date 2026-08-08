<?php

namespace App\Domains\Habits\Policies;

use App\Domains\Habits\Models\Habit;
use App\Models\User;

class HabitPolicy
{
    public function view(User $user, Habit $habit): bool
    {
        return (int) $user->id === (int) $habit->user_id;
    }

    public function update(User $user, Habit $habit): bool
    {
        return (int) $user->id === (int) $habit->user_id;
    }
}

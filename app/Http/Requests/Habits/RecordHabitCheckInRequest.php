<?php

namespace App\Http\Requests\Habits;

use App\Domains\Habits\Enums\CheckInStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecordHabitCheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::enum(CheckInStatus::class)],
            'note' => ['nullable', 'string', 'max:500'],
            'mood_score' => ['nullable', 'integer', 'min:1', 'max:10'],
        ];
    }
}

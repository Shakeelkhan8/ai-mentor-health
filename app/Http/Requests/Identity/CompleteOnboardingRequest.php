<?php

namespace App\Http\Requests\Identity;

use App\Domains\Identity\Enums\HealthFocusArea;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompleteOnboardingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'focus_areas' => ['required', 'array', 'min:1', 'max:5'],
            'focus_areas.*' => ['required', 'string', Rule::enum(HealthFocusArea::class)],
            'primary_goal' => ['required', 'string', 'min:5', 'max:500'],
            'timezone' => ['nullable', 'timezone'],
        ];
    }

    public function messages(): array
    {
        return [
            'focus_areas.required' => 'Choose at least one wellness focus area.',
            'primary_goal.required' => 'Tell us the main goal you want coaching support with.',
        ];
    }
}

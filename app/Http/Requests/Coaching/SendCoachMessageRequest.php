<?php

namespace App\Http\Requests\Coaching;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendCoachMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'min:1', 'max:2000'],
            'conversation_id' => [
                'nullable',
                'integer',
                Rule::exists('coach_conversations', 'id')->where(
                    fn ($query) => $query->where('user_id', $this->user()->id)
                ),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('message') && $this->filled('search')) {
            $this->merge([
                'message' => $this->input('search'),
            ]);
        }

        if (! $this->filled('conversation_id')) {
            $this->merge(['conversation_id' => null]);
        }
    }
}

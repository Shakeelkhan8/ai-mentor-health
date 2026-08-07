<?php

namespace App\Domains\Coaching\Models;

use App\Domains\Coaching\Enums\MessageRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoachMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'user_id',
        'role',
        'content',
        'meta',
    ];

    protected $casts = [
        'role' => MessageRole::class,
        'meta' => 'array',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(CoachConversation::class, 'conversation_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

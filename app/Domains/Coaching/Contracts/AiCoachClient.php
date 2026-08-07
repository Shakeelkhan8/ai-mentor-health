<?php

namespace App\Domains\Coaching\Contracts;

/**
 * Port for AI coaching providers (RapidAPI, OpenAI, etc.).
 * Domain depends on this contract — Infrastructure implements it.
 */
interface AiCoachClient
{
    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     */
    public function complete(array $messages, array $options = []): string;
}

<?php

namespace App\Infrastructure\Ai;

use App\Domains\Coaching\Contracts\AiCoachClient;
use App\Domains\Shared\Exceptions\DomainException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * RapidAPI-backed implementation of AiCoachClient.
 * Swap this binding without changing domain code.
 */
class RapidApiCoachClient implements AiCoachClient
{
    public function __construct(
        private readonly Client $http = new Client(['timeout' => 30]),
    ) {
    }

    public function complete(array $messages, array $options = []): string
    {
        $apiKey = config('services.rapidapi.key');

        if (blank($apiKey)) {
            throw new DomainException(
                'AI coach is not configured. Set RAPIDAPI_KEY in your environment.',
                'ai_not_configured',
            );
        }

        try {
            $response = $this->http->post(config('services.rapidapi.coach_url'), [
                'headers' => [
                    'x-rapidapi-key' => $apiKey,
                    'x-rapidapi-host' => config('services.rapidapi.host'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => config('services.rapidapi.model'),
                    'messages' => $messages,
                ],
            ]);
        } catch (GuzzleException $e) {
            throw new DomainException(
                'Unable to reach the AI coaching provider.',
                'ai_provider_error',
                previous: $e,
            );
        }

        $payload = json_decode((string) $response->getBody(), true);

        return (string) data_get(
            $payload,
            'choices.0.message.content',
            data_get($payload, 'content', 'I could not generate a response right now. Please try again.')
        );
    }
}

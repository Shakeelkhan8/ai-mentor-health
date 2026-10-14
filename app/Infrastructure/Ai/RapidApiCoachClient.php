<?php

namespace App\Infrastructure\Ai;

use App\Domains\Coaching\Contracts\AiCoachClient;
use App\Domains\Shared\Exceptions\DomainException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

/**
 * RapidAPI-backed implementation of AiCoachClient.
 * Swap this binding without changing domain code.
 *
 * Reliability policy (it runs inside a web request, so the total wait stays bounded):
 * - each attempt: 5 s to connect, 25 s overall;
 * - up to 3 attempts, retrying only failures that are likely to clear quickly:
 *   connection errors and HTTP 429/502/503/504, with a short exponential backoff.
 *   A read timeout is not retried: it has already used the time budget;
 * - an empty or malformed reply is an error, never a made-up answer, so callers
 *   don't persist it as something the coach said.
 * Every failed attempt is logged as a warning, the final one as an error by the caller.
 */
class RapidApiCoachClient implements AiCoachClient
{
    private const MAX_ATTEMPTS = 3;

    private const CONNECT_TIMEOUT = 5;

    private const TIMEOUT = 25;

    private const RETRYABLE_STATUSES = [429, 502, 503, 504];

    public function __construct(
        private readonly Client $http = new Client(),
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

        for ($attempt = 1; ; $attempt++) {
            try {
                $response = $this->http->post(config('services.rapidapi.coach_url'), [
                    'connect_timeout' => self::CONNECT_TIMEOUT,
                    'timeout' => self::TIMEOUT,
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

                return $this->content((string) $response->getBody());
            } catch (GuzzleException $e) {
                $retryable = $this->isRetryable($e);

                Log::warning('AI provider request failed', [
                    'attempt' => $attempt,
                    'status' => $e instanceof RequestException ? $e->getResponse()?->getStatusCode() : null,
                    'error' => class_basename($e),
                    'will_retry' => $retryable && $attempt < self::MAX_ATTEMPTS,
                ]);

                if (! $retryable || $attempt >= self::MAX_ATTEMPTS) {
                    throw new DomainException(
                        'Unable to reach the AI coaching provider.',
                        'ai_provider_error',
                        previous: $e,
                    );
                }

                usleep($this->backoffMicroseconds($attempt));
            }
        }
    }

    private function isRetryable(GuzzleException $e): bool
    {
        if ($e instanceof ConnectException) {
            // A connect failure is quick to retry; a read timeout has used the budget already.
            return ! str_contains(strtolower($e->getMessage()), 'operation timed out');
        }

        if ($e instanceof RequestException && $e->getResponse() !== null) {
            return in_array($e->getResponse()->getStatusCode(), self::RETRYABLE_STATUSES, true);
        }

        return false;
    }

    private function backoffMicroseconds(int $attempt): int
    {
        $baseMs = (int) config('services.rapidapi.retry_delay_ms', 250);

        return $baseMs * (2 ** ($attempt - 1)) * 1000;
    }

    private function content(string $body): string
    {
        $payload = json_decode($body, true);
        $content = is_array($payload)
            ? data_get($payload, 'choices.0.message.content', data_get($payload, 'content'))
            : null;

        if (! is_string($content) || trim($content) === '') {
            throw new DomainException('The AI coaching provider returned an empty reply.', 'ai_provider_error');
        }

        return trim($content);
    }
}

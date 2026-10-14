<?php

namespace Tests\Feature\Coaching;

use App\Domains\Shared\Exceptions\DomainException;
use App\Infrastructure\Ai\RapidApiCoachClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;

class RapidApiCoachClientTest extends TestCase
{
    /** @var list<array<string, mixed>> request options seen by the fake server */
    private array $sent = [];

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.rapidapi.key' => 'test-key',
            'services.rapidapi.retry_delay_ms' => 0,
        ]);
    }

    /**
     * @param  list<Response|\Throwable>  $responses
     */
    private function client(array $responses): RapidApiCoachClient
    {
        $mock = new MockHandler(array_map(
            fn ($response) => function ($request, array $options) use ($response) {
                $this->sent[] = $options;

                if ($response instanceof \Throwable) {
                    throw $response;
                }

                return $response;
            },
            $responses,
        ));

        return new RapidApiCoachClient(new Client(['handler' => HandlerStack::create($mock)]));
    }

    private function reply(string $content): Response
    {
        return new Response(200, [], json_encode(['choices' => [['message' => ['content' => $content]]]]));
    }

    private function messages(): array
    {
        return [['role' => 'user', 'content' => 'Hi']];
    }

    private function assertProviderError(callable $call): void
    {
        try {
            $call();
            $this->fail('Expected an ai_provider_error.');
        } catch (DomainException $e) {
            $this->assertSame('ai_provider_error', $e->errorCode);
        }
    }

    public function test_retries_a_temporary_server_error_then_succeeds(): void
    {
        $client = $this->client([new Response(503), $this->reply('Sleep at the same time each night.')]);

        $this->assertSame('Sleep at the same time each night.', $client->complete($this->messages()));
        $this->assertCount(2, $this->sent);
    }

    public function test_retries_a_connection_failure_then_succeeds(): void
    {
        $client = $this->client([
            new ConnectException('Connection refused', new Request('POST', 'https://example.test')),
            $this->reply('Take a short walk after lunch.'),
        ]);

        $this->assertSame('Take a short walk after lunch.', $client->complete($this->messages()));
        $this->assertCount(2, $this->sent);
    }

    public function test_gives_up_after_three_attempts(): void
    {
        $client = $this->client([new Response(503), new Response(502), new Response(504)]);

        $this->assertProviderError(fn () => $client->complete($this->messages()));
        $this->assertCount(3, $this->sent);
    }

    public function test_does_not_retry_a_reply_that_timed_out(): void
    {
        // A read timeout has already used the whole time budget; retrying would double the wait.
        $client = $this->client([
            new ConnectException('cURL error 28: Operation timed out after 25001 milliseconds', new Request('POST', 'https://example.test')),
            $this->reply('never used'),
        ]);

        $this->assertProviderError(fn () => $client->complete($this->messages()));
        $this->assertCount(1, $this->sent);
    }

    public function test_does_not_retry_a_client_error(): void
    {
        $client = $this->client([new Response(400), $this->reply('never used')]);

        $this->assertProviderError(fn () => $client->complete($this->messages()));
        $this->assertCount(1, $this->sent);
    }

    public function test_empty_or_malformed_reply_is_an_error_not_a_fake_answer(): void
    {
        $this->assertProviderError(fn () => $this->client([$this->reply('   ')])->complete($this->messages()));
        $this->assertProviderError(fn () => $this->client([new Response(200, [], 'not json')])->complete($this->messages()));
    }

    public function test_requests_use_bounded_timeouts(): void
    {
        $this->client([$this->reply('ok')])->complete($this->messages());

        $this->assertSame(5, $this->sent[0]['connect_timeout']);
        $this->assertSame(25, $this->sent[0]['timeout']);
    }
}

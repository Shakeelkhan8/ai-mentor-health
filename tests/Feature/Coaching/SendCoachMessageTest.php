<?php

namespace Tests\Feature\Coaching;

use App\Domains\Coaching\Contracts\AiCoachClient;
use App\Domains\Coaching\Enums\MessageRole;
use App\Domains\Coaching\Models\CoachMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SendCoachMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_send_coach_message_and_persist_history(): void
    {
        $this->mock(AiCoachClient::class, function ($mock) {
            $mock->shouldReceive('complete')
                ->once()
                ->andReturn('Try a consistent bedtime routine tonight.');
        });

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->postJson(route('coach.messages.store'), [
            'message' => 'I want better sleep.',
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Try a consistent bedtime routine tonight.');

        $this->assertDatabaseCount('coach_conversations', 1);
        $this->assertDatabaseCount('coach_messages', 2);

        $this->assertTrue(
            CoachMessage::query()->where('role', MessageRole::User->value)->where('content', 'I want better sleep.')->exists()
        );
        $this->assertTrue(
            CoachMessage::query()->where('role', MessageRole::Assistant->value)->exists()
        );
    }

    public function test_guest_cannot_send_coach_message(): void
    {
        $this->postJson(route('coach.messages.store'), [
            'message' => 'Hello',
        ])->assertUnauthorized();
    }
}

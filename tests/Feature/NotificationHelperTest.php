<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class NotificationHelperTest extends TestCase
{
    use RefreshDatabase;

    public function test_failed_notification_is_logged_instead_of_crashing(): void
    {
        Log::spy();

        // user_id is required, so the insert fails and the helper's error path runs.
        $result = createNotification(null, 'Payment Success', 'Appointment booked');

        $this->assertNull($result);
        Log::shouldHaveReceived('warning')
            ->withArgs(fn ($message, $context) => $message === 'Notification creation failed'
                && array_key_exists('error', $context))
            ->once();
    }
}

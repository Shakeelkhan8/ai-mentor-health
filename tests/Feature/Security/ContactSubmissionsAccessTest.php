<?php

namespace Tests\Feature\Security;

use App\Domains\Identity\Enums\HealthFocusArea;
use App\Models\ContactForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactSubmissionsAccessTest extends TestCase
{
    use RefreshDatabase;

    private function onboardedUser(string $role): User
    {
        $user = User::factory()->create(['role' => $role]);

        $user->profile()->create([
            'focus_areas' => [HealthFocusArea::Sleep->value],
            'primary_goal' => 'Sleep consistently',
            'timezone' => 'UTC',
            'onboarding_completed_at' => now(),
        ]);

        return $user;
    }

    private function submission(): ContactForm
    {
        return ContactForm::query()->create([
            'name' => 'Visitor Name',
            'email' => 'visitor@example.com',
            'phone' => '0300 1234567',
            'message' => 'Please call me back.',
        ]);
    }

    public function test_regular_user_cannot_view_contact_submissions(): void
    {
        $this->submission();

        $this->actingAs($this->onboardedUser('user'))
            ->get(route('community-forms'))
            ->assertForbidden()
            ->assertDontSee('visitor@example.com');
    }

    public function test_admin_can_view_contact_submissions(): void
    {
        $this->submission();

        $this->actingAs($this->onboardedUser('admin'))
            ->get(route('community-forms'))
            ->assertOk()
            ->assertSee('visitor@example.com');
    }

    public function test_registration_cannot_grant_the_admin_role(): void
    {
        $this->post('/register', [
            'name' => 'Sneaky User',
            'email' => 'sneaky@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
        ]);

        $this->assertSame('user', User::query()->where('email', 'sneaky@example.com')->sole()->role);
    }
}

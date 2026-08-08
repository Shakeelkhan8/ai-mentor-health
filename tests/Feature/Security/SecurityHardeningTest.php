<?php

namespace Tests\Feature\Security;

use App\Models\Appointment;
use App\Models\ContactForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_user_cannot_access_dashboard(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_role_is_not_mass_assignable(): void
    {
        $user = User::factory()->create();

        $user->fill(['role' => 'admin'])->save();

        $this->assertNotSame('admin', $user->fresh()->role);
    }

    public function test_newsletter_signup_does_not_send_mail_and_requires_email(): void
    {
        Mail::fake();

        $this->post(route('sendmail'), [])
            ->assertSessionHasErrors('email');

        $this->post(route('sendmail'), ['email' => 'member@example.com'])
            ->assertRedirect();

        Mail::assertNothingSent();
        $this->assertDatabaseHas('contact_forms', [
            'email' => 'member@example.com',
            'message' => 'Newsletter signup request',
        ]);
        $this->assertSame(1, ContactForm::query()->count());
    }

    public function test_appointment_success_rejects_forged_query_params_without_session(): void
    {
        config(['mentor.features.care_marketplace' => true]);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->profile()->create([
            'focus_areas' => ['sleep'],
            'primary_goal' => 'Rest',
            'timezone' => 'UTC',
            'onboarding_completed_at' => now(),
        ]);

        $this->actingAs($user)
            ->from(route('dashboard'))
            ->get(route('appointment.success', [
                'user_id' => $user->id,
                'doctor_id' => 1,
                'appointment_date' => '2026-08-10',
                'start_time' => '10:00:00',
            ]))
            ->assertRedirect()
            ->assertSessionHasErrors('session_id');

        $this->assertSame(0, Appointment::query()->count());
    }

    public function test_non_admin_cannot_view_community_forms(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'user',
        ]);
        $user->profile()->create([
            'focus_areas' => ['sleep'],
            'primary_goal' => 'Rest',
            'timezone' => 'UTC',
            'onboarding_completed_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('community-forms'))
            ->assertForbidden();
    }

    public function test_admin_can_view_community_forms(): void
    {
        $admin = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);
        $admin->profile()->create([
            'focus_areas' => ['sleep'],
            'primary_goal' => 'Rest',
            'timezone' => 'UTC',
            'onboarding_completed_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('community-forms'))
            ->assertOk();
    }

    public function test_care_marketplace_routes_are_off_by_default(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->profile()->create([
            'focus_areas' => ['sleep'],
            'primary_goal' => 'Rest',
            'timezone' => 'UTC',
            'onboarding_completed_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('all-doctors'))
            ->assertNotFound();
    }
}

<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_logout_cannot_be_triggered_by_a_get_request(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/logout')->assertMethodNotAllowed();

        $this->assertAuthenticatedAs($user);
    }

    public function test_app_navigation_logs_out_with_a_post_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('onboarding.show'))
            ->assertOk()
            ->assertSee('<form method="POST" action="'.route('logout').'">', false)
            ->assertDontSee('href="'.route('logout').'"', false);
    }
}

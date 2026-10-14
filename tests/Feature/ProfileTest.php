<?php

namespace Tests\Feature;

use App\Domains\Identity\Enums\HealthFocusArea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Profile routes sit behind the `onboarded` middleware, so the user needs a completed profile.
     */
    private function onboardedUser(): User
    {
        $user = User::factory()->create();

        $user->profile()->create([
            'focus_areas' => [HealthFocusArea::Sleep->value],
            'primary_goal' => 'Sleep consistently',
            'timezone' => 'UTC',
            'onboarding_completed_at' => now(),
        ]);

        return $user;
    }

    public function test_profile_page_is_displayed(): void
    {
        $user = $this->onboardedUser();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = $this->onboardedUser();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = $this->onboardedUser();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = $this->onboardedUser();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = $this->onboardedUser();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function test_profile_image_is_stored_under_a_server_generated_name(): void
    {
        Storage::fake('avatars');
        $user = $this->onboardedUser();

        $this->actingAs($user)
            ->from('/edit-profile')
            ->post('/profile', $this->profileData($user, [
                'img' => UploadedFile::fake()->createWithContent('my photo.png', self::pngBytes()),
            ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect('/edit-profile');

        $stored = $user->fresh()->img;

        $this->assertMatchesRegularExpression('/^[A-Za-z0-9]{40}\.png$/', $stored);
        Storage::disk('avatars')->assertExists($stored);
    }

    public function test_php_file_disguised_as_an_image_is_rejected(): void
    {
        Storage::fake('avatars');
        $user = $this->onboardedUser();

        // Valid GIF magic bytes followed by PHP: content sniffing alone reports image/gif.
        $polyglot = UploadedFile::fake()->createWithContent('shell.php', "GIF89a<?php echo 'pwned'; ?>");

        $this->actingAs($user)
            ->from('/edit-profile')
            ->post('/profile', $this->profileData($user, ['img' => $polyglot]))
            ->assertSessionHasErrors('img');

        $this->assertNull($user->fresh()->img);
        $this->assertFileDoesNotExist(public_path('assets/users/shell.php'));
        $this->assertSame([], Storage::disk('avatars')->allFiles());
    }

    public function test_svg_profile_image_is_rejected(): void
    {
        Storage::fake('avatars');
        $user = $this->onboardedUser();

        $svg = UploadedFile::fake()->createWithContent(
            'avatar.svg',
            '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(document.cookie)</script></svg>'
        );

        $this->actingAs($user)
            ->from('/edit-profile')
            ->post('/profile', $this->profileData($user, ['img' => $svg]))
            ->assertSessionHasErrors('img');

        $this->assertNull($user->fresh()->img);
        $this->assertSame([], Storage::disk('avatars')->allFiles());
    }

    public function test_profile_image_larger_than_2mb_is_rejected(): void
    {
        Storage::fake('avatars');
        $user = $this->onboardedUser();

        $large = UploadedFile::fake()->createWithContent(
            'large.png',
            self::pngBytes().str_repeat("\0", 2049 * 1024)
        );

        $this->actingAs($user)
            ->from('/edit-profile')
            ->post('/profile', $this->profileData($user, ['img' => $large]))
            ->assertSessionHasErrors('img');

        $this->assertNull($user->fresh()->img);
        $this->assertSame([], Storage::disk('avatars')->allFiles());
    }

    private function profileData(User $user, array $overrides = []): array
    {
        return array_merge(['name' => $user->name, 'email' => $user->email], $overrides);
    }

    /**
     * A valid 1x1 PNG, so tests do not depend on the GD extension.
     */
    private static function pngBytes(): string
    {
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
    }
}

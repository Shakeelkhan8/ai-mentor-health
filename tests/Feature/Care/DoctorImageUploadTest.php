<?php

namespace Tests\Feature\Care;

use App\Domains\Identity\Enums\HealthFocusArea;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DoctorImageUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Doctor routes are behind feature:care_marketplace, which is off by default.
        config(['mentor.features.care_marketplace' => true]);
        Storage::fake('doctor_photos');
    }

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

    private function doctorData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Dr. Test',
            'email' => 'doctor@example.com',
            'phone_no' => '0123456789',
            'designation' => 'GP',
            'country' => 'PK',
            'city' => 'Lahore',
            'address' => '1 Test Street',
            'price' => '50',
        ], $overrides);
    }

    public function test_doctor_photo_is_stored_under_a_server_generated_name(): void
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');

        $this->actingAs($this->onboardedUser())
            ->from('/doctors/all')
            ->post(route('store-doctor'), $this->doctorData([
                'img' => UploadedFile::fake()->createWithContent('portrait.png', $png),
            ]))
            ->assertSessionHas('success');

        $stored = Doctor::query()->sole()->img;

        $this->assertMatchesRegularExpression('/^[A-Za-z0-9]{40}\.png$/', $stored);
        Storage::disk('doctor_photos')->assertExists($stored);
    }

    public function test_php_file_disguised_as_a_doctor_photo_is_rejected(): void
    {
        $polyglot = UploadedFile::fake()->createWithContent('shell.php', "GIF89a<?php echo 'pwned'; ?>");

        $this->actingAs($this->onboardedUser())
            ->from('/doctors/all')
            ->post(route('store-doctor'), $this->doctorData(['img' => $polyglot]))
            ->assertSessionHas('error');

        $this->assertSame(0, Doctor::query()->count());
        $this->assertFileDoesNotExist(public_path('assets/doctors/shell.php'));
        $this->assertSame([], Storage::disk('doctor_photos')->allFiles());
    }
}

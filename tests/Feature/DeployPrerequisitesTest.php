<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeployPrerequisitesTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        unset($_SERVER['TRUSTED_PROXIES'], $_ENV['TRUSTED_PROXIES']);

        parent::tearDown();
    }

    private function loginPageBehindHttpsProxy(): \Illuminate\Testing\TestResponse
    {
        return $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
            ->withHeaders(['X-Forwarded-Proto' => 'https', 'X-Forwarded-Port' => '443'])
            ->get('/login');
    }

    /**
     * Evaluate config/trustedproxy.php with TRUSTED_PROXIES set (or not) the way the
     * host's environment would, without rebooting the app. A reboot re-reads .env,
     * whose loader may overwrite a value it set on the first boot.
     */
    private function trustedProxiesConfigWith(?string $value): mixed
    {
        // Clear every place the value can come from (.env loads into all three).
        unset($_SERVER['TRUSTED_PROXIES'], $_ENV['TRUSTED_PROXIES']);
        putenv('TRUSTED_PROXIES');

        if ($value !== null) {
            $_SERVER['TRUSTED_PROXIES'] = $value;
        }

        return (require config_path('trustedproxy.php'))['proxies'];
    }

    public function test_trusted_proxies_come_from_the_environment_and_default_to_none(): void
    {
        $this->assertSame('*', $this->trustedProxiesConfigWith('*'));
        $this->assertSame('10.0.0.1,10.0.0.2', $this->trustedProxiesConfigWith('10.0.0.1,10.0.0.2'));
        $this->assertNull($this->trustedProxiesConfigWith(''), 'An empty value (as in .env.example) trusts no proxy.');
        $this->assertNull($this->trustedProxiesConfigWith(null));
    }

    public function test_links_use_https_when_the_hosting_proxy_is_trusted(): void
    {
        config(['trustedproxy.proxies' => '*']);

        $this->loginPageBehindHttpsProxy()
            ->assertOk()
            ->assertSee('action="https://localhost/login"', false);
    }

    public function test_forwarded_headers_are_ignored_unless_proxies_are_configured(): void
    {
        config(['trustedproxy.proxies' => null]);

        $this->loginPageBehindHttpsProxy()
            ->assertOk()
            ->assertSee('action="http://localhost/login"', false);
    }

    public function test_health_check_reports_ok_without_starting_a_session(): void
    {
        $response = $this->get('/up');

        $response->assertOk()->assertExactJson(['status' => 'ok']);
        $this->assertFalse($response->headers->has('Set-Cookie'));
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeployPrerequisitesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Reboot the app with TRUSTED_PROXIES set, the way a host's environment would.
     */
    private function bootWithTrustedProxies(?string $value): void
    {
        foreach (['putenv', '_ENV', '_SERVER'] as $store) {
            match ($store) {
                'putenv' => $value === null ? putenv('TRUSTED_PROXIES') : putenv("TRUSTED_PROXIES={$value}"),
                '_ENV' => $value === null ? $this->unsetKey($_ENV) : $_ENV['TRUSTED_PROXIES'] = $value,
                '_SERVER' => $value === null ? $this->unsetKey($_SERVER) : $_SERVER['TRUSTED_PROXIES'] = $value,
            };
        }

        $this->refreshApplication();
        $this->setUpTraits();
    }

    private function unsetKey(array &$store): void
    {
        unset($store['TRUSTED_PROXIES']);
    }

    protected function tearDown(): void
    {
        putenv('TRUSTED_PROXIES');
        unset($_ENV['TRUSTED_PROXIES'], $_SERVER['TRUSTED_PROXIES']);

        parent::tearDown();
    }

    private function loginPageBehindHttpsProxy(): \Illuminate\Testing\TestResponse
    {
        return $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
            ->withHeaders(['X-Forwarded-Proto' => 'https', 'X-Forwarded-Port' => '443'])
            ->get('/login');
    }

    public function test_links_use_https_when_the_hosting_proxy_is_trusted(): void
    {
        $this->bootWithTrustedProxies('*');

        $this->loginPageBehindHttpsProxy()
            ->assertOk()
            ->assertSee('action="https://localhost/login"', false);
    }

    public function test_forwarded_headers_are_ignored_unless_proxies_are_configured(): void
    {
        $this->bootWithTrustedProxies(null);

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

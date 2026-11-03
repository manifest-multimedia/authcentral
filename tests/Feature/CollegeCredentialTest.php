<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SsoRedirect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CollegeCredentialTest extends TestCase
{
    use RefreshDatabase;

    private string $key;

    protected function setUp(): void
    {
        parent::setUp();
        $this->key = str_repeat('a', 64);
        config(['college.service_key' => $this->key]);
        Http::preventStrayRequests();
    }

    private function user(array $attributes = []): User
    {
        return User::create(array_merge(['name' => 'Local Test Staff', 'email' => 'fixture@example.test', 'password' => 'Strong-fixture-password', 'status' => 'active'], $attributes));
    }

    private function verify(array $data = [])
    {
        return $this->withHeader('X-College-Service-Key', $this->key)->postJson('https://authcentral.localhost/api/v1/college/verify-credentials', array_merge(['username' => 'fixture@example.test', 'password' => 'Strong-fixture-password'], $data));
    }

    public function test_new_and_updated_passwords_are_hashed_without_double_hashing(): void
    {
        $user = $this->user();
        $this->assertNotSame('Strong-fixture-password', $user->password);
        $this->assertTrue(Hash::check('Strong-fixture-password', $user->password));
        $hash = $user->password;
        $user->password = $hash;
        $user->save();
        $this->assertSame($hash, $user->fresh()->password);
        $user->update(['password' => 'Different-fixture-password']);
        $this->assertTrue(Hash::check('Different-fixture-password', $user->fresh()->password));
    }

    public function test_identity_verification_returns_no_password_roles_or_tokens(): void
    {
        $user = $this->user();
        $response = $this->verify()->assertOk()->assertJsonPath('authenticated', true)->assertJsonPath('user.email', $user->email);
        $this->assertSame(['authenticated', 'user'], array_keys($response->json()));
        $this->assertSame(['id', 'email', 'name'], array_keys($response->json('user')));
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_missing_wrong_and_unconfigured_service_keys_are_rejected(): void
    {
        $this->postJson('https://authcentral.localhost/api/v1/college/verify-credentials', [])->assertForbidden();
        $this->key = 'wrong';
        $this->verify()->assertForbidden();
        config(['college.service_key' => null]);
        $this->verify()->assertForbidden();
    }

    public function test_http_credentials_are_rejected(): void
    {
        $this->withHeader('X-College-Service-Key', $this->key)->postJson('http://authcentral.localhost/api/v1/college/verify-credentials', [])->assertStatus(400);
    }

    public function test_unknown_and_wrong_passwords_are_unauthorized(): void
    {
        $this->verify()->assertUnauthorized();
        $this->user();
        $this->verify(['password' => 'wrong'])->assertUnauthorized();
    }

    public function test_inactive_accounts_and_two_factor_accounts_fail_closed(): void
    {
        $user = $this->user(['status' => 'suspended']);
        $this->verify()->assertForbidden();
        $user->forceFill(['status' => 'active', 'two_factor_confirmed_at' => now()])->save();
        $this->verify()->assertForbidden();
    }

    public function test_failed_credentials_are_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->verify()->assertUnauthorized();
        }
        $this->verify()->assertStatus(429);
    }

    public function test_browser_password_check_does_not_authenticate_before_two_factor_challenge(): void
    {
        $user = $this->user();
        $user->forceFill(['two_factor_secret' => encrypt('JBSWY3DPEHPK3PXP'), 'two_factor_confirmed_at' => now()])->save();
        $this->post('/login', ['email' => $user->email, 'password' => 'Strong-fixture-password'])->assertRedirect('/two-factor-challenge');
        $this->assertGuest();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_sso_rejects_unregistered_callbacks_and_does_not_issue_tokens(): void
    {
        config(['sso.allowed_callbacks' => ['https://college.localhost/auth/callback']]);
        $this->get('/login?redirect_url='.urlencode('https://attacker.example/callback'))->assertStatus(422);
        $this->assertDatabaseCount('personal_access_tokens', 0);
        $user = $this->user();
        $response = app(SsoRedirect::class)->redirect($user, 'https://college.localhost/auth/callback');
        $this->assertStringStartsWith('https://college.localhost/auth/callback?token=', $response->getTargetUrl());
        $token = $user->tokens()->first();
        $this->assertSame(['identity:read'], $token->abilities);
        $this->assertTrue($token->expires_at->isFuture());
        $this->assertTrue($token->expires_at->lte(now()->addMinutes(5)));
    }
}

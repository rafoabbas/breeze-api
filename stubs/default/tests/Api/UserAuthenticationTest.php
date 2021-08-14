<?php

namespace Tests\Api\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Stephenjude\ApiTestHelper\WithApiHelper;
use Tests\TestCase;

class UserAuthenticationTest extends TestCase
{
    use RefreshDatabase;
    use WithApiHelper;

    /**
     * @test
     * @enlighten
     */
    public function test_register()
    {
        $this->response = $this->postJson('api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->response->assertCreated();

        $this->assertApiSuccess();
    }

    /**
     * @test
     * @enlighten
     */
    public function test_login()
    {
        $user = User::factory()->create();

        $this->response = $this->postJson('api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->response->assertOk();

        $this->assertApiSuccess();
    }

    /**
     * @test
     * @enlighten
     */
    public function test_resend_email_verification_link()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->postJson('api/resend-verify-link', [
            'email' => $user->email,
        ]);

        $response->assertOk();

        $response->assertJsonFragment(['message' => 'Email verification link sent']);
    }

    /**
     * @test
     * @enlighten
     */
    public function test_request_password_reset_link()
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->postJson('api/forgot-password', [
            'email' => $user->email,
        ]);

        Notification::assertSentTo($user, ResetPassword::class);

        $response->assertOk();

        $response->assertJsonFragment(['message' => 'Password reset link sent']);
    }
}

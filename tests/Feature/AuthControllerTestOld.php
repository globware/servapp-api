<?php
// tests/Feature/AuthControllerTest.php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\EmailVerificationToken;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

   #[Test]
    public function it_sends_email_verification_successfully()
    {
        $response = $this->postJson('/api/auth/send_email_verification_mail', [
            'email' => 'test@example.com'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'statusCode' => 200,
            'message' => 'Verification mail sent successfully'
        ]);

        Mail::assertSent(\App\Mail\EmailVerification::class);
    }

   #[Test]
    public function it_returns_error_when_email_already_registered()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson('/api/auth/send_email_verification_mail', [
            'email' => 'existing@example.com'
        ]);

        $response->assertStatus(402);
        $response->assertJson([
            'statusCode' => 402,
            'error' => 'This email is already registered, please login'
        ]);
    }

   #[Test]
    public function it_registers_user_successfully_with_verified_email()
    {
        // Create verified email token first
        EmailVerificationToken::create([
            'email' => 'newuser@example.com',
            'token_signature' => hash('sha256', 'test-token'),
            'expires_at' => now()->addHours(24),
            'verified' => true
        ]);

        $response = $this->postJson('/api/auth/register', [
            'type' => 'customer',
            'firstname' => 'John',
            'surname' => 'Doe',
            'email' => 'newuser@example.com',
            'phoneNumber' => '1234567890',
            'locationId' => 1,
            'password' => 'Password123!'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'statusCode',
            'data' => [
                'user' => [
                    'id',
                    'firstname',
                    'surname',
                    'email'
                ],
                'tokenData'
            ]
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'firstname' => 'John',
            'surname' => 'Doe'
        ]);
    }

   #[Test]
    public function it_returns_error_when_registering_with_unverified_email()
    {
        // Create unverified email token
        EmailVerificationToken::create([
            'email' => 'unverified@example.com',
            'token_signature' => hash('sha256', 'test-token'),
            'expires_at' => now()->addHours(24),
            'verified' => false
        ]);

        $response = $this->postJson('/api/auth/register', [
            'type' => 'customer',
            'firstname' => 'John',
            'surname' => 'Doe',
            'email' => 'unverified@example.com',
            'phoneNumber' => '1234567890',
            'locationId' => 1,
            'password' => 'Password123!'
        ]);

        $response->assertStatus(402);
        $response->assertJson([
            'statusCode' => 402,
            'error' => 'Email has not been verified'
        ]);
    }

   #[Test]
    public function it_logs_in_user_successfully()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('Password123!')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'Password123!'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'statusCode',
            'data' => [
                'user' => [
                    'id',
                    'email'
                ],
                'tokenData'
            ]
        ]);
    }

   #[Test]
    public function it_returns_unauthorized_for_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('Password123!')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'WrongPassword!'
        ]);

        $response->assertStatus(401);
    }
}
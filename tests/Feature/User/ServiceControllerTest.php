<?php
// tests/Feature/User/ServiceControllerTest.php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use App\Models\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPUnit\Framework\Attributes\Test;

class ServiceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
    }

    #[Test]
    public function test_authentication_required()
    {
        $response = $this->postJson('/api/user/services/get_by_location');
        $response->assertStatus(401);
        $response->assertExactJson([
            'message' => 'unauthorized'
        ]);
    }

    #[Test]
    public function test_returns_services_with_authentication()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/user/services/get_by_location');

        $response->assertStatus(200);
        $response->assertExactJson([
            'statusCode' => 200,
            'data' => []
        ]);
    }

    #[Test]
    public function test_returns_services_when_data_exists()
    {
        // Create a service
        $service = Service::factory()->create(['name' => 'Plumbing']);
        
        // Create a verified user service
        UserService::factory()->create([
            'service_id' => $service->id,
            'verified' => true,
            'name' => 'Test Plumbing Service'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/user/services/get_by_location');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'statusCode',
            'data' => [
                '*' => [
                    'id',
                    'name'
                    // userServices and users might not be in response
                ]
            ]
        ]);
        
        // Should have at least one service
        $data = $response->json('data');
        $this->assertGreaterThan(0, count($data));
    }

    #[Test]
    public function test_with_gps_coordinates_parameter()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/user/services/get_by_location', [
            'long' => '3.3792',
            'lat' => '6.5244'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'statusCode',
            'data'
        ]);
    }
}
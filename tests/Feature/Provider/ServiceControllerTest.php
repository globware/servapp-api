<?php
// tests/Feature/Provider/ServiceControllerTest.php

namespace Tests\Feature\Provider;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserService;
use App\Models\Service;
use App\Models\Location;
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
    public function test_requires_authentication()
    {
        $response = $this->getJson('/api/provider/services');
        $response->assertStatus(401);
        $response->assertExactJson([
            'message' => 'unauthorized'
        ]);
    }

    #[Test]
    public function test_returns_user_services_list()
    {
        $service = Service::factory()->create(['name' => 'Plumbing']);
        
        UserService::factory()->create([
            'user_id' => $this->user->id,
            'service_id' => $service->id,
            'name' => 'Test Plumbing Service',
            'verified' => true
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/provider/services');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'statusCode',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'phoneNumbers',
                    'rating',
                    'verified',
                    'suspended',
                    'longitude',
                    'latitude'
                    // Other fields might be null or not included
                ]
            ]
        ]);
    }

    #[Test]
    public function test_returns_empty_list_when_no_services()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/provider/services');

        $response->assertStatus(200);
        $response->assertExactJson([
            'statusCode' => 200,
            'data' => []
        ]);
    }

    #[Test]
    public function test_creates_service_successfully()
    {
        $service = Service::factory()->create(['name' => 'Plumbing']);
        $location = Location::factory()->create(['name' => 'Test Location']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/provider/services/add', [
            'name' => 'My Plumbing Service',
            'serviceId' => $service->id,
            'address' => '123 Main St',
            'locationId' => $location->id,
            'allDay' => true,
            'description' => 'Professional plumbing services'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'statusCode',
            'data' => [
                'id',
                'name'
            ]
        ]);
        
        // Verify it was saved
        $this->assertDatabaseHas('user_services', [
            'name' => 'My Plumbing Service',
            'user_id' => $this->user->id
        ]);
    }
}
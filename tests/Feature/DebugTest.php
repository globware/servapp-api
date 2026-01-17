<?php
// tests/Feature/DebugTest.php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class DebugTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    public function test_debug_user_service_endpoint()
    {
        // Create user and token
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        
        echo "User created with ID: " . $user->id . "\n";
        echo "Token: " . substr($token, 0, 50) . "...\n\n";
        
        // Test without token
        echo "1. Testing WITHOUT token:\n";
        $response1 = $this->postJson('/api/user/services/get_by_location');
        echo "   Status: " . $response1->getStatusCode() . "\n";
        echo "   Response: " . $response1->getContent() . "\n\n";
        
        // Test with token
        echo "2. Testing WITH token:\n";
        $response2 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/services/get_by_location');
        
        echo "   Status: " . $response2->getStatusCode() . "\n";
        echo "   Response: " . substr($response2->getContent(), 0, 500) . "\n";
        
        // Always pass so we can see output
        $this->assertTrue(true);
    }
}
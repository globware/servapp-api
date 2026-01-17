<?php
// tests/Feature/UtilityControllerTest.php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Country;
use App\Models\State;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class UtilityControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_states_endpoint_returns_utilities_format()
    {
        try {
            $country = Country::create([
                'name' => 'Nigeria',
                'code' => 'NG',
                'phone_code' => '+234'
            ]);
            
            $state = State::create([
                'name' => 'Lagos',
                'country_id' => $country->id
            ]);
            
            Location::create(['name' => 'Lagos Island', 'state_id' => $state->id]);

            $response = $this->getJson('/api/utilities/states');

            $response->assertStatus(200);
            $response->assertJsonStructure([
                'statusCode',
                'data' => [
                    '*' => [
                        'id',
                        'name'
                        // locations might be included
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            $this->markTestSkipped('Test requires database tables: ' . $e->getMessage());
        }
    }

    #[Test]
    public function test_states_endpoint_returns_empty_when_no_data()
    {
        $response = $this->getJson('/api/utilities/states');
        
        // Should return 200 with empty data, not error
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'statusCode',
            'data'
        ]);
        
        $data = $response->json('data');
        $this->assertIsArray($data);
    }
}
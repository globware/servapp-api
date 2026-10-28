<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use App\Models\Product;
use App\Models\Location;
use App\Models\State;
use App\Models\Country;
use App\Models\ProviderLocation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPUnit\Framework\Attributes\Test;

class ProviderLocationTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
    }

    #[Test]
    public function test_provider_can_create_and_list_locations()
    {
        $country = Country::first() ?? Country::forceCreate(['name' => 'Nigeria', 'code' => 'NG', 'phone_code' => '+234']);
        $state = State::first() ?? State::forceCreate(['name' => 'Lagos', 'country_id' => $country->id]);
        $adminLoc = Location::first() ?? Location::forceCreate([
            'state_id' => $state->id,
            'name' => 'Ikeja Ward',
            'latitude' => '6.6018',
            'longitude' => '3.3515'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/v1/provider/locations', [
            'title' => 'Main Workshop',
            'address' => '12 Allen Avenue, Ikeja, Lagos',
            'lat' => 6.6018,
            'long' => 3.3515,
            'stateId' => $state->id,
            'countryId' => $country->id,
            'locationId' => $adminLoc->id,
            'isDefault' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.title', 'Main Workshop');
        $response->assertJsonPath('data.address', '12 Allen Avenue, Ikeja, Lagos');
        $response->assertJsonPath('data.isDefault', true);

        $this->assertDatabaseHas('provider_locations', [
            'user_id' => $this->user->id,
            'title' => 'Main Workshop',
            'address' => '12 Allen Avenue, Ikeja, Lagos',
            'is_default' => true,
        ]);

        // List
        $listResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/v1/provider/locations');

        $listResponse->assertStatus(200);
        $listResponse->assertJsonCount(1, 'data');
    }

    #[Test]
    public function test_provider_can_update_and_delete_location()
    {
        $location = ProviderLocation::create([
            'user_id' => $this->user->id,
            'title' => 'Old Title',
            'address' => 'Old Address',
            'is_default' => true,
        ]);

        $updateResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->patchJson("/v1/provider/locations/{$location->id}", [
            'title' => 'Updated Title',
            'address' => '45 Marina Street, Lagos',
        ]);

        $updateResponse->assertStatus(200);
        $updateResponse->assertJsonPath('data.title', 'Updated Title');
        $updateResponse->assertJsonPath('data.address', '45 Marina Street, Lagos');

        // Delete
        $deleteResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/v1/provider/locations/{$location->id}");

        $deleteResponse->assertStatus(200);
        $this->assertDatabaseMissing('provider_locations', ['id' => $location->id]);
    }

    #[Test]
    public function test_provider_can_create_product_with_saved_location()
    {
        $productCategory = Product::first() ?? Product::create(['name' => 'Laptops']);
        $savedLoc = ProviderLocation::create([
            'user_id' => $this->user->id,
            'title' => 'Tech Hub',
            'address' => '10 Computer Village, Ikeja',
            'latitude' => 6.5950,
            'longitude' => 3.3390,
            'is_default' => true,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/v1/provider/products', [
            'name' => 'MacBook Pro M3 ' . uniqid(),
            'description' => 'Brand new sealed',
            'price' => 2500000,
            'productId' => $productCategory->id,
            'providerLocationId' => $savedLoc->id,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.address', '10 Computer Village, Ikeja');
        $response->assertJsonPath('data.providerLocationId', $savedLoc->id);
        $response->assertJsonPath('data.providerLocation.title', 'Tech Hub');

        $this->assertDatabaseHas('user_products', [
            'user_id' => $this->user->id,
            'provider_location_id' => $savedLoc->id,
            'address' => '10 Computer Village, Ikeja',
        ]);
    }

    #[Test]
    public function test_provider_can_create_service_with_saved_location()
    {
        $country = Country::first() ?? Country::forceCreate(['name' => 'Nigeria', 'code' => 'NG', 'phone_code' => '+234']);
        $state = State::first() ?? State::forceCreate(['name' => 'Lagos', 'country_id' => $country->id]);
        $adminLoc = Location::first() ?? Location::forceCreate([
            'state_id' => $state->id,
            'name' => 'Ikeja Ward',
        ]);
        $service = Service::factory()->create(['name' => 'Carpentry ' . uniqid()]);

        $savedLoc = ProviderLocation::create([
            'user_id' => $this->user->id,
            'title' => 'Carpentry Workshop',
            'address' => '88 Wood Avenue, Lagos',
            'latitude' => 6.5123,
            'longitude' => 3.3456,
            'state_id' => $state->id,
            'country_id' => $country->id,
            'location_id' => $adminLoc->id,
            'is_default' => true,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/v1/provider/services/add', [
            'name' => 'Custom Furniture ' . uniqid(),
            'serviceId' => $service->id,
            'providerLocationId' => $savedLoc->id,
            'allDay' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.address', '88 Wood Avenue, Lagos');
        $response->assertJsonPath('data.providerLocationId', $savedLoc->id);
        $response->assertJsonPath('data.providerLocation.title', 'Carpentry Workshop');

        $this->assertDatabaseHas('user_services', [
            'user_id' => $this->user->id,
            'provider_location_id' => $savedLoc->id,
            'address' => '88 Wood Avenue, Lagos',
        ]);
    }

    #[Test]
    public function test_provider_can_create_service_and_save_location_on_the_fly()
    {
        $country = Country::first() ?? Country::forceCreate(['name' => 'Nigeria', 'code' => 'NG', 'phone_code' => '+234']);
        $state = State::first() ?? State::forceCreate(['name' => 'Lagos', 'country_id' => $country->id]);
        $adminLoc = Location::first() ?? Location::forceCreate([
            'state_id' => $state->id,
            'name' => 'Surulere Ward',
        ]);
        $service = Service::factory()->create(['name' => 'Painting ' . uniqid()]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/v1/provider/services/add', [
            'name' => 'Pro Painter ' . uniqid(),
            'serviceId' => $service->id,
            'address' => '15 Bode Thomas, Surulere',
            'locationId' => $adminLoc->id,
            'lat' => 6.4975,
            'long' => 3.3551,
            'saveLocation' => true,
            'locationTitle' => 'Surulere Studio',
            'allDay' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.address', '15 Bode Thomas, Surulere');
        $response->assertJsonPath('data.providerLocation.title', 'Surulere Studio');

        $this->assertDatabaseHas('provider_locations', [
            'user_id' => $this->user->id,
            'title' => 'Surulere Studio',
            'address' => '15 Bode Thomas, Surulere',
        ]);
    }

    #[Test]
    public function test_provider_can_create_product_and_save_location_on_the_fly()
    {
        $productCategory = Product::first() ?? Product::create(['name' => 'Electronics']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/v1/provider/products', [
            'name' => 'Sony Headphones ' . uniqid(),
            'description' => 'Noise cancelling',
            'price' => 120000,
            'productId' => $productCategory->id,
            'address' => '22 Awolowo Road, Ikoyi',
            'latitude' => 6.4474,
            'longitude' => 3.4287,
            'saveLocation' => true,
            'locationTitle' => 'Ikoyi Depot',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.address', '22 Awolowo Road, Ikoyi');
        $response->assertJsonPath('data.providerLocation.title', 'Ikoyi Depot');

        $this->assertDatabaseHas('provider_locations', [
            'user_id' => $this->user->id,
            'title' => 'Ikoyi Depot',
            'address' => '22 Awolowo Road, Ikoyi',
        ]);
    }
}

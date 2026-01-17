<?php
// tests/Unit/Models/ServiceTest.php

namespace Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Service;
use App\Models\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_be_created()
    {
        $service = Service::create(['name' => 'Plumbing']);
        
        $this->assertDatabaseHas('services', [
            'name' => 'Plumbing',
            'approved' => true, // Default value
        ]);
    }

    #[Test]
    public function it_has_user_services_relationship()
    {
        $service = Service::factory()->create();
        $userService = UserService::factory()->create(['service_id' => $service->id]);
        
        $this->assertCount(1, $service->userServices);
        $this->assertEquals($userService->id, $service->userServices->first()->id);
    }

    #[Test]
    public function it_can_have_users_through_user_services()
    {
        $service = Service::factory()->create();
        
        $this->assertInstanceOf(
            'Illuminate\Database\Eloquent\Relations\BelongsToMany',
            $service->users()
        );
    }
}
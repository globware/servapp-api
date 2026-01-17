<?php
// tests/Unit/Models/UserTest.php

namespace Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;
use App\Models\File;
use App\Models\Service;
use App\Models\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_be_created_with_required_attributes()
    {
        $user = User::factory()->create([
            'firstname' => 'John',
            'surname' => 'Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'firstname' => 'John',
            'surname' => 'Doe',
        ]);
    }

    #[Test]
    public function it_has_photo_relationship()
    {
        $user = User::factory()->create();
        $file = File::factory()->create();
        
        $user->photo()->associate($file);
        $user->save();

        $this->assertInstanceOf(File::class, $user->photo);
        $this->assertEquals($file->id, $user->photo_id);
    }

    #[Test]
    public function it_has_services_relationship()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create();
        
        $user->services()->attach($service, [
            'name' => "test Service",
            'phone_numbers' => json_encode(['1234567890']),
            'email' => 'service@example.com'
        ]);

        $this->assertCount(1, $user->services);
        $this->assertEquals($service->name, $user->services->first()->name);
    }

    #[Test]
    public function it_returns_jwt_identifier()
    {
        $user = User::factory()->create();
        
        $this->assertEquals($user->getKey(), $user->getJWTIdentifier());
    }

    #[Test]
    public function it_returns_empty_jwt_custom_claims()
    {
        $user = User::factory()->create();
        
        $this->assertEmpty($user->getJWTCustomClaims());
    }

    #[Test]
    public function it_hides_sensitive_attributes()
    {
        $user = User::factory()->make();
        $hidden = $user->getHidden();
        
        $this->assertContains('password', $hidden);
        $this->assertContains('remember_token', $hidden);
    }

    #[Test]
    public function it_casts_attributes_correctly()
    {
        $user = new User();
        $casts = $user->getCasts();
        
        $this->assertEquals('datetime', $casts['email_verified_at']);
        $this->assertEquals('hashed', $casts['password']);
    }
}
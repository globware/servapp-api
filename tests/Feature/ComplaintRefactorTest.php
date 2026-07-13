<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use App\Models\UserService;
use App\Models\UserServiceRequest;
use App\Models\Complaint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPUnit\Framework\Attributes\Test;

class ComplaintRefactorTest extends TestCase
{
    use RefreshDatabase;

    private function createServiceAndRequest($client, $provider)
    {
        $service = Service::factory()->create();
        $userService = UserService::factory()->create([
            'user_id' => $provider->id,
            'service_id' => $service->id,
            'verified' => true
        ]);

        $request = new UserServiceRequest();
        $request->user_service_id = $userService->id;
        $request->user_id = $client->id;
        $request->message = "Please do my work";
        $request->status = "pending";
        $request->Status = "pending";
        $request->save();

        return [$userService, $request];
    }

    #[Test]
    public function test_client_can_complain_using_request_id()
    {
        $client = User::factory()->create();
        $provider = User::factory()->create();
        [$userService, $request] = $this->createServiceAndRequest($client, $provider);

        $token = JWTAuth::fromUser($client);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/user_services/complain', [
            'requestId' => $request->id,
            'title' => 'Client complaining title',
            'content' => 'Client complaining content details'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('complaints', [
            'user_id' => $client->id,
            'target_id' => $request->id,
            'target_type' => 'App\Models\UserServiceRequest',
            'reference_id' => $userService->id,
            'reference_type' => 'App\Models\UserService',
            'title' => 'Client complaining title',
            'content' => 'Client complaining content details'
        ]);
    }

    #[Test]
    public function test_provider_can_complain_using_request_id()
    {
        $client = User::factory()->create();
        $provider = User::factory()->create();
        [$userService, $request] = $this->createServiceAndRequest($client, $provider);

        $token = JWTAuth::fromUser($provider);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/provider/complaints/save', [
            'requestId' => $request->id,
            'title' => 'Provider complaining title',
            'content' => 'Provider complaining content details'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('complaints', [
            'user_id' => $provider->id,
            'target_id' => $request->id,
            'target_type' => 'App\Models\UserServiceRequest',
            'reference_id' => $userService->id,
            'reference_type' => 'App\Models\UserService',
            'title' => 'Provider complaining title',
            'content' => 'Provider complaining content details'
        ]);
    }

    #[Test]
    public function test_client_cannot_complain_on_unrelated_request()
    {
        $clientA = User::factory()->create();
        $clientB = User::factory()->create();
        $provider = User::factory()->create();
        [$userService, $request] = $this->createServiceAndRequest($clientA, $provider);

        $token = JWTAuth::fromUser($clientB);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/user_services/complain', [
            'requestId' => $request->id,
            'title' => 'Client B complaining',
            'content' => 'Client B content'
        ]);

        $response->assertStatus(402);
    }

    #[Test]
    public function test_provider_cannot_complain_on_unrelated_request()
    {
        $client = User::factory()->create();
        $providerA = User::factory()->create();
        $providerB = User::factory()->create();
        [$userService, $request] = $this->createServiceAndRequest($client, $providerA);

        $token = JWTAuth::fromUser($providerB);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/provider/complaints/save', [
            'requestId' => $request->id,
            'title' => 'Provider B complaining',
            'content' => 'Provider B content'
        ]);

        $response->assertStatus(402);
    }

    #[Test]
    public function test_complaints_displayed_via_service_complaints_relation()
    {
        $client = User::factory()->create();
        $provider = User::factory()->create();
        [$userService, $request] = $this->createServiceAndRequest($client, $provider);

        // Save client complaint
        $clientComplaint = new Complaint();
        $clientComplaint->user_id = $client->id;
        $clientComplaint->target_id = $request->id;
        $clientComplaint->target_type = 'App\Models\UserServiceRequest';
        $clientComplaint->reference_id = $userService->id;
        $clientComplaint->reference_type = 'App\Models\UserService';
        $clientComplaint->title = 'Bad Service';
        $clientComplaint->content = 'Terrible service quality';
        $clientComplaint->save();

        // Save provider complaint
        $providerComplaint = new Complaint();
        $providerComplaint->user_id = $provider->id;
        $providerComplaint->target_id = $request->id;
        $providerComplaint->target_type = 'App\Models\UserServiceRequest';
        $providerComplaint->reference_id = $userService->id;
        $providerComplaint->reference_type = 'App\Models\UserService';
        $providerComplaint->title = 'Rude Customer';
        $providerComplaint->content = 'Customer was extremely rude';
        $providerComplaint->save();

        $complaints = $userService->complaints;
        $this->assertEquals(2, $complaints->count());

        $accused1 = $clientComplaint->accused();
        $this->assertNotNull($accused1);
        $this->assertEquals($provider->id, $accused1->id);

        $accused2 = $providerComplaint->accused();
        $this->assertNotNull($accused2);
        $this->assertEquals($client->id, $accused2->id);
    }

    #[Test]
    public function test_client_cannot_raise_duplicate_complaint()
    {
        $client = User::factory()->create();
        $provider = User::factory()->create();
        [$userService, $request] = $this->createServiceAndRequest($client, $provider);

        $token = JWTAuth::fromUser($client);

        // Raise first complaint
        $response1 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/user_services/complain', [
            'requestId' => $request->id,
            'title' => 'Complaint 1',
            'content' => 'Content 1'
        ]);
        $response1->assertStatus(200);

        // Try duplicate complaint
        $response2 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/user_services/complain', [
            'requestId' => $request->id,
            'title' => 'Complaint 2',
            'content' => 'Content 2'
        ]);
        $response2->assertStatus(402);
        $this->assertEquals("You have already raised a complaint for this request", $response2->json('message'));
    }

    #[Test]
    public function test_provider_cannot_raise_duplicate_complaint()
    {
        $client = User::factory()->create();
        $provider = User::factory()->create();
        [$userService, $request] = $this->createServiceAndRequest($client, $provider);

        $token = JWTAuth::fromUser($provider);

        // Raise first complaint
        $response1 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/provider/complaints/save', [
            'requestId' => $request->id,
            'title' => 'Complaint 1',
            'content' => 'Content 1'
        ]);
        $response1->assertStatus(200);

        // Try duplicate complaint
        $response2 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/provider/complaints/save', [
            'requestId' => $request->id,
            'title' => 'Complaint 2',
            'content' => 'Content 2'
        ]);
        $response2->assertStatus(402);
        $this->assertEquals("You have already raised a complaint for this request", $response2->json('message'));
    }

    #[Test]
    public function test_has_complained_flag_returned_in_resource()
    {
        $client = User::factory()->create();
        $provider = User::factory()->create();
        [$userService, $request] = $this->createServiceAndRequest($client, $provider);

        // 1. Authenticated user has not complained
        $this->actingAs($client);
        $resource = (new \App\Http\Resources\ServiceRequestResource($request))->toArray(request());
        $this->assertFalse($resource['hasComplained']);

        // 2. Authenticated user has complained
        $complaint = new Complaint();
        $complaint->user_id = $client->id;
        $complaint->target_id = $request->id;
        $complaint->target_type = 'App\Models\UserServiceRequest';
        $complaint->title = 'Title';
        $complaint->content = 'Content';
        $complaint->save();

        $resourceAfter = (new \App\Http\Resources\ServiceRequestResource($request))->toArray(request());
        $this->assertTrue($resourceAfter['hasComplained']);
    }
}

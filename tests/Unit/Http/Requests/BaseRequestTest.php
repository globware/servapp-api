<?php
// tests/Unit/Http/Requests/BaseRequestTest.php

namespace Tests\Unit\Http\Requests;

use Tests\TestCase;
use App\Http\Requests\BaseRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use PHPUnit\Framework\Attributes\Test;

class BaseRequestTest extends TestCase
{
    #[Test]
    public function it_throws_http_response_exception_on_validation_failure()
    {
        // Create a test request instance using a concrete class
        $testRequest = new ConcreteTestRequest();
        
        $validator = Validator::make(['email' => 'not-an-email'], [
            'email' => 'required|email'
        ]);
        
        $this->expectException(HttpResponseException::class);
        
        // Use reflection to call the protected method
        $reflectionClass = new \ReflectionClass($testRequest);
        $method = $reflectionClass->getMethod('failedValidation');
        $method->setAccessible(true);
        $method->invoke($testRequest, $validator);
    }

    #[Test]
    public function it_returns_correct_error_format_on_validation_failure()
    {
        $testRequest = new ConcreteTestRequest();
        
        try {
            $validator = Validator::make(['email' => 'invalid'], [
                'email' => 'required|email',
                'name' => 'required|string|min:3'
            ]);
            
            // Use reflection to call the protected method
            $reflectionClass = new \ReflectionClass($testRequest);
            $method = $reflectionClass->getMethod('failedValidation');
            $method->setAccessible(true);
            $method->invoke($testRequest, $validator);
            
            $this->fail('Expected HttpResponseException was not thrown');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
            $data = json_decode($response->getContent(), true);
            
            $this->assertEquals(422, $response->getStatusCode());
            $this->assertEquals(422, $data['statusCode']);
            $this->assertArrayHasKey('error', $data);
            $this->assertArrayHasKey('errors', $data);
            $this->assertIsArray($data['errors']);
            $this->assertIsString($data['error']);
        }
    }
}

// Create a concrete test class outside the test method
class ConcreteTestRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'name' => 'required|string|min:3'
        ];
    }
}
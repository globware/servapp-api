<?php
// tests/Unit/Http/Requests/BaseRequestTest.php

namespace Tests\Unit\Http\Requests;

use Tests\TestCase;
use App\Http\Requests\BaseRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseRequestTest extends TestCase
{
    /** @test */
    public function it_throws_http_response_exception_on_validation_failure()
    {
        $request = new class extends BaseRequest {
            public function rules(): array
            {
                return ['email' => 'required|email'];
            }

            public function triggerFailedValidation($validator): void
            {
                $this->failedValidation($validator);
            }
        };

        $validator = Validator::make(['email' => 'not-an-email'], $request->rules());

        $this->expectException(HttpResponseException::class);

        $request->triggerFailedValidation($validator);
    }

    /** @test */
    public function it_returns_correct_error_format_on_validation_failure()
    {
        $request = new class extends BaseRequest {
            public function rules() {
                return [
                    'email' => 'required|email',
                    'name' => 'required|string|min:3'
                ];
            }
        };

        try {
            $validator = Validator::make(['email' => 'invalid'], $request->rules());
            $request->failedValidation($validator);
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
            // $data = $response->getData(true);
            $data = json_decode($response->getContent(), true);
            
            $this->assertEquals(422, $response->getStatusCode());
            $this->assertEquals(422, $data['statusCode']);
            $this->assertArrayHasKey('error', $data);
            $this->assertArrayHasKey('errors', $data);
            $this->assertIsArray($data['errors']);
        }
    }
}

// Create a concrete test class that extends BaseRequest
class TestBaseRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'name' => 'required|string|min:3'
        ];
    }
}
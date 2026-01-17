<?php
// tests/Unit/Exceptions/AppExceptionTest.php

namespace Tests\Unit\Exceptions;

use Tests\TestCase;
use App\Exceptions\AppException;
use Illuminate\Http\JsonResponse;
use PHPUnit\Framework\Attributes\Test;

class AppExceptionTest extends TestCase
{
    #[Test]
    
    public function it_creates_exception_with_custom_status_code()
    {
        $exception = new AppException(404, 'Not found', null, 'CUSTOM_ERROR');
        
        $this->assertEquals(404, $exception->getStatusCode());
        $this->assertEquals('CUSTOM_ERROR', $exception->getErrorCode());
        $this->assertEquals('Not found', $exception->getMessage());
    }

    #[Test]
    public function it_uses_default_error_code_based_on_status()
    {
        $exception = new AppException(401, 'Unauthorized');
        
        $this->assertEquals('UNAUTHORIZED', $exception->getErrorCode());
    }

    #[Test]
    public function it_returns_correct_default_error_codes()
    {
        $this->assertEquals('BAD_REQUEST', AppException::getDefaultErrorCode(400));
        $this->assertEquals('UNAUTHORIZED', AppException::getDefaultErrorCode(401));
        $this->assertEquals('PAYMENT_REQUIRED', AppException::getDefaultErrorCode(402));
        $this->assertEquals('FORBIDDEN', AppException::getDefaultErrorCode(403));
        $this->assertEquals('NOT_FOUND', AppException::getDefaultErrorCode(404));
        $this->assertEquals('VALIDATION_ERROR', AppException::getDefaultErrorCode(422));
        $this->assertEquals('INTERNAL_ERROR', AppException::getDefaultErrorCode(500));
        $this->assertEquals('UNKNOWN_ERROR', AppException::getDefaultErrorCode(999));
    }

    #[Test]
    public function it_renders_json_response()
    {
        $exception = new AppException(404, 'Resource not found');
        $response = $exception->render();
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        // The Utilities::error() always returns 500 status
        $this->assertEquals(500, $response->getStatusCode());
    }

    #[Test]
    public function it_stores_exception_when_provided()
    {
        $previousException = new \Exception('Previous error');
        $appException = new AppException(500, 'Something went wrong', $previousException);
        
        $this->assertSame($previousException, $appException->getException());
    }
}
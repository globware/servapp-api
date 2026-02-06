<?php
// app/Utilities.php
namespace App;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Exceptions\PaymentRequiredException;

use App\Exceptions\AppException;

use App\Models\User;

use App\Helpers;

class Utilities
{
    /**
     * Handle general application errors
     */
    public static function error(\Throwable $e, string $message = ''): JsonResponse
    {
        // Log the error
        Log::stack(['project'])->info($e->getMessage().' in '.$e->getFile().' at Line '.$e->getLine());
        Log::stack(['project'])->info($e);
        
        // For AppException with 402 status, use the specific method
        if ($e instanceof AppException && $e->getStatusCode() == 402) {
            return self::error402($e->getMessage());
        }

        // For AppException with 402 status, use the specific method
        if ($e instanceof AppException && $e->getStatusCode() == 401) {
            return self::error401($e->getMessage());
        }
        
        $finalMessage = ($message != '') ? $message : 'An error occurred while trying to perform this operation, Please try again later or contact support';
        
        return response()->json([
            'statusCode' => 500,
            'message' => $finalMessage,
            'error' => $finalMessage
        ], 500);
    }

    /**
     * Handle 402 Payment Required errors
     */
    public static function error402(string $message): JsonResponse
    {
        return response()->json([
            'statusCode' => 402,
            'message' => $message,
            'error' => $message
        ], 402);
    }

    /**
     * Handle 401 Payment Required errors
     */
    public static function error401(string $message): JsonResponse
    {
        return response()->json([
            'statusCode' => 401,
            'message' => $message,
            'error' => $message
        ], 401);
    }

    /**
     * Handle 400
     */
    public static function error400(string $message): JsonResponse
    {
        return response()->json([
            'statusCode' => 400,
            'message' => $message,
            'error' => $message
        ], 400);
    }

    public static function logStuff($message)
    {
        Log::stack(['project'])->info($message);
    }

    public static function ok($data)
    {
        return response()->json([
            'statusCode' => 200,
            'data' => $data
        ], 200);
    }

    public static function okay($message='', $data=null, )
    {
        $responseData = ['statusCode' => 200];
        if(!empty($data) || $data != '') $responseData['data'] = $data;
        return response()->json([
            'statusCode' => 200,
            'data' => $data,
            'message' => $message
        ], 200);
    }

    /**
     * Helper method to create AppException with specific status code
     */
    public static function createException(
        string $message, 
        int $statusCode = 500, 
        string $errorCode = null,
        ?array $details = null
    ): AppException {
        return new AppException($message, $statusCode, $errorCode);
    }

    public static function generateReferalCode()
    {
        do{
            $exists = true;
            $code = Helpers::randomAlphaNumeric(7);
            $user = User::where("referral_code", $code)->first();
            $exists = ($user) ? true : false;
        }while($exists);
        return $code;
    }
}
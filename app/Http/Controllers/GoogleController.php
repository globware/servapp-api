<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

use App\Exceptions\AppException;

use App\Http\Requests\GoogleAuth;

use App\Services\AuthService;

use App\Utilities;

class GoogleController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Handle Google login/signup from mobile app.
     * The mobile app sends an id_token obtained from Google SDK.
     */
    public function loginOrRegister(GoogleAuth $request)
    {
        try{
            $data = $request->validated();

            // ✅ Verify the token with Google
            $response = $this->authService->googleAuth($data);

            return Utilities::ok([
                "user" => new UserResource($response['user']),
                "tokenData" => $response['tokenData']
            ]);
        } catch(AppException $e){
            throw $e;
        }
    }

    // // Step 1: Redirect to Google
    // public function redirectToGoogle(Request $request)
    // {
    //     // optional: pass state for login/register context
    //     $state = $request->query('type', 'login');
    //     return Socialite::driver('google')
    //         ->with(['state' => $state])
    //         ->redirect();
    // }

    // // Step 2: Handle callback
    // public function handleGoogleCallback(Request $request)
    // {
    //     try {
    //         $googleUser = Socialite::driver('google')->stateless()->user();
    //         $state = $request->query('state', 'login');

    //         // Try to find user by email
    //         $user = User::where('email', $googleUser->getEmail())->first();

    //         if ($state === 'register' && !$user) {
    //             // Register new user
    //             $user = User::create([
    //                 'name' => $googleUser->getName(),
    //                 'email' => $googleUser->getEmail(),
    //                 'google_id' => $googleUser->getId(),
    //                 'avatar' => $googleUser->getAvatar(),
    //             ]);
    //         } elseif (!$user) {
    //             return response()->json(['message' => 'No account found, please register first.'], 404);
    //         }

    //         // Log in user
    //         Auth::login($user);

    //         return response()->json([
    //             'token' => $user->createToken('google')->plainTextToken,
    //             'user' => $user,
    //         ]);
    //     } catch (\Throwable $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }


}

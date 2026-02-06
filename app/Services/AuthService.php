<?php 

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use Jenssegers\Agent\Agent;

use App\Exceptions\AppException;

use App\Enums\Role;

use App\Helpers;

use App\Services\UserService;

class AuthService
{
    public $tenant = false;
    private $refreshToken;
    private $device;

    public function login($credentials)
    {
        $userService = new UserService;
        // try {
            if (!$token = JWTAuth::attempt($credentials)) {
                // return response()->json([
                //     'error' => 'Invalid credentials'
                // ], 401);
                throw new AppException(401, 'Invalid Credentials');
            }
            
            $user = Auth::user();

            if ($user->suspended) {
                JWTAuth::invalidate($token);
                throw new AppException(402, 'User account suspended');
            }

            $tokenData = $this->respondWithToken($token);

            $userService->updateRefreshTokenData($user, $this->refreshToken, $this->device);

            return ["user" => $user, "tokenData" => $tokenData];
    }

    public function googleAuth($data)
    {
        $userService = new UserService;

        $response = Http::get("https://oauth2.googleapis.com/tokeninfo?id_token={$data['idToken']}");

        if ($response->failed()) throw new AppException(401, 'Invalid Google token');

        $googleUser = $response->json();

        // Extract user info
        $email = $googleUser['email'] ?? null;
        $name = $googleUser['name'] ?? null;
        $googleId = $googleUser['sub'] ?? null;

        if (!$email) throw new AppException(401, 'Invalid Google user data');

        // Check if user exists in your DB
        $user = $userService->getByProviderId($googleId);

        if (!$user) {
            // Register a new user
            if ($userService->getByEmail($email)) throw new AppException(401, 'User already exists, please login');

            $name = explode(' ', $name);
            $data['firstname'] = $name[0];
            if(isset($name[1])) $data['surname'] = $name[1];
            $data['email'] = $email;
            $data['providerId'] = $googleId;

            $user = $userService->save($data, true);
        }

        // Log in and generate token
        $token = Auth::login($user);

        $tokenData = $this->respondWithToken($token);

        return ["user" => $user, "tokenData" => $tokenData];

        
        // $googleUser = Socialite::driver('google')->stateless()->user();
        // // $state = $request->query('state', 'login');

        // // Try to find user by email
        // $user = $userService->getByEmail($googleUser->getEmail());

        // if ($state === 'register' && !$user) {
        //     // Register new user
        //     $name = explode(' ', $googleUser->getName());
        //     $data['firstname'] = $name[0];
        //     if(isset($name[1])) $data['surname'] = $name[1];
        //     $data['email'] = $googleUser->getEmail();
        //     $data['providerId'] = $googleUser->getId();
        //     // $user = User::create([
        //     //     'name' => $googleUser->getName(),
        //     //     'email' => $googleUser->getEmail(),
        //     //     'google_id' => $googleUser->getId(),
        //     //     'avatar' => $googleUser->getAvatar(),
        //     // ]);
        // } elseif (!$user) {
        //     return response()->json(['message' => 'No account found, please register first.'], 404);
        // }
    }

    public function loginUser($user)
    {
        $token = Auth::login($user);

        $tokenData = $this->respondWithToken($token);

        return ["user" => $user, "tokenData" => $tokenData];
    }

    public function refreshToken($refreshToken)
    {
        $userService = new UserService;
        try {
            // dd($refreshToken);
            $user = $userService->getUserByRefreshToken($refreshToken);
            if(!$user) throw new AppException(401, 'Invalid refresh token');

            $device = $this->getDeviceInfo();
            // dd($user->refresh_token_device.' !== '.$device);
            if($user->refresh_token_device !== $device) throw new AppException(401, 'Invalid device for refresh token');

            if ($user->suspended) {
                throw new AppException(402, 'User account suspended');
            }

            $token = JWTAuth::fromUser($user);
            $tokenData = $this->respondWithToken($token);

            $userService->updateRefreshTokenData($user, $this->refreshToken, $this->device);

            return ["user" => $user, "tokenData" => $tokenData];

        } catch (AppException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new AppException(500, 'Could not refresh token', $e);
        }
    }

    protected function respondWithToken($token)
    {
        $payload = JWTAuth::setToken($token)->getPayload();
        $this->refreshToken = Helpers::randomAlphaNumeric(12);
        $this->device = $this->getDeviceInfo();

        return [
            'accessToken' => $token,
            'tokenType' => 'Bearer',
            'expiresIn' => $payload->get('exp') - time(),
            'issuedAt' => $payload->get('iat'),
            'notBefore' => $payload->get('nbf'),
            'claims' => $payload->toArray(),
            'refreshToken' => $this->refreshToken
        ];
    }

    private function getDeviceInfo()
    {
        $agent = new Agent();
        
        $device = $agent->isMobile() ? 'mobile' : 'desktop';
        $os = $agent->platform();
        $osVersion = $agent->version($agent->platform());
        
        return "{$device}-{$os}-{$osVersion}";
    }
}
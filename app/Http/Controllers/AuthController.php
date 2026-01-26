<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Exceptions\AppException;

use App\Http\Requests\Login;
use App\Http\Requests\RefreshToken;
use App\Http\Requests\Register;
use App\Http\Requests\VerifyEmail;
use App\Http\Requests\ValidateEmailToken;

use App\Http\Resources\UserResource;

use App\Services\AuthService;
use App\Services\UserService;
use App\Services\EmailService;

use App\Mail\EmailVerification;
use App\Mail\NewRegistration;

use App\Utilities;

class AuthController extends Controller
{
    protected $authService;
    protected $userService;
    private $emailService;

    public function __construct()
    {
        $this->authService = new AuthService;
        $this->userService = new UserService;
        $this->emailService = new EmailService;
    }
    
    //Send email verification before registration
    public function sendVerificationMail(VerifyEmail $request)
    {
        try{
            $user = $this->userService->getByEmail($request->email);
            if($user) return Utilities::error402('This email is already registered, please login');

            $emailToken = $this->emailService->saveEmailVerificationToken($request->email);
            $mail = Mail::to($request->email)->send(new EmailVerification($emailToken));
            // if (Mail::failures()) {
            //     return response()->json(['status' => 'fail', 'message' => 'Failed to send email.']);
            // }
            return Utilities::okay('Verification mail sent successfully');
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to send verification mail, Please try again later or contact support');
        }
    }

    //Verify the token sent to the email
    public function verifyEmailToken(ValidateEmailToken $request)
    {
        try{
            $data = $request->validated();
            $emailToken = $this->emailService->emailExists($data['email']);
            if($emailToken && $emailToken->verified) return Utilities::error402("Your email has been verified already, Go ahead and login");
            $response = $this->emailService->validateEmailToken($data);
            if($response['success']) {
                return Utilities::okay("Validation Successful");
            }else{
                return response()->json([
                    'statusCode' => 402,
                    'message' => $response['error']
                ], 402);
            }
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to send verification mail, Please try again later or contact support');
        }
    }

    public function register(Register $request)
    {
        try{
            $data = $request->validated();

            if(!$this->emailService->emailVerified($data['email'])) return Utilities::error402("Email has not been verified");

            $user = $this->userService->save($data);

            $response = $this->authService->loginUser($user);

            return Utilities::ok([
                "user" => new UserResource($response['user']),
                "tokenData" => $response['tokenData']
            ]);

            // return Utilities::ok(new UserService($user));
        } catch(AppException $e){
            throw $e;
        }
    }
    
    public function login(Login $request)
    {
    // dd('hello');
        $credentials = $request->only('email', 'password');

        try {
            $response = $this->authService->login($credentials);

            return Utilities::ok([
                "user" => new UserResource($response['user']),
                "tokenData" => $response['tokenData']
            ]);

        } catch(AppException $e){
            throw $e;
        }
    }

    public function refreshToken(RefreshToken $request)
    {
        try{
            $response = $this->authService->refreshToken($request->validated('refreshToken'));

            return Utilities::ok([
                "user" => new UserResource($response['user']),
                "tokenData" => $response['tokenData']
            ]);

        }catch(AppException $e){
            throw $e;
        }
    }
}

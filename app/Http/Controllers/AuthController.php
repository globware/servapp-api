<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\AppException;

use App\Http\Requests\Login;
use App\Http\Requests\RefreshToken;
use App\Http\Requests\Register;
use App\Http\Requests\VerifyEmail;
use App\Http\Requests\ValidateEmailToken;
use App\Http\Requests\SendPasswordResetCode;
use App\Http\Requests\ResetPassword;
use App\Http\Requests\VerifyPasswordResetToken;

use app\Mail\SendPasswordResetCode as sendPasswordResetCodeMail;

use App\Http\Resources\UserResource;

use App\Services\AuthService;
use App\Services\UserService;
use App\Services\EmailService;
use App\Services\PasswordService;
use App\Services\UserProfileService;

use App\Mail\EmailVerification;
use App\Mail\NewRegistration;

use App\Utilities;

class AuthController extends Controller
{
    protected $authService;
    protected $userService;
    private $emailService;
    private $passwordService;
    private $userProfileService;

    public function __construct()
    {
        $this->authService = new AuthService;
        $this->userService = new UserService;
        $this->emailService = new EmailService;
        $this->passwordService = new PasswordService;
        $this->userProfileService = new UserProfileService;
    }
    
    //Send email verification before registration
    public function sendVerificationMail(Request $request)
    {
        try{
            $user = Auth::user();

            $emailToken = $this->emailService->saveEmailVerificationToken($user->email);

            // Utilities::logStuff("sending mail");
            $mail = Mail::to($request->email)->send(new EmailVerification($emailToken));
            // Utilities::logStuff("done sending mail");

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
            $user = Auth::user();
            $data = $request->validated();
            $data['email'] = $user->email;
            $emailToken = $this->emailService->emailExists($user->email);
            if($emailToken && $emailToken->verified) return Utilities::error402("Your email has been verified already, Go ahead and login");
            $response = $this->emailService->validateEmailToken($data);
            if($response['success']) {
                $user = $this->userService->emailVerified($user);
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

            // if(!$this->emailService->emailVerified($data['email'])) return Utilities::error402("Email has not been verified");

            $user = $this->userService->save($data);

            try{
                $emailToken = $this->emailService->saveEmailVerificationToken($user->email);

                $mail = Mail::to($request->email)->send(new EmailVerification($emailToken));
            }catch(\Exception $e) {
                Utilities::error($e, "An Error occurred trying to send verification mail during registration");
            }

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

        } catch (AppException $e) {
            throw $e;

        } catch (\Exception $e) {
            throw new AppException(500, 'Could not login User', $e);
        }
    }

    public function sendPasswordResetCode(SendPasswordResetCode $request)
    {
        try{
            $email = $request->validated('email');
            $user = $this->userService->getByEmail($email);
            if(!$user) return Utilities::error402("We cant find this email in our Database");

            DB::beginTransaction();
            $token = $this->passwordService->savePasswordResetToken($email, PasswordTypes::USER->value);
            Mail::to($email)->send(new SendPasswordResetCodeMail($token));
            DB::commit();
            return Utilities::okay(['message'=>'Reset Token Sent']);
        }catch(\Exception $e){
            DB::rollBack();
            return Utilities::error($e, 'An error occured while trying to send verification mail, Please try again later or contact support');
        }
    }

    public function verifyPasswordResetToken(VerifyPasswordResetToken $request)
    {
        try{
            $data = $request->validated();

            $user = $this->userService->getByEmail($data['email']);
            if(!$user) return Utilities::error402("User not found");
            
            $data['type'] = PasswordTypes::USER->value;
            $res = $this->passwordService->validateEmailToken($data);
            if($res['success']) return Utilities::okay('password verified successfully');
            return Utilities::error402($res['error']);
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occured while trying to send verification mail, Please try again later or contact support');
        }
    }

    public function resetPassword(ResetPassword $request)
    {
        try{
            $data = $request->validated();
            $resetToken = $this->passwordService->emailExists($data['email'], PasswordTypes::USER->value);
            if(!$resetToken) return Utilities::error402("You have not been cleared to reset this password, go through the password reset process");
            if(!$resetToken->verified) return Utilities::error402("Your password reset was not successful, click on the verify link sent to your mail");

            $user = $this->userService->getByEmail($data['email']);
            if(!$user) return Utilities::error402("no user exists for this email");

            $this->userProfileService->changePassword($data['password'], $user);
            return Utilities::okay('password Reset Successful');
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to send verification mail, Please try again later or contact support');
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

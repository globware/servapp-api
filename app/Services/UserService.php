<?php

namespace App\Services;

use App\Models\User;

use App\Services\EmailService;

use App\Exceptions\AppException;

use App\Utilities;

class UserService
{
    public $count = [];

    public function save($data, $fromGoogle=false)
    {
        $emailService = new EmailService;
        try{
            if(!$fromGoogle) {
                $emailVerified = $emailService->emailVerified($data['email']);

                // if(!$emailVerified) throw new AppException(402, "This Email has not been verified!");
            }

            $user = new User;
            $user->firstname = $data['firstname'];
            $user->surname = $data['surname'];
            $user->email = $data['email'];
            $user->phone_number = $data['phoneNumber'];
            if(isset($data['hasService'])) $user->has_service = $data['hasService'];
            if(isset($data['providerId'])) $user->provider_id = $data['providerId'];
            if(isset($data['locationId'])) $user->location_id = $data['locationId'];
            
            if(!$fromGoogle) $user->password = $data['password'];
            $user->referral_code = Utilities::generateReferalCode();
            if(isset($data['refererId'])) $user->referred_by = $data['refererId'];
            $user->email_verified_at = now();

            $user->save();

            return $user;
        }catch(\Exception $e){
            $errorCode = AppException::getDefaultErrorCode(402);
            throw $e;
            // throw new AppException(500, null, $e);
        }
    }

    public function update($data, $user)
    {
        try{
            $user->phone_number = $data['phoneNumber'];
            if(isset($data['hasService'])) $user->has_service = $data['hasService'];
            if(isset($data['locationId'])) $user->location_id = $data['locationId'];
            if(isset($data['photoId'])) $user->photo_id = $data['photoId'];
            if(isset($data['identificationId'])) $user->identification_id = $data['identificationId'];
            if(isset($data['identificationNo'])) $user->identification_no = $data['identificationNo'];

            $user->update();

            return $user;
        }catch(\Exception $e){
            $errorCode = AppException::getDefaultErrorCode(402);
            throw new AppException(500, null, $e);
        }
    }

    public function updateRefreshTokenData($user, $token, $device)
    {
        $user->refresh_token = $token;
        $user->refresh_token_device = $device;
        $user->last_login = now();
        $user->update();

        return $user;
    }

    public function getUser($id, $with=[])
    {
        return User::with($with)->withCount($this->count)->where("id", $id)->first();
    }

    public function getUserByRefreshToken($refreshToken)
    {
        return User::where("refresh_token", $refreshToken)->first();
    }

    public function getByEmail($email)
    {
        return User::where("email", $email)->first();
    }

    public function getByProviderId($providerId)
    {
        return User::where("provider_id", $providerId)->first();
    }

    
}
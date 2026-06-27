<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Exceptions\AppException;

use App\Http\Requests\ChangePassword;

use App\Http\Requests\SaveFcmToken;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Utilities;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function save(Request $request)
    {
        try{
            $user = $this->userService->save($request->all());

            return $user;
        }catch(AppException $e){
            throw $e;
        }
    }

    public function saveFCMToken(SaveFCMToken $request)
    {
        $user = Auth::user();
        $this->userService->saveFCMToken($user, $request->validated('token'));

        return response()->json(['success' => true]);
    }

    public function getProfile()
    {
        $user = $this->userService->getUser(Auth::user()->id);

        return Utilities::ok(new UserResource($user));
    }

    public function changePassword(ChangePassword $request)
    {
        try{
            $data = $request->validated();
            $user = $this->userService->getUser(Auth::user()->id);
            if(!$user) return Utilities::error402("not authenticated or user not found");

            if (!Hash::check($data['password'], $user->password)) {
                return Utilities::error402("Incorrect Password");
            }

            $this->userService->changePassword($data['newPassword'], $user);

            return Utilities::okay("Password changed successfully");
        }catch (AppException $e) {
            throw $e;
        } catch (\Exception $e) {
            return Utilities::error($e, "An error occurred while attempting to change Password");
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Exceptions\AppException;

use App\Http\Requests\SaveFCMToken;

use App\Services\UserService;

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
}

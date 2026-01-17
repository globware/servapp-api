<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exceptions\AppException;

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
}

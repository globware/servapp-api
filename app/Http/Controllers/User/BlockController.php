<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserBlock;
use App\Models\User;
use App\Utilities;
use App\Http\Resources\UserResource;

class BlockController extends Controller
{
    /**
     * @return array{status: boolean, message: string, data: array}
     */
    public function block($userId)
    {
        if (Auth::id() == $userId) return Utilities::error402("Cannot block yourself");
        
        $target = User::find($userId);
        if (!$target) return Utilities::error402("User not found");

        UserBlock::firstOrCreate([
            'user_id' => Auth::id(),
            'blocked_user_id' => $userId
        ]);

        return Utilities::ok([], "User blocked successfully");
    }

    /**
     * @return array{status: boolean, message: string, data: array}
     */
    public function unblock($userId)
    {
        UserBlock::where('user_id', Auth::id())->where('blocked_user_id', $userId)->delete();
        return Utilities::ok([], "User unblocked successfully");
    }

    /**
     * @return array{status: boolean, message: string, data: array<\App\Http\Resources\UserResource>}
     */
    public function index()
    {
        $blocks = UserBlock::with('blockedUser')->where('user_id', Auth::id())->get();
        $users = $blocks->map(fn($b) => $b->blockedUser);
        
        return Utilities::ok(UserResource::collection($users));
    }
}

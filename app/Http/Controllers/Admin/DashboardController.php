<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserService;
use App\Models\Admin;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalServices = UserService::count();
        $totalAdmins = Admin::count();

        return view('admin.dashboard', compact('totalUsers', 'totalServices', 'totalAdmins'));
    }

    public function users()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function services()
    {
        $services = UserService::with(['user', 'country', 'state'])->latest()->paginate(10);
        return view('admin.services.index', compact('services'));
    }

    public function verifyService($id)
    {
        $service = UserService::findOrFail($id);
        $service->verified = true;
        $service->save();

        return response()->json([
            'success' => true,
            'message' => 'Service verified successfully.'
        ]);
    }
}

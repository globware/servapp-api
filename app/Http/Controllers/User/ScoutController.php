<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ScoutService;
use App\Http\Resources\UnclaimedLeadResource;
use App\Utilities;

class ScoutController extends Controller
{
    public function __construct(protected ScoutService $scoutService)
    {
    }

    public function suggest(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lead_type' => 'required|in:service,product',
            'category_text' => 'nullable|string',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        try {
            $lead = $this->scoutService->suggestLead($validated, Auth::id());
            return Utilities::ok(new UnclaimedLeadResource($lead));
        } catch (\Exception $e) {
            return Utilities::error($e, $e->getMessage());
        }
    }
}

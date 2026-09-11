<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ScoutService;
use App\Http\Resources\UnclaimedLeadResource;
use App\Utilities;

class ClaimController extends Controller
{
    public function __construct(protected ScoutService $scoutService)
    {
    }

    public function claim(Request $request, $leadId)
    {
        try {
            // In the MVP, clicking claim instantly converts the lead into a verified asset for the provider.
            $lead = $this->scoutService->claimLead($leadId, Auth::id());
            
            return Utilities::ok([
                'message' => 'Successfully claimed!',
                'lead' => new UnclaimedLeadResource($lead)
            ]);
        } catch (\Exception $e) {
            return Utilities::error($e, $e->getMessage());
        }
    }
}

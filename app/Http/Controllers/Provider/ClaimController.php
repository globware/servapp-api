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

    /**
     * @return array{status: boolean, message: string, data: array<\App\Http\Resources\UnclaimedLeadResource>}
     */
    public function search(\Illuminate\Http\Request $request)
    {
        $query = $request->query('q', '');
        $lat = $request->query('lat');
        $long = $request->query('long');

        $search = \App\Models\UnclaimedLead::search($query)->where('status', 'unclaimed');
        
        if ($lat && $long) {
            // UnclaimedLeads would need _geo in toSearchableArray for Meilisearch radius, 
            // assuming it's set up or we just return the search results.
            // For now we just return the text search results if radius isn't strictly configured.
        }

        $leads = $search->take(20)->get();

        return Utilities::ok(\App\Http\Resources\UnclaimedLeadResource::collection($leads));
    }
}

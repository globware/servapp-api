<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\User\SuggestLeadRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\ScoutService;
use App\Http\Resources\UnclaimedLeadResource;
use App\Utilities;

class ScoutController extends Controller
{
    public function __construct(protected ScoutService $scoutService)
    {
    }

    public function suggest(SuggestLeadRequest $request)
    {
        $validated = $request->validated();

        try {
            $lead = $this->scoutService->suggestLead($validated, Auth::id());
            return Utilities::ok(new UnclaimedLeadResource($lead));
        } catch (\Exception $e) {
            return Utilities::error($e, $e->getMessage());
        }
    }

    /**
     * @return array{status: boolean, message: string, data: array<\App\Http\Resources\UnclaimedLeadResource>}
     */
    public function suggestions()
    {
        $leads = \App\Models\UnclaimedLead::where('suggested_by', \Illuminate\Support\Facades\Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return Utilities::ok(\App\Http\Resources\UnclaimedLeadResource::collection($leads));
    }
}

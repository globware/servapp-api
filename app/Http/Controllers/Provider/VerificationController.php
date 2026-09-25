<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProviderVerification;
use App\Utilities;

class VerificationController extends Controller
{
    /**
     * @return array{status: boolean, message: string, data: \App\Models\ProviderVerification}
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'id_file_id' => 'required|integer|exists:files,id',
            'business_file_id' => 'nullable|integer|exists:files,id',
        ]);

        $verification = ProviderVerification::updateOrCreate(
            ['provider_id' => Auth::id()],
            [
                'id_file_id' => $validated['id_file_id'],
                'business_file_id' => $validated['business_file_id'] ?? null,
                'status' => 'pending',
                'reason' => null
            ]
        );

        return Utilities::ok($verification, "Verification documents submitted successfully");
    }

    /**
     * @return array{status: boolean, message: string, data: \App\Models\ProviderVerification}
     */
    public function status()
    {
        $verification = ProviderVerification::where('provider_id', Auth::id())->first();
        
        if (!$verification) {
            return Utilities::ok([
                'status' => 'unsubmitted',
                'reason' => null
            ]);
        }

        return Utilities::ok($verification);
    }
}

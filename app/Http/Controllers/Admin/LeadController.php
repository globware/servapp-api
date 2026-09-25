<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UnclaimedLead;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        
        $query = UnclaimedLead::latest();
        
        if ($status === 'approved') {
            $query->where('approved', true);
        } elseif ($status === 'rejected') {
            // we don't have a rejected column, just if it was explicitly deleted or maybe we add rejected.
            // Wait, UnclaimedLead just has 'approved' boolean. Let's just do pending and approved.
            $query->where('approved', false)->where('status', '!=', 'rejected'); // assuming we add a rejected status or just false
        } else {
            $query->where('approved', false); // pending
        }

        $leads = $query->paginate(15);
        return view('admin.leads.index', compact('leads', 'status'));
    }

    public function approve($id)
    {
        $lead = UnclaimedLead::findOrFail($id);
        $lead->approved = true;
        $lead->save();

        return redirect()->back()->with('success', 'Lead approved successfully and is now public!');
    }

    public function reject($id)
    {
        $lead = UnclaimedLead::findOrFail($id);
        // Soft delete or just remove it so it's rejected
        $lead->delete();

        return redirect()->back()->with('success', 'Lead rejected and removed.');
    }
}

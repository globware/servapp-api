<?php

namespace App\Services;

use App\Models\UnclaimedLead;
use App\Models\UserService;
use App\Models\UserProduct;
use App\Exceptions\AppException;

class ScoutService
{
    public function suggestLead(array $data, $userId)
    {
        // ANTI-DUPLICATE ENGINE
        // 1. Check existing verified Services
        if ($data['lead_type'] === 'service') {
            $serviceExists = UserService::where('name', $data['title'])
                // In a real app we might also check location proximity
                ->exists();
            if ($serviceExists) {
                throw new AppException("A verified service with this name already exists.");
            }
        }

        // 2. Check existing verified Products
        if ($data['lead_type'] === 'product') {
            $productExists = UserProduct::where('name', $data['title'])->exists();
            if ($productExists) {
                throw new AppException("A verified product with this name already exists.");
            }
        }

        // 3. Check existing Unclaimed Leads (even unapproved ones)
        $leadExists = UnclaimedLead::where('title', $data['title'])
            ->where('lead_type', $data['lead_type'])
            ->exists();
            
        if ($leadExists) {
            throw new AppException("This lead has already been suggested and is pending claim or approval.");
        }

        // Create the lead
        $lead = UnclaimedLead::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'lead_type' => $data['lead_type'],
            'category_text' => $data['category_text'] ?? null,
            'address' => $data['address'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'status' => 'unclaimed',
            'approved' => false // Requires Admin approval
        ]);

        return $lead;
    }

    public function claimLead($leadId, $userId)
    {
        $lead = UnclaimedLead::where('status', 'unclaimed')->find($leadId);
        
        if (!$lead) {
            throw new AppException("This lead does not exist or has already been claimed.");
        }

        // For MVP, we instantly grant the claim. 
        // A full implementation might set status to 'claim_pending' and await Admin verification.
        $lead->status = 'verified';
        $lead->save();

        // Convert the lead into an actual Service or Product for the user
        if ($lead->lead_type === 'service') {
            UserService::create([
                'name' => $lead->title,
                'description' => $lead->description,
                'address' => $lead->address,
                'latitude' => $lead->latitude,
                'longitude' => $lead->longitude,
                'user_id' => $userId,
                'service_id' => 1, // Default fallback or mapped from category
                'active' => true,
                'approved' => true
            ]);
        } else {
            UserProduct::create([
                'name' => $lead->title,
                'description' => $lead->description,
                'address' => $lead->address,
                'latitude' => $lead->latitude,
                'longitude' => $lead->longitude,
                'user_id' => $userId,
                'product_id' => 1, // Default fallback
                'active' => true,
            ]);
        }

        return $lead;
    }
}
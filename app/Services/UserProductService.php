<?php

namespace App\Services;

use App\Models\UserProduct;
use App\Models\UnclaimedLead;
use Illuminate\Support\Facades\DB;
use App\Exceptions\AppException;

class UserProductService
{
    public function searchProductsAndLeads($query)
    {
        $products = UserProduct::search($query)->where('active', true)->get();
        $leads = UnclaimedLead::search($query)->where('lead_type', 'product')->where('active', true)->get();
        
        return [
            'products' => $products,
            'leads' => $leads
        ];
    }

    public function getProviderProducts($userId)
    {
        return UserProduct::where('user_id', $userId)->get();
    }

    public function getProduct($id)
    {
        return UserProduct::find($id);
    }

    public function save(array $data, $userId)
    {
        // Duplicate check
        $exists = UserProduct::where('user_id', $userId)->where('name', $data['name'])->exists();
        if ($exists) {
            throw new AppException("You already have a product with this name.");
        }

        // Scouting Interception
        $lead = UnclaimedLead::where('lead_type', 'product')
            ->where('title', 'like', '%' . $data['name'] . '%')
            ->where('status', 'unclaimed')
            ->first();

        if ($lead) {
            // Throwing a custom exception with payload so the controller can return the special 200 response
            throw new \Exception(json_encode([
                'is_scout_intercept' => true,
                'message' => 'We found a suggested business matching this name. Would you like to claim it?',
                'claim_lead_id' => $lead->id,
                'lead' => $lead
            ]));
        }

        $data['user_id'] = $userId;
        $data['active'] = true;
        
        return UserProduct::create($data);
    }

    public function update($id, array $data, $userId)
    {
        $product = UserProduct::where('user_id', $userId)->find($id);
        if (!$product) throw new AppException("Product not found or unauthorized");

        $product->update($data);
        return $product;
    }

    public function delete($id, $userId)
    {
        $product = UserProduct::where('user_id', $userId)->find($id);
        if (!$product) throw new AppException("Product not found or unauthorized");

        $product->delete();
        return true;
    }
}
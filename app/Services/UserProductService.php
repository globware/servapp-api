<?php

namespace App\Services;

use App\Models\UserProduct;
use App\Models\UnclaimedLead;
use Illuminate\Support\Facades\DB;
use App\Exceptions\AppException;
use App\Services\ProviderLocationService;

class UserProductService
{
    public function __construct(protected ProviderLocationService $providerLocationService)
    {
    }

    public function searchProductsAndLeads($query)
    {
        $products = UserProduct::search($query)->where('active', true)->get();
        $leads = UnclaimedLead::search($query)->where('lead_type', 'product')->where('status', 'unclaimed')->where('approved', true)->get();
        
        return [
            'products' => $products,
            'leads' => $leads
        ];
    }

    public function getProviderProducts($userId, $with = ['media', 'product', 'providerLocation'])
    {
        return UserProduct::with($with)->where('user_id', $userId)->get();
    }

    public function getProduct($id, $with = ['media', 'product', 'providerLocation'])
    {
        return UserProduct::with($with)->find($id);
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

        // Handle provider location
        if (!empty($data['provider_location_id'])) {
            $providerLocation = $this->providerLocationService->getLocation($data['provider_location_id'], $userId);
            if (!$providerLocation) {
                throw new AppException("Selected provider location not found or unauthorized.");
            }
            $data['address'] = $data['address'] ?? $providerLocation->address;
            $data['latitude'] = $data['latitude'] ?? $providerLocation->latitude;
            $data['longitude'] = $data['longitude'] ?? $providerLocation->longitude;
            $data['country_id'] = $providerLocation->country_id;
            $data['state_id'] = $providerLocation->state_id;
            $data['location_id'] = $providerLocation->location_id;
        } elseif (!empty($data['save_location']) && !empty($data['address'])) {
            $savedLocation = $this->providerLocationService->save([
                'title' => $data['location_title'] ?? null,
                'address' => $data['address'],
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'country_id' => $data['country_id'] ?? null,
                'state_id' => $data['state_id'] ?? null,
                'location_id' => $data['location_id'] ?? null,
            ], $userId);
            $data['provider_location_id'] = $savedLocation->id;
        }

        $data['user_id'] = $userId;
        $data['active'] = true;
        unset($data['mediaIds'], $data['save_location'], $data['location_title']);
        
        $product = UserProduct::create($data);
        return $product->fresh(['media', 'product', 'providerLocation']);
    }

    public function update($id, array $data, $userId)
    {
        $product = UserProduct::where('user_id', $userId)->find($id);
        if (!$product) throw new AppException("Product not found or unauthorized");

        // Handle provider location
        if (isset($data['provider_location_id'])) {
            $providerLocation = $this->providerLocationService->getLocation($data['provider_location_id'], $userId);
            if (!$providerLocation) {
                throw new AppException("Selected provider location not found or unauthorized.");
            }
            $data['address'] = $providerLocation->address;
            $data['latitude'] = $providerLocation->latitude;
            $data['longitude'] = $providerLocation->longitude;
            $data['country_id'] = $providerLocation->country_id;
            $data['state_id'] = $providerLocation->state_id;
            $data['location_id'] = $providerLocation->location_id;
        } elseif (!empty($data['save_location']) && (!empty($data['address']) || !empty($product->address))) {
            $savedLocation = $this->providerLocationService->save([
                'title' => $data['location_title'] ?? null,
                'address' => $data['address'] ?? $product->address,
                'latitude' => $data['latitude'] ?? $product->latitude,
                'longitude' => $data['longitude'] ?? $product->longitude,
                'country_id' => $data['country_id'] ?? $product->country_id,
                'state_id' => $data['state_id'] ?? $product->state_id,
                'location_id' => $data['location_id'] ?? $product->location_id,
            ], $userId);
            $data['provider_location_id'] = $savedLocation->id;
        }

        unset($data['mediaIds'], $data['save_location'], $data['location_title']);

        $product->update($data);
        return $product->fresh(['media', 'product', 'providerLocation']);
    }

    public function delete($id, $userId)
    {
        $product = UserProduct::where('user_id', $userId)->find($id);
        if (!$product) throw new AppException("Product not found or unauthorized");

        $product->delete();
        return true;
    }
}
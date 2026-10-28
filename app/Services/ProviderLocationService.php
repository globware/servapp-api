<?php

namespace App\Services;

use App\Models\ProviderLocation;
use App\Exceptions\AppException;
use Illuminate\Support\Str;

class ProviderLocationService
{
    public function getLocations($userId, array $with = ['country', 'state', 'location'])
    {
        return ProviderLocation::with($with)
            ->where('user_id', $userId)
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();
    }

    public function getLocation($id, $userId, array $with = ['country', 'state', 'location'])
    {
        return ProviderLocation::with($with)
            ->where('user_id', $userId)
            ->where('id', $id)
            ->first();
    }

    public function save(array $data, $userId)
    {
        $hasExisting = ProviderLocation::where('user_id', $userId)->exists();

        $isDefault = false;
        if (isset($data['is_default']) && filter_var($data['is_default'], FILTER_VALIDATE_BOOLEAN)) {
            $isDefault = true;
            ProviderLocation::where('user_id', $userId)->update(['is_default' => false]);
        } elseif (!$hasExisting) {
            $isDefault = true;
        }

        $title = $data['title'] ?? null;
        if (empty($title) && !empty($data['address'])) {
            $title = Str::limit(trim(explode(',', $data['address'])[0]), 100, '');
        }

        $location = ProviderLocation::create([
            'user_id' => $userId,
            'title' => $title ?: 'Primary Location',
            'address' => $data['address'],
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'country_id' => $data['country_id'] ?? null,
            'state_id' => $data['state_id'] ?? null,
            'location_id' => $data['location_id'] ?? null,
            'is_default' => $isDefault,
        ]);

        return $location->fresh(['country', 'state', 'location']);
    }

    public function update($id, array $data, $userId)
    {
        $location = ProviderLocation::where('user_id', $userId)->find($id);
        if (!$location) {
            throw new AppException(404, "Provider location not found or unauthorized");
        }

        if (isset($data['is_default']) && filter_var($data['is_default'], FILTER_VALIDATE_BOOLEAN)) {
            ProviderLocation::where('user_id', $userId)->where('id', '!=', $id)->update(['is_default' => false]);
            $data['is_default'] = true;
        }

        $location->update($data);

        return $location->fresh(['country', 'state', 'location']);
    }

    public function delete($id, $userId)
    {
        $location = ProviderLocation::where('user_id', $userId)->find($id);
        if (!$location) {
            throw new AppException(404, "Provider location not found or unauthorized");
        }

        $wasDefault = $location->is_default;
        $location->delete();

        // If the deleted location was default, set the oldest remaining location as default
        if ($wasDefault) {
            $next = ProviderLocation::where('user_id', $userId)->orderBy('created_at', 'ASC')->first();
            if ($next) {
                $next->update(['is_default' => true]);
            }
        }

        return true;
    }
}

<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserProduct;
use App\Models\UnclaimedLead;
use App\Utilities;

use App\Services\UserProductService;
use App\Http\Resources\UserProductResource;

class ProductController extends Controller
{
    public function __construct(protected UserProductService $productService)
    {
    }

    public function index(Request $request)
    {
        $products = $this->productService->getProviderProducts(Auth::id());
        return Utilities::ok(UserProductResource::collection($products));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'product_id' => 'required|exists:products,id' 
        ]);

        try {
            $product = $this->productService->save($validated, Auth::id());
            return Utilities::ok(new UserProductResource($product));
        } catch (\Exception $e) {
            $decoded = json_decode($e->getMessage(), true);
            if (isset($decoded['is_scout_intercept'])) {
                return Utilities::ok($decoded); // Special payload for UI claim interception
            }
            return Utilities::error($e, $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'active' => 'boolean'
        ]);

        try {
            $product = $this->productService->update($id, $validated, Auth::id());
            return Utilities::ok(new UserProductResource($product));
        } catch (\Exception $e) {
            return Utilities::error($e, $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->productService->delete($id, Auth::id());
            return Utilities::okay("Product deleted successfully");
        } catch (\Exception $e) {
            return Utilities::error($e, $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Provider\StoreProductRequest;
use App\Http\Requests\Provider\UpdateProductRequest;
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



    /**
     * @return array{status: boolean, message: string, data: array<\App\Http\Resources\UserProductResource>}
     */
    public function index(Request $request)
    {
        $products = $this->productService->getProviderProducts(Auth::id());
        return Utilities::ok(UserProductResource::collection($products));
    }



    /**
     * @return array{status: boolean, message: string, data: \App\Http\Resources\UserProductResource}
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        try {
            $product = $this->productService->save($validated, Auth::id());
            if ($request->has('mediaIds')) {
            $product->media()->sync($validated['mediaIds']);
        }
        
        return Utilities::ok(new UserProductResource($product));
        } catch (\Exception $e) {
            $decoded = json_decode($e->getMessage(), true);
            if (isset($decoded['is_scout_intercept'])) {
                return Utilities::ok($decoded); // Special payload for UI claim interception
            }
            return Utilities::error($e, $e->getMessage());
        }
    }



    /**
     * @return array{status: boolean, message: string, data: \App\Http\Resources\UserProductResource}
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $validated = $request->validated();

        try {
            $product = $this->productService->update($id, $validated, Auth::id());
            if ($request->has('mediaIds')) {
            $product->media()->sync($validated['mediaIds']);
        }
        
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

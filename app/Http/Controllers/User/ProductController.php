<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserProductService;
use App\Http\Resources\UserProductResource;
use App\Http\Resources\UnclaimedLeadResource;
use App\Utilities;

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
        $query = $request->query('query', '');
        $results = $this->productService->searchProductsAndLeads($query);

        $merged = collect();
        foreach ($results['products'] as $product) {
            $merged->push(new UserProductResource($product));
        }
        foreach ($results['leads'] as $lead) {
            $merged->push(new UnclaimedLeadResource($lead));
        }

        return Utilities::ok($merged->values());
    }


    /**
     * @return array{status: boolean, message: string, data: \App\Http\Resources\UserProductResource}
     */
    public function show($id)
    {
        $product = $this->productService->getProduct($id);
        if (!$product) return Utilities::error402("Product not found");

        return Utilities::ok(new UserProductResource($product));
    }
}

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
        /**
     * @return array{status: boolean, message: string, data: array<\App\Http\Resources\UserProductResource>}
     */
    public function index(Request $request)
    {
        $query = $request->query('q', $request->query('query', ''));
        $categoryId = $request->query('categoryId');
        $lat = $request->query('lat');
        $long = $request->query('long');
        $radiusKm = $request->query('radiusKm', 50);

        $search = \App\Models\UserProduct::search($query);
        
        if ($lat && $long) {
            // Meilisearch geo search implementation
            // $search->options(['filter' => '_geoRadius('.$lat.', '.$long.', '.$radiusKm.'*1000)']);
            // NOTE: Scout options callback might be needed depending on Scout version
        }
        
        // For MVP we just use the custom service that merges leads as well
        $results = $this->productService->searchProductsAndLeads($query);

        $merged = collect();
        foreach ($results['products'] as $product) {
            $merged->push(new UserProductResource($product));
        }
        // Leads do not typically match product searches natively unless configured, but included per MVP spec
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

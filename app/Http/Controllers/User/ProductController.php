<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserProduct;
use App\Models\UnclaimedLead;
use App\Utilities;

class ProductController extends Controller
{
    /**
     * Fetch products and unclaimed product leads using Meilisearch.
     */
    public function index(Request $request)
    {
        $query = $request->input('query', '');
        
        // 1. Search verified active products
        $products = UserProduct::search($query)
            ->where('active', true)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'title' => $product->name,
                    'description' => $product->description,
                    'address' => $product->address,
                    'latitude' => $product->latitude,
                    'longitude' => $product->longitude,
                    'price' => $product->price,
                    'is_unclaimed' => false,
                    'type' => 'product'
                ];
            });

        // 2. Search unclaimed product leads
        $unclaimedLeads = UnclaimedLead::search($query)
            ->where('lead_type', 'product')
            ->where('active', true) // active = true means approved & unclaimed in our Scout config
            ->get()
            ->map(function ($lead) {
                return [
                    'id' => $lead->id,
                    'title' => $lead->title,
                    'description' => $lead->description,
                    'address' => $lead->address,
                    'latitude' => $lead->latitude,
                    'longitude' => $lead->longitude,
                    'price' => null,
                    'is_unclaimed' => true,
                    'type' => 'product_lead'
                ];
            });

        // 3. Merge and return
        $combinedResults = $products->merge($unclaimedLeads);

        return Utilities::ok($combinedResults);
    }
    
    public function show($id)
    {
        $product = UserProduct::find($id);
        if (!$product) {
            return Utilities::error402("Product not found");
        }
        
        return Utilities::ok($product);
    }
}

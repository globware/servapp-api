<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Utilities;

class ProductCategoryController extends Controller
{
    /**
     * @return array{status: boolean, message: string, data: array<\App\Models\Product>}
     */
    public function index()
    {
        $categories = Product::orderBy('name', 'asc')->get();
        return Utilities::ok($categories);
    }

    /**
     * @return array{status: boolean, message: string, data: array<\App\Models\Product>}
     */
    public function types($id)
    {
        // For MVP, products are a flat taxonomy in the Product table. 
        // To satisfy the mobile team's 2-level structure expectation, we return the same list or an empty list.
        // We'll return empty array for now since they are flat.
        return Utilities::ok([]);
    }
}

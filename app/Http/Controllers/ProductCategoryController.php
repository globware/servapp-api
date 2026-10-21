<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Resources\ProductResource;
use App\Utilities;

class ProductCategoryController extends Controller
{
    public function __construct(protected ProductService $productService)
    {
    }

    /**
     * @return array{status: boolean, message: string, data: array<\App\Http\Resources\ProductResource>}
     */
    public function index()
    {
        $categories = $this->productService->getProducts();
        return Utilities::ok(ProductResource::collection($categories));
    }
}

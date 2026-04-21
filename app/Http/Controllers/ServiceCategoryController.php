<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Resources\ServiceResource;

use App\Services\ServiceService;

use App\Utilities;

class ServiceCategoryController extends Controller
{
    public function __construct(protected ServiceService $categoryService)
    {
    }

    public function categories()
    {
        $categories = $this->categoryService->getServices();

        return Utilities::ok(ServiceResource::collection($categories));
    }
}

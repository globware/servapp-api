<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public $limit = null;

    /**
     * Get all product categories.
     *
     * @param array $with
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product>
     */
    public function getProducts(array $with = [])
    {
        $query = Product::with($with)->orderBy("name", "ASC");

        if ($this->limit) {
            $query->limit($this->limit);
        }

        return $query->get();
    }

    /**
     * Get a single product category by ID.
     *
     * @param int $id
     * @param array $with
     * @return \App\Models\Product|null
     */
    public function getProduct(int $id, array $with = [])
    {
        return Product::with($with)->where("id", $id)->first();
    }
}

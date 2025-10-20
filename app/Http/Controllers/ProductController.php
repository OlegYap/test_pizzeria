<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaginationRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(PaginationRequest $request)
    {
        $perPage = $request->perPage();

        $page = $request->input('page', 1);

        $cacheKey = "products_page_{$page}_per_{$perPage}";

        $products = Cache::remember($cacheKey,now()->addDay(), function () use ($perPage) {
            return Product::paginate($perPage);
        });

        return ProductResource::collection($products);
    }

    public function store(ProductRequest $request): ProductResource
    {
        $product = Product::create($request->validated());

        Cache::flush();

        return new ProductResource($product);
    }

    public function show(Product $product): ProductResource
    {
        $cacheKey = "products_show_{$product->id}";

        $cachedProduct = Cache::remember($cacheKey,now()->addDay(), function () use ($product) {
            return $product;
        });

        return new ProductResource($cachedProduct);
    }

    public function update(ProductRequest $request, Product $product): ProductResource
    {
        $product->update($request->validated());

        Cache::forget("product_{$product->id}");

        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        Cache::forget("products_{$product->id}");
        Cache::flush();

        return response()->noContent();
    }
}

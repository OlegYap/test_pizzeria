<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaginationRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\SearchProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(PaginationRequest $request)
    {
        $perPage = $request->perPage();

        $page = $request->input('page', 1);

        $cacheKey = "products_page_{$page}_per_{$perPage}";

        $products = Cache::tags(['products'])->remember($cacheKey, now()->addDay(), fn() => Product::paginate($perPage));

        return ProductResource::collection($products);
    }

    public function store(ProductRequest $request): ProductResource
    {
        $product = Product::create($request->validated());

        Cache::tags(['products'])->flush();

        return new ProductResource($product);
    }

    public function show(Product $product): ProductResource
    {
        $cacheKey = "products_show_{$product->id}";

        $cachedProduct = Cache::tags(['products'])->remember($cacheKey, now()->addDay(), fn() => $product);

        return new ProductResource($cachedProduct);
    }

    public function update(ProductRequest $request, Product $product): ProductResource
    {
        $product->update($request->validated());

        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        Cache::tags(['products'])->flush();

        return response()->noContent();
    }

    public function search(SearchProductRequest $request): JsonResponse
    {
        $query = $request->input('q');

        $products = Product::search($query)->get();

        return response()->json([
            'data' => $products,
            'count' => $products->count(),
        ]);
    }
}

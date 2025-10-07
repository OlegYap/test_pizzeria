<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();
        if ($request->has('page')) {
            return ProductResource::collection($query->paginate(15));
        }

        return ProductResource::collection($query->get());
    }

    public function store(ProductRequest $request): ProductResource
    {
        return new ProductResource(Product::create($request->validated()));
    }

    public function show(Product $product): ProductResource
    {
        return new ProductResource($product);
    }

    public function update(ProductRequest $request, Product $product): ProductResource
    {
        $product->update($request->validated());

        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->noContent();
    }
}

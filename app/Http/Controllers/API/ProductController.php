<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category')
            ->get();

        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $product = new Product();

        $product->fill($request->validated());

        $productSlug = Str::slug($product->getAttribute('name'));

        $product->setAttribute('slug', $productSlug);

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');

            $extension = $file->getClientOriginalExtension();

            $filename = 'thumbnail' . '.' . $extension;

            $file->storeAs('public/uploads/' . $productSlug, $filename);

            $product->setAttribute('thumbnail_filename', $filename);
        }

        $product_category_id = $request->safe()->category_id;

        if (Category::find($product_category_id)) {
            $product->setAttribute('category_id', $product_category_id);
        }

        $product->save();

        return response()->json([], 201, [
            'Location' => route('products.show', $product)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $product_id)
    {
        $product = Product::find($product_id)
            ->with('category')
            ->first();

        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());
        
        $productSlug = Str::slug($product->getAttribute('name'));

        $product->setAttribute('slug', $productSlug);

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');

            $extension = $file->getClientOriginalExtension();

            $filename = 'thumbnail' . '.' . $extension;

            $file->storeAs('public/uploads/' . $productSlug, $filename);

            $product->setAttribute('thumbnail_filename', $filename);
        }

        $product_category_id = $request->get('category_id');

        if (isset($product_category_id) && Category::find($product_category_id)) {
            $product->setAttribute('category_id', $product_category_id);
        }

        $product->save();

        return response()->json([], 201, [
            'Location' => route('products.show', $product)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([], 201);
    }
}

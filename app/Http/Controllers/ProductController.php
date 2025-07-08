<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    //

    public function index()
    {
        $products = Product::with('categories')->get();
        return response()->json($products, 200);
    }

    public function show(int $id)
    {
        $products = Product::with('categories')->findOrFail($id);
        return response()->json($products, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'sell_price' => 'required|numeric',
            'is_active' => 'boolean',
            'category_ids' => 'required|array'
        ]);

        $product = Product::create([
            'name' => $validated['name'],
            'sell_price' => $validated['sell_price'],
            'is_active' => $validated['is_active'] ?? true
        ]);

        if (!empty($validated['category_ids'])) {
            $product->categories()->attach($validated['category_ids']);
        }

        return response()->json($product->load('categories'), 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'       => 'sometimes|string|max:255',
            'sell_price' => 'sometimes|numeric',
            'is_active'  => 'boolean',
            'category_ids' => 'required|array'
        ]);

        $product = Product::findOrFail($id);
        $product->update($validated);

        if (isset($validated['category_ids'])) {
            $product->categories()->sync($validated['category_ids']);
        }

        return response()->json($product->load('categories'));
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->categories()->detach();

        $product->delete();

        return response()->json(['message' => 'Product deleted']);
    }
}

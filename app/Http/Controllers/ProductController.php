<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{

    public function index(Request $request)
    {
       
        $products = Product::with('categories')->where('user_id',Auth::user()->id)->get();
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
            'category_ids' => 'required|array'
        ]);

        $product = $request->user()->products()->create([
            'name' => $validated['name'],
            'sell_price' => $validated['sell_price'],
        ]);

  

        if (!empty($validated['category_ids'])) {
            $product->categories()->attach($validated['category_ids']);
        }

        return response()->json($product->load('categories'), 201);
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'sell_price' => 'required|numeric',
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

    public function destroy(int $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted']);
    } 
}

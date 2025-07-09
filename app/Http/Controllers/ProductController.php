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
        $query = Product::with('categories')
            ->where('user_id', Auth::id())
            ->latest();

      
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('products.name', 'like', '%' . $search . '%')
                    ->orWhere('products.sell_price', 'like', '%' . $search . '%');
            });
        }

       
        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        $products = $query->paginate($request->per_page ?? 10)
            ->appends($request->query());

        return response()->json($products);
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
            'sell_price' => 'required|numeric|min:1|gt:0',
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
            'sell_price' => 'required|numeric|min:1|gt:0',
            'is_active'  => 'boolean',
            'category_ids' => 'required|array'
        ]);

        $product = Product::findOrFail($id);
        $product->update($validated);

        if (isset($validated['category_ids'])) {
            $product->categories()->sync($validated['category_ids']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product->load('categories')
        ]);
    }

    public function destroy(int $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted']);
    }
}

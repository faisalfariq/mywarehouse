<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Models\AppLog;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('locations')->latest()->paginate(20);
        return view('pages.products.index', compact('products'));
    }

    public function create()
    {
        $categories = \App\Models\ProductCategory::all();
        $units = \App\Models\ProductUnit::all();
        return view('pages.products.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_code' => 'required|string|max:255|unique:products',
            'category_id' => 'required|exists:product_categories,id',
            'unit_id' => 'required|exists:product_units,id',
            'description' => 'nullable|string',
            'location_ids' => 'array',
            'location_ids.*' => 'exists:locations,id',
            'stock' => 'array',
            'stock.*' => 'integer|min:0'
        ]);

        try {
            \DB::beginTransaction();
            
            $product = Product::create([
                'product_name' => $validated['product_name'],
                'product_code' => $validated['product_code'],
                'category_id' => $validated['category_id'],
                'unit_id' => $validated['unit_id'],
                'description' => $validated['description'] ?? null
            ]);

            // Attach locations with stock
            if (isset($validated['location_ids'])) {
                $locationData = [];
                foreach ($validated['location_ids'] as $index => $locationId) {
                    $stock = $validated['stock'][$index] ?? 0;
                    $locationData[$locationId] = ['stock' => $stock];
                }
                $product->locations()->attach($locationData);
            }

            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'create',
                'module' => 'product',
                'ip_address' => $request->ip(),
            ]);

            \DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Product added successfully.',
                'data' => $product
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to add product: ' . $e->getMessage()
            ]);
        }
    }

    public function show(string $id)
    {
        $product = Product::with('locations')->findOrFail($id);
        AppLog::create([
            'user_id' => auth()->id(),
            'action' => 'show',
            'module' => 'product',
            'ip_address' => request()->ip(),
        ]);
        return view('pages.products.show', compact('product'));
    }

    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        $categories = \App\Models\ProductCategory::all();
        $units = \App\Models\ProductUnit::all();
        return view('pages.products.edit', compact('product', 'categories', 'units'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_code' => 'required|string|max:255|unique:products,product_code,' . $id,
            'category_id' => 'required|exists:product_categories,id',
            'unit_id' => 'required|exists:product_units,id',
            'description' => 'nullable|string',
            'location_ids' => 'array',
            'location_ids.*' => 'exists:locations,id',
            'stock' => 'array',
            'stock.*' => 'integer|min:0'
        ]);

        try {
            \DB::beginTransaction();
            
            $product = Product::findOrFail($id);
            $product->update([
                'product_name' => $validated['product_name'],
                'product_code' => $validated['product_code'],
                'category_id' => $validated['category_id'],
                'unit_id' => $validated['unit_id'],
                'description' => $validated['description'] ?? null
            ]);

            // Sync locations with stock
            if (isset($validated['location_ids'])) {
                $locationData = [];
                foreach ($validated['location_ids'] as $index => $locationId) {
                    $stock = $validated['stock'][$index] ?? 0;
                    $locationData[$locationId] = ['stock' => $stock];
                }
                $product->locations()->sync($locationData);
            } else {
                $product->locations()->detach();
            }

            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'update',
                'module' => 'product',
                'ip_address' => $request->ip(),
            ]);

            \DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully.',
                'data' => $product
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update product: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy(string $id)
    {
        try {
            \DB::beginTransaction();
            $product = Product::findOrFail($id);
            // Cek relasi
            if ($product->mutations()->exists() || $product->locations()->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot delete this product because it is already used in other data.'
                ]);
            }
            $product->delete();
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'delete',
                'module' => 'product',
                'ip_address' => request()->ip(),
            ]);
            \DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Product has been deleted successfully.'
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete product: ' . $e->getMessage()
            ]);
        }
    }
}

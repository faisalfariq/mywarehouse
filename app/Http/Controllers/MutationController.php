<?php

namespace App\Http\Controllers;

use App\Models\Mutation;
use App\Models\Product;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\AppLog;

class MutationController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $mutations = Mutation::with(['user', 'product', 'location'])
            ->when($keyword, function($query) use ($keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->where('mutation_type', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('note', 'LIKE', '%' . $keyword . '%')
                        ->orWhereHas('user', function($qu) use ($keyword) {
                            $qu->where('name', 'LIKE', '%' . $keyword . '%');
                        })
                        ->orWhereHas('product', function($qu) use ($keyword) {
                            $qu->where('product_name', 'LIKE', '%' . $keyword . '%');
                        })
                        ->orWhereHas('location', function($qu) use ($keyword) {
                            $qu->where('location_name', 'LIKE', '%' . $keyword . '%');
                        });
                });
            })
            ->latest()
            ->paginate(20);
        return view('pages.mutations.index', compact('mutations'));
    }

    public function create()
    {
        $users = User::all();
        $products = Product::all();
        $locations = Location::all();
        return view('pages.mutations.create', compact('users', 'products', 'locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'location_id' => 'required|exists:locations,id',
            'date' => 'required|date',
            'mutation_type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string'
        ]);

        try {
            \DB::beginTransaction();
            
            // Check if product exists in location
            $product = Product::findOrFail($validated['product_id']);
            $location = Location::findOrFail($validated['location_id']);
            
            // Get current stock
            $currentStock = $product->locations()->where('location_id', $validated['location_id'])->first();
            $stockValue = $currentStock ? $currentStock->pivot->stock : 0;
            
            // Calculate new stock
            if ($validated['mutation_type'] == 'in') {
                $newStock = $stockValue + $validated['quantity'];
            } else {
                // Check if stock is sufficient for out
                if ($stockValue < $validated['quantity']) {
                    throw new \Exception('Insufficient stock for mutation out.');
                }
                $newStock = $stockValue - $validated['quantity'];
            }
            
            // Create mutation
            $mutation = Mutation::create($validated);
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'create',
                'module' => 'mutation',
                'ip_address' => $request->ip(),
            ]);
            
            // Update stock in pivot table
            if ($currentStock) {
                $product->locations()->updateExistingPivot($validated['location_id'], ['stock' => $newStock]);
            } else {
                $product->locations()->attach($validated['location_id'], ['stock' => $newStock]);
            }
            
            \DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Mutation added successfully.',
                'data' => $mutation
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to add mutation: ' . $e->getMessage()
            ]);
        }
    }

    public function show(string $id)
    {
        $mutation = Mutation::with(['user', 'product', 'location'])->findOrFail($id);
        AppLog::create([
            'user_id' => auth()->id(),
            'action' => 'show',
            'module' => 'mutation',
            'ip_address' => request()->ip(),
        ]);
        return view('pages.mutations.show', compact('mutation'));
    }

    public function edit(string $id)
    {
        $mutation = Mutation::findOrFail($id);
        $users = User::all();
        $products = Product::all();
        $locations = Location::all();
        return view('pages.mutations.edit', compact('mutation', 'users', 'products', 'locations'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'location_id' => 'required|exists:locations,id',
            'date' => 'required|date',
            'mutation_type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string'
        ]);

        try {
            \DB::beginTransaction();
            
            $mutation = Mutation::findOrFail($id);
            
            // Revert previous mutation effect
            $oldProduct = Product::findOrFail($mutation->product_id);
            $oldLocation = Location::findOrFail($mutation->location_id);
            $oldStock = $oldProduct->locations()->where('location_id', $mutation->location_id)->first();
            $oldStockValue = $oldStock ? $oldStock->pivot->stock : 0;
            
            if ($mutation->mutation_type == 'in') {
                $revertedStock = $oldStockValue - $mutation->quantity;
            } else {
                $revertedStock = $oldStockValue + $mutation->quantity;
            }
            
            if ($oldStock) {
                $oldProduct->locations()->updateExistingPivot($mutation->location_id, ['stock' => $revertedStock]);
            }
            
            // Apply new mutation effect
            $newProduct = Product::findOrFail($validated['product_id']);
            $newLocation = Location::findOrFail($validated['location_id']);
            $newStock = $newProduct->locations()->where('location_id', $validated['location_id'])->first();
            $newStockValue = $newStock ? $newStock->pivot->stock : 0;
            
            if ($validated['mutation_type'] == 'in') {
                $finalStock = $newStockValue + $validated['quantity'];
            } else {
                if ($newStockValue < $validated['quantity']) {
                    throw new \Exception('Insufficient stock for mutation out.');
                }
                $finalStock = $newStockValue - $validated['quantity'];
            }
            
            // Update mutation
            $mutation->update($validated);
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'update',
                'module' => 'mutation',
                'ip_address' => $request->ip(),
            ]);
            
            // Update stock in pivot table
            if ($newStock) {
                $newProduct->locations()->updateExistingPivot($validated['location_id'], ['stock' => $finalStock]);
            } else {
                $newProduct->locations()->attach($validated['location_id'], ['stock' => $finalStock]);
            }
            
            \DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Mutation updated successfully.',
                'data' => $mutation
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update mutation: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy(string $id)
    {
        try {
            \DB::beginTransaction();
            $mutation = Mutation::findOrFail($id);
            
            // Revert mutation effect
            $product = Product::findOrFail($mutation->product_id);
            $location = Location::findOrFail($mutation->location_id);
            $currentStock = $product->locations()->where('location_id', $mutation->location_id)->first();
            $stockValue = $currentStock ? $currentStock->pivot->stock : 0;
            
            if ($mutation->mutation_type == 'in') {
                $newStock = $stockValue - $mutation->quantity;
            } else {
                $newStock = $stockValue + $mutation->quantity;
            }
            
            if ($currentStock) {
                $product->locations()->updateExistingPivot($mutation->location_id, ['stock' => $newStock]);
            }
            
            $mutation->delete();
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'delete',
                'module' => 'mutation',
                'ip_address' => request()->ip(),
            ]);
            \DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Mutation deleted successfully.'
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete mutation: ' . $e->getMessage()
            ]);
        }
    }
}

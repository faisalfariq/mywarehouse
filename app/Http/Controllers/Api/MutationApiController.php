<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mutation;
use App\Models\Product;
use App\Models\Location;
use App\Models\AppLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/** 
 * @OA\Tag(
 *     name="F. Mutations",
 *     description="API Endpoints for Stock Mutation management"
 * )
 */
class MutationApiController extends Controller 
{
    /**
     * Calculate current stock for a product at a specific location
     * 
     * @param int $productId
     * @param int $locationId
     * @param int|null $excludeMutationId
     * @return int
     */
    private function calculateCurrentStock(int $productId, int $locationId, ?int $excludeMutationId = null): int
    {
        $query = Mutation::where('product_id', $productId)
            ->where('location_id', $locationId);
        
        if ($excludeMutationId) {
            $query->where('id', '!=', $excludeMutationId);
        }
        
        return $query->get()->sum(function($mutation) {
            return $mutation->mutation_type === 'in' ? $mutation->quantity : -$mutation->quantity;
        });
    }

    /**
     * @OA\Get(
     *     path="/api/v1/mutations",
     *     summary="Get all mutations",
     *     tags={"Mutations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search mutations by product name or code",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="mutation_type",
     *         in="query",
     *         description="Filter by mutation type (in/out)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"in", "out"})
     *     ),
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         description="Filter by product ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="location_id",
     *         in="query",
     *         description="Filter by location ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mutations retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Mutations retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Mutation")),
     *                 @OA\Property(property="total", type="integer", example=5)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Mutation::with(['product', 'location', 'user']);
            
            if ($request->has('search') && $request->search) {
                $query->whereHas('product', function($q) use ($request) {
                    $q->where('product_name', 'like', '%' . $request->search . '%')
                      ->orWhere('product_code', 'like', '%' . $request->search . '%');
                });
            }
            
            if ($request->has('mutation_type') && $request->mutation_type) {
                $query->where('mutation_type', $request->mutation_type);
            }
            
            if ($request->has('product_id') && $request->product_id) {
                $query->where('product_id', $request->product_id);
            }
            
            if ($request->has('location_id') && $request->location_id) {
                $query->where('location_id', $request->location_id);
            }
            
            $perPage = $request->get('per_page', 10);
            $mutations = $query->orderBy('created_at', 'desc')->paginate($perPage);
            
            // Log activity
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'view',
                'module' => 'mutation',
                'ip_address' => $request->ip(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Mutations retrieved successfully.',
                'data' => $mutations
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve mutations.',
                'errors' => null
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/mutations",
     *     summary="Create a new mutation",
     *     tags={"Mutations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id","location_id","mutation_type","quantity","note"},
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="location_id", type="integer", example=1),
     *             @OA\Property(property="mutation_type", type="string", enum={"in", "out"}, example="in"),
     *             @OA\Property(property="quantity", type="integer", example=10),
     *             @OA\Property(property="note", type="string", example="Stock in from supplier")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Mutation created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Mutation created successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Mutation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'location_id' => 'required|exists:locations,id',
                'mutation_type' => 'required|in:in,out',
                'quantity' => 'required|integer|min:1',
                'note' => 'required|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check stock availability for out mutation
            if ($request->mutation_type === 'out') {
                $product = Product::find($request->product_id);
                $location = Location::find($request->location_id);
                
                // Get current stock at this location
                $currentStock = $this->calculateCurrentStock($request->product_id, $request->location_id);
                
                if ($currentStock < $request->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient stock. Available: ' . $currentStock,
                        'errors' => null
                    ], 422);
                }
            }

            $mutation = Mutation::create([
                'product_id' => $request->product_id,
                'location_id' => $request->location_id,
                'user_id' => auth()->id(),
                'mutation_type' => $request->mutation_type,
                'quantity' => $request->quantity,
                'note' => $request->note,
            ]);

            $mutation->load(['product', 'location', 'user']);

            // Log activity
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'create',
                'module' => 'mutation',
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mutation created successfully.',
                'data' => $mutation
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create mutation.',
                'errors' => null
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/mutations/{id}",
     *     summary="Get mutation by ID",
     *     tags={"Mutations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mutation retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Mutation retrieved successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Mutation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Mutation not found"
     *     )
     * )
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $mutation = Mutation::with(['product', 'location', 'user'])->find($id);
            
            if (!$mutation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mutation not found.',
                    'errors' => null
                ], 404);
            }

            // Log activity
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'show',
                'module' => 'mutation',
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mutation retrieved successfully.',
                'data' => $mutation
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve mutation.',
                'errors' => null
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/mutations/{id}",
     *     summary="Update mutation",
     *     tags={"Mutations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="quantity", type="integer", example=15),
     *             @OA\Property(property="note", type="string", example="Updated description")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mutation updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Mutation updated successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Mutation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Mutation not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $mutation = Mutation::find($id);
            
            if (!$mutation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mutation not found.',
                    'errors' => null
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'quantity' => 'sometimes|required|integer|min:1',
                'note' => 'sometimes|required|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check stock availability for out mutation when quantity is being updated
            if ($mutation->mutation_type === 'out' && $request->has('quantity')) {
                $newQuantity = $request->quantity;
                $oldQuantity = $mutation->quantity;
                $quantityDifference = $newQuantity - $oldQuantity;
                
                if ($quantityDifference > 0) {
                    // Calculate current stock excluding this mutation
                    $currentStock = $this->calculateCurrentStock($mutation->product_id, $mutation->location_id, $mutation->id);
                    
                    // Add back the old quantity to get available stock
                    $availableStock = $currentStock + $oldQuantity;
                    
                    if ($availableStock < $newQuantity) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Insufficient stock. Available: ' . $availableStock,
                            'errors' => null
                        ], 422);
                    }
                }
            }

            $mutation->update([
                'quantity' => $request->quantity,
                'note' => $request->note
            ]);
            $mutation->load(['product', 'location', 'user']);

            // Log activity
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'update',
                'module' => 'mutation',
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mutation updated successfully.',
                'data' => $mutation
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update mutation.',
                'errors' => null
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/mutations/{id}",
     *     summary="Delete mutation",
     *     tags={"Mutations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mutation deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Mutation deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Mutation not found"
     *     )
     * )
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $mutation = Mutation::find($id);
            
            if (!$mutation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mutation not found.',
                    'errors' => null
                ], 404);
            }

            // Check stock availability before deleting out mutation
            if ($mutation->mutation_type === 'out') {
                // Calculate current stock excluding this mutation
                $currentStock = $this->calculateCurrentStock($mutation->product_id, $mutation->location_id, $mutation->id);
                
                // If deleting this mutation would cause negative stock, prevent deletion
                if ($currentStock < $mutation->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete mutation. Deleting this mutation would cause insufficient stock. Available: ' . $currentStock,
                        'errors' => null
                    ], 422);
                }
            }

            $mutationData = $mutation->mutation_type . ' ' . $mutation->quantity . ' ' . $mutation->product->name;
            $mutation->delete();

            // Log activity
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'delete',
                'module' => 'mutation',
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mutation deleted successfully.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete mutation.',
                'errors' => null
            ], 500);
        }
    }
} 
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
     *         name="type",
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
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('code', 'like', '%' . $request->search . '%');
                });
            }
            
            if ($request->has('type') && $request->type) {
                $query->where('type', $request->type);
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
     *             required={"product_id","location_id","type","quantity","description"},
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="location_id", type="integer", example=1),
     *             @OA\Property(property="type", type="string", enum={"in", "out"}, example="in"),
     *             @OA\Property(property="quantity", type="integer", example=10),
     *             @OA\Property(property="description", type="string", example="Stock in from supplier")
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
                'type' => 'required|in:in,out',
                'quantity' => 'required|integer|min:1',
                'description' => 'required|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check stock availability for out mutation
            if ($request->type === 'out') {
                $product = Product::find($request->product_id);
                $location = Location::find($request->location_id);
                
                // Get current stock at this location
                $currentStock = Mutation::where('product_id', $request->product_id)
                    ->where('location_id', $request->location_id)
                    ->get()
                    ->sum(function($mutation) {
                        return $mutation->type === 'in' ? $mutation->quantity : -$mutation->quantity;
                    });
                
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
                'type' => $request->type,
                'quantity' => $request->quantity,
                'description' => $request->description,
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
     *             @OA\Property(property="description", type="string", example="Updated description")
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
                'description' => 'sometimes|required|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $mutation->update($request->only(['quantity', 'description']));
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

            $mutationData = $mutation->type . ' ' . $mutation->quantity . ' ' . $mutation->product->name;
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
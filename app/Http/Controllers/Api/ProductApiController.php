<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductUnit;
use App\Models\AppLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="E. Products",
 *     description="API Endpoints for Product management"
 * )
 */
class ProductApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/products",
     *     summary="Get all products",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search products by name or code",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by category ID",
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
     *         description="Products retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Products retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
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
            $query = Product::with(['category', 'unit']);
            
            if ($request->has('search') && $request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('product_name', 'like', '%' . $request->search . '%')
                      ->orWhere('product_code', 'like', '%' . $request->search . '%');
                });
            }
            
            if ($request->has('category_id') && $request->category_id) {
                $query->where('category_id', $request->category_id);
            }
            
            $perPage = $request->get('per_page', 10);
            $products = $query->paginate($perPage);
            
            // Log activity
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'view',
                'module' => 'product',
                'ip_address' => $request->ip(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully.',
                'data' => $products
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products.',
                'errors' => null
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/products",
     *     summary="Create a new product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","code","category_id","unit_id","price"},
     *             @OA\Property(property="name", type="string", example="Laptop ASUS"),
     *             @OA\Property(property="code", type="string", example="LAP001"),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="unit_id", type="integer", example=1),
     *             @OA\Property(property="price", type="number", example=15000000),
     *             @OA\Property(property="description", type="string", example="High performance laptop")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product created successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
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
            $validated = $request->validate([
                'product_name' => 'required|string|max:255',
                'product_code' => 'required|string|max:255|unique:products,product_code',
                'category_id' => 'required|exists:product_categories,id',
                'unit_id' => 'required|exists:product_units,id',
                'description' => 'nullable|string',
            ]);

            $product = Product::create($validated);
            $product->load(['category', 'unit']);

            // Log activity
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'create',
                'module' => 'product',
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'data' => $product
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product.',
                'errors' => null
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products/{id}",
     *     summary="Get product by ID",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product retrieved successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $product = Product::with(['category', 'unit'])->find($id);
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found.',
                    'errors' => null
                ], 404);
            }

            // Log activity
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'show',
                'module' => 'product',
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product retrieved successfully.',
                'data' => $product
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve product.',
                'errors' => null
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/products/{id}",
     *     summary="Update product",
     *     tags={"Products"},
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
     *             @OA\Property(property="name", type="string", example="Laptop ASUS Updated"),
     *             @OA\Property(property="code", type="string", example="LAP001"),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="unit_id", type="integer", example=1),
     *             @OA\Property(property="price", type="number", example=16000000),
     *             @OA\Property(property="description", type="string", example="Updated description")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product updated successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
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
            $product = Product::find($id);
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found.',
                    'errors' => null
                ], 404);
            }

            $validated = $request->validate([
                'product_name' => 'required|string|max:255',
                'product_code' => 'required|string|max:255|unique:products,product_code,' . $product->id,
                'category_id' => 'required|exists:product_categories,id',
                'unit_id' => 'required|exists:product_units,id',
                'description' => 'nullable|string',
            ]);

            $product->update($validated);
            $product->load(['category', 'unit']);

            // Log activity
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'update',
                'module' => 'product',
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'data' => $product
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product.',
                'errors' => null
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/products/{id}",
     *     summary="Delete product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Cannot delete product with related data"
     *     )
     * )
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $product = Product::find($id);
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found.',
                    'errors' => null
                ], 404);
            }

            // Check if product has related data (mutations, product_locations, etc.)
            if ($product->mutations()->exists() || $product->locations()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete product. Product has related data.',
                    'errors' => null
                ], 422);
            }

            $productName = $product->product_name;
            $product->delete();

            // Log activity
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'delete',
                'module' => 'product',
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product.',
                'errors' => null
            ], 500);
        }
    }
} 
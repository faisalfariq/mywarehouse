<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\AppLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="D. Locations",
 *     description="API Endpoints for Location management"
 * )
 */
class LocationApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/locations",
     *     summary="Get all locations",
     *     tags={"Locations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search locations by name or address",
     *         required=false,
     *         @OA\Schema(type="string")
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
     *         description="Locations retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Locations retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Location")),
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
            $query = Location::query();
            
            if ($request->has('search') && $request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('address', 'like', '%' . $request->search . '%');
                });
            }
            
            $perPage = $request->get('per_page', 10);
            $locations = $query->paginate($perPage);
            
            // Log activity
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'view',
                'module' => 'location',
                'ip_address' => $request->ip(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Locations retrieved successfully.',
                'data' => $locations
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve locations.',
                'errors' => null
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/locations",
     *     summary="Create a new location",
     *     tags={"Locations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","address"},
     *             @OA\Property(property="name", type="string", example="Warehouse A"),
     *             @OA\Property(property="address", type="string", example="Jl. Sudirman No. 123, Jakarta"),
     *             @OA\Property(property="description", type="string", example="Main warehouse for electronics")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Location created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Location created successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Location")
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
                'location_code' => 'required|string|max:255|unique:locations,location_code',
                'location_name' => 'required|string|max:255',
                'address' => 'nullable|string',
                'description' => 'nullable|string',
            ]);

            $location = Location::create($validated);

            // Log activity
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'create',
                'module' => 'location',
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Location created successfully.',
                'data' => $location
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create location.',
                'errors' => null
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/locations/{id}",
     *     summary="Get location by ID",
     *     tags={"Locations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Location retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Location retrieved successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Location")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Location not found"
     *     )
     * )
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $location = Location::find($id);
            
            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found.',
                    'errors' => null
                ], 404);
            }

            // Log activity
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'show',
                'module' => 'location',
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Location retrieved successfully.',
                'data' => $location
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve location.',
                'errors' => null
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/locations/{id}",
     *     summary="Update location",
     *     tags={"Locations"},
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
     *             @OA\Property(property="name", type="string", example="Warehouse A Updated"),
     *             @OA\Property(property="address", type="string", example="Jl. Sudirman No. 456, Jakarta"),
     *             @OA\Property(property="description", type="string", example="Updated description")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Location updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Location updated successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Location")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Location not found"
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
            $location = Location::find($id);
            
            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found.',
                    'errors' => null
                ], 404);
            }

            $validated = $request->validate([
                'location_code' => 'required|string|max:255|unique:locations,location_code,' . $location->id,
                'location_name' => 'required|string|max:255',
                'address' => 'nullable|string',
                'description' => 'nullable|string',
            ]);

            $location->update($validated);

            // Log activity
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'update',
                'module' => 'location',
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Location updated successfully.',
                'data' => $location
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update location.',
                'errors' => null
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/locations/{id}",
     *     summary="Delete location",
     *     tags={"Locations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Location deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Location deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Location not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Cannot delete location with related data"
     *     )
     * )
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $location = Location::find($id);
            
            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found.',
                    'errors' => null
                ], 404);
            }

            // Check if location has related data (mutations, product_locations, etc.)
            if ($location->mutations()->exists() || $location->products()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete location. Location has related data.',
                    'errors' => null
                ], 422);
            }

            $locationName = $location->location_name;
            $location->delete();

            // Log activity
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'delete',
                'module' => 'location',
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Location deleted successfully.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete location.',
                'errors' => null
            ], 500);
        }
    }
} 
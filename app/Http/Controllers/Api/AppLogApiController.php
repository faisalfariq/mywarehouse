<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="C. AppLogs",
 *     description="API Endpoints for Application Log management"
 * )
 */
class AppLogApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/app-logs",
     *     summary="Get all application logs",
     *     tags={"AppLogs"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search logs by activity",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="Filter by user ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         description="Filter from date (Y-m-d)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         description="Filter to date (Y-m-d)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
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
     *         description="Logs retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Logs retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AppLog")),
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
            $query = AppLog::with(['user']);
            
            if ($request->has('search') && $request->search) {
                $query->where('action', 'like', '%' . $request->search . '%');
            }
            
            if ($request->has('user_id') && $request->user_id) {
                $query->where('user_id', $request->user_id);
            }
            
            if ($request->has('date_from') && $request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            $perPage = $request->get('per_page', 10);
            $logs = $query->orderBy('created_at', 'desc')->paginate($perPage);
            
            // Log activity
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'view',
                'module' => 'app_log',
                'ip_address' => $request->ip(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Logs retrieved successfully.',
                'data' => $logs
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve logs.',
                'errors' => null
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/app-logs/{id}",
     *     summary="Get log by ID",
     *     tags={"AppLogs"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Log retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Log retrieved successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/AppLog")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Log not found"
     *     )
     * )
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $log = AppLog::with(['user'])->find($id);
            
            if (!$log) {
                return response()->json([
                    'success' => false,
                    'message' => 'Log not found.',
                    'errors' => null
                ], 404);
            }

            // Log activity
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'show',
                'module' => 'app_log',
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Log retrieved successfully.',
                'data' => $log
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve log.',
                'errors' => null
            ], 500);
        }
    }
} 
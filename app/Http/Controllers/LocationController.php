<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Models\AppLog;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $locations = Location::with('products')
            ->when($keyword, function($query) use ($keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->where('location_code', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('location_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('address', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('description', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->latest()
            ->paginate(20);
        return view('pages.locations.index', compact('locations'));
    }

    public function create()
    {
        return view('pages.locations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_code' => 'required|string|max:255|unique:locations',
            'location_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string'
        ]);

        try {
            \DB::beginTransaction();
            $location = Location::create($validated);
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'create',
                'module' => 'location',
                'ip_address' => $request->ip(),
            ]);
            \DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Location added successfully.',
                'data' => $location
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to add location: ' . $e->getMessage()
            ]);
        }
    }

    public function show(string $id)
    {
        $location = Location::with('products')->findOrFail($id);
        AppLog::create([
            'user_id' => auth()->id(),
            'action' => 'show',
            'module' => 'location',
            'ip_address' => request()->ip(),
        ]);
        return view('pages.locations.show', compact('location'));
    }

    public function edit(string $id)
    {
        $location = Location::findOrFail($id);
        return view('pages.locations.edit', compact('location'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'location_code' => 'required|string|max:255|unique:locations,location_code,' . $id,
            'location_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string'
        ]);

        try {
            \DB::beginTransaction();
            $location = Location::findOrFail($id);
            $location->update($validated);
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'update',
                'module' => 'location',
                'ip_address' => $request->ip(),
            ]);
            \DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Location updated successfully.',
                'data' => $location
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update location: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy(string $id)
    {
        try {
            \DB::beginTransaction();
            $location = Location::findOrFail($id);
            // Cek relasi
            if ($location->mutations()->exists() || $location->products()->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot delete this location because it is already used in other data.'
                ]);
            }
            $location->delete();
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'delete',
                'module' => 'location',
                'ip_address' => request()->ip(),
            ]);
            \DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Location has been deleted successfully.'
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete location: ' . $e->getMessage()
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AppLog;
use App\Models\User;
use Illuminate\Http\Request;

class AppLogController extends Controller 
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $logs = AppLog::with('user')
            ->when($keyword, function($query) use ($keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->where('action', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('module', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('ip_address', 'LIKE', '%' . $keyword . '%')
                        ->orWhereHas('user', function($qu) use ($keyword) {
                            $qu->where('name', 'LIKE', '%' . $keyword . '%');
                        });
                });
            })
            ->latest()
            ->paginate(20);
        return view('pages.app_logs.index', compact('logs'));
    }

    public function create()
    {
        $users = User::all();
        return view('pages.app_logs.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'action' => 'required|string|max:255',
            'module' => 'required|string|max:255',
            'ip_address' => 'nullable|ip',
        ]);

        try {
            \DB::beginTransaction();
            $log = AppLog::create($validated);
            \DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Log has been added successfully.',
                'data' => $log
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to add log: ' . $e->getMessage()
            ]);
        }
    }

    public function show(string $id)
    {
        $log = AppLog::with('user')->findOrFail($id);
        return view('pages.app_logs.show', compact('log'));
    }

    public function edit(string $id)
    {
        $log = AppLog::findOrFail($id);
        $users = User::all();
        return view('pages.app_logs.edit', compact('log', 'users'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'action' => 'required|string|max:255',
            'module' => 'required|string|max:255',
            'ip_address' => 'nullable|ip',
        ]);

        try {
            \DB::beginTransaction();
            $log = AppLog::findOrFail($id);
            $log->update($validated);
            \DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Log has been updated successfully.',
                'data' => $log
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update log: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy(string $id)
    {
        try {
            \DB::beginTransaction();
            $log = AppLog::findOrFail($id);
            $log->delete();
            \DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Log has been deleted successfully.'
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete log: ' . $e->getMessage()
            ]);
        }
    }
}
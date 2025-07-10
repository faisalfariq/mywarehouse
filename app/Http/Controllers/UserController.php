<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AppLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $users = User::when($keyword, function($query) use ($keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->where('name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('email', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->latest()
            ->paginate(20);
        return view('pages.users.index', compact('users'));
    }

    public function create()
    {
        return view('pages.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);
        $validated['password'] = Hash::make($validated['password']);
        try {
            \DB::beginTransaction();
            $user = User::create($validated);
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'create',
                'module' => 'user',
                'ip_address' => $request->ip(),
            ]);
            \DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'User has been added successfully.',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to add user: ' . $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        AppLog::create([
            'user_id' => auth()->id(),
            'action' => 'show',
            'module' => 'user',
            'ip_address' => request()->ip(),
        ]);
        return view('pages.users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('pages.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);
        if ($validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        try {
            \DB::beginTransaction();
            $user->update($validated);
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'update',
                'module' => 'user',
                'ip_address' => $request->ip(),
            ]);
            \DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'User has been updated successfully.',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $user = User::findOrFail($id);
            // Cek relasi
            if ($user->mutations()->exists() || $user->appLogs()->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot delete this user because it is already used in other data.'
                ]);
            }
            $user->delete();
            AppLog::create([
                'user_id' => auth()->id(),
                'action' => 'delete',
                'module' => 'user',
                'ip_address' => request()->ip(),
            ]);
            \DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'User has been deleted successfully.'
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ]);
        }
    }
} 
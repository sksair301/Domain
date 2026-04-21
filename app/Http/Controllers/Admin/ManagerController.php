<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Manager;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $managers = Manager::with(['user', 'branch'])->get();

        return response()->json([
            'success' => true,
            'data' => $managers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone_number' => 'required|string|max:20',
            'branch_id' => 'required|exists:branches,id',
        ]);

        return DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'manager',
                'branch_id' => $request->branch_id,
            ]);

            $manager = Manager::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'branch_id' => $request->branch_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Manager created successfully',
                'data' => $manager->load('user')
            ], 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Manager $manager): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $manager->load(['user', 'branch'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Manager $manager): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $manager->user_id,
            'phone_number' => 'required|string|max:20',
            'branch_id' => 'required|exists:branches,id',
        ]);

        return DB::transaction(function () use ($request, $manager) {
            $manager->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'branch_id' => $request->branch_id,
            ]);

            if ($request->filled('password')) {
                $manager->user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            $manager->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'branch_id' => $request->branch_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Manager updated successfully',
                'data' => $manager->load('user')
            ]);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Manager $manager): JsonResponse
    {
        return DB::transaction(function () use ($manager) {
            $user = $manager->user;
            $manager->delete();
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Manager deleted successfully'
            ]);
        });
    }
}

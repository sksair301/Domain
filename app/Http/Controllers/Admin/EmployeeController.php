<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $employees = Employee::with(['user', 'branch'])->get();

        return response()->json([
            'success' => true,
            'data' => $employees
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
                'role' => 'employee',
                'branch_id' => $request->branch_id,
            ]);

            $employee = Employee::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'branch_id' => $request->branch_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Employee created successfully',
                'data' => $employee->load('user')
            ], 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $employee->load(['user', 'branch'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $employee->user_id,
            'phone_number' => 'required|string|max:20',
            'branch_id' => 'required|exists:branches,id',
        ]);

        return DB::transaction(function () use ($request, $employee) {
            $employee->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'branch_id' => $request->branch_id,
            ]);

            if ($request->filled('password')) {
                $employee->user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            $employee->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'branch_id' => $request->branch_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Employee updated successfully',
                'data' => $employee->load('user')
            ]);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee): JsonResponse
    {
        return DB::transaction(function () use ($employee) {
            $user = $employee->user;
            $employee->delete();
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Employee deleted successfully'
            ]);
        });
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $branches = Branch::withCount(['managers', 'employees', 'domains'])->get();

        return response()->json([
            'success' => true,
            'data' => $branches
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:branches,name',
        ]);

        $branch = Branch::create($request->only('name'));

        return response()->json([
            'success' => true,
            'message' => 'Branch created successfully',
            'data' => $branch
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $branch->load(['managers', 'employees', 'domains'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:branches,name,' . $branch->id,
        ]);

        $branch->update($request->only('name'));

        return response()->json([
            'success' => true,
            'message' => 'Branch updated successfully',
            'data' => $branch
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch): JsonResponse
    {
        // Check if branch has dependents
        if ($branch->managers()->count() > 0 || $branch->employees()->count() > 0 || $branch->domains()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete branch with active managers, employees, or domains.'
            ], 422);
        }

        $branch->delete();

        return response()->json([
            'success' => true,
            'message' => 'Branch deleted successfully'
        ]);
    }
}

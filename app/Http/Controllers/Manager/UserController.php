<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * List users in the manager's branch.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // If it's a manager, they can only see users in their own branch.
        // If it's a superadmin (allowed by middleware), they might see all or we still filter.
        // Usually, managers only care about their branch.
        
        $query = User::with('branch');

        if (!$user->isSuperAdmin()) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->get();

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users)
        ]);
    }
}

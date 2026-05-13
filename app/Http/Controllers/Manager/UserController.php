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
     * Display a listing of the users in the manager's branch.
     */
    public function index(Request $request): JsonResponse
    {
        $manager = $request->user();
        
        // A manager can see users in their branch
        $users = User::where('branch_id', $manager->branch_id)
            ->with('branch')
            ->get();

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users)
        ]);
    }
}

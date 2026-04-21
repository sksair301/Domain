<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Status;
use App\Http\Requests\StoreDomainRequest;
use App\Http\Requests\UpdateDomainRequest;
use Illuminate\Http\JsonResponse;

class DomainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Domain::class);

        $user = auth()->user();
        $query = Domain::query();

        if (!$user->isSuperAdmin()) {
            $query->where('branch_id', $user->branch_id);
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDomainRequest $request): JsonResponse
    {
        $this->authorize('create', Domain::class);

        $user = auth()->user();
        $data = $request->validated();
        
        if (!$user->isSuperAdmin()) {
            $data['branch_id'] = $user->branch_id;
        }

        // Set default status to 'Active'
        $activeStatus = Status::where('name', 'Active')->first();
        if ($activeStatus) {
            $data['status_id'] = $activeStatus->id;
        }

        $domain = Domain::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Domain created successfully',
            'data' => $domain,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Domain $domain): JsonResponse
    {
        $this->authorize('view', $domain);

        return response()->json([
            'success' => true,
            'data' => $domain,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDomainRequest $request, Domain $domain): JsonResponse
    {
        $this->authorize('update', $domain);

        $domain->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Domain updated successfully',
            'data' => $domain,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Domain $domain): JsonResponse
    {
        $this->authorize('delete', $domain);

        $domain->delete();

        return response()->json([
            'success' => true,
            'message' => 'Domain deleted successfully',
        ]);
    }
}

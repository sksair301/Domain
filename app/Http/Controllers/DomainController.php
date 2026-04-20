<?php

namespace App\Http\Controllers;

use App\Models\Domain;
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
        return response()->json([
            'success' => true,
            'data' => Domain::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDomainRequest $request): JsonResponse
    {
        $domain = Domain::create($request->validated());

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
        $domain->delete();

        return response()->json([
            'success' => true,
            'message' => 'Domain deleted successfully',
        ]);
    }
}

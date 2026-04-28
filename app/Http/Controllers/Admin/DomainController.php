<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
    public function index(\Illuminate\Http\Request $request): JsonResponse
    {
        $query = Domain::with(['branch', 'salesPerson', 'renewedBy', 'payments']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('system_status')) {
            $query->where('system_status', $request->system_status);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $perPage = $request->get('per_page', 15);
        $domains = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => \App\Http\Resources\DomainResource::collection($domains),
            'meta' => [
                'current_page' => $domains->currentPage(),
                'last_page' => $domains->lastPage(),
                'per_page' => $domains->perPage(),
                'total' => $domains->total(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDomainRequest $request): JsonResponse
    {
        $data = $request->validated();

        $domain = Domain::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Domain created successfully',
            'data' => new \App\Http\Resources\DomainResource($domain->load(['branch', 'salesPerson', 'renewedBy', 'payments']))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Domain $domain): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new \App\Http\Resources\DomainResource($domain->load(['branch', 'salesPerson', 'renewedBy', 'payments']))
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDomainRequest $request, Domain $domain): JsonResponse
    {
        $data = $request->validated();

        // Enforce payment-before-renewal logic
        if (isset($data['expiry_date']) && $data['expiry_date'] > $domain->expiry_date) {
            $domain->load('payments');
            $totalPaid = $domain->payments()->sum('amount');
            $balancePending = $domain->total_amount - $totalPaid;

            if ($balancePending > 0 && !$request->boolean('override_payment_check')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot renew: domain has a pending balance of ' . number_format($balancePending, 2) . '. Pass override_payment_check=true to force.',
                    'balance_pending' => $balancePending,
                ], 422);
            }
        }

        $domain->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Domain updated successfully',
            'data' => new \App\Http\Resources\DomainResource($domain->load(['branch', 'salesPerson', 'renewedBy', 'payments']))
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
            'message' => 'Domain deleted successfully'
        ]);
    }

    /**
     * Bulk update domains.
     */
    public function bulkUpdate(\App\Http\Requests\BulkUpdateDomainRequest $request): JsonResponse
    {
        $data = $request->validated();
        $domainIds = $data['domain_ids'];
        unset($data['domain_ids']); // don't try to update domain_ids column

        if (!empty($data)) {
            Domain::whereIn('id', $domainIds)->update($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Domains updated successfully',
        ]);
    }
}

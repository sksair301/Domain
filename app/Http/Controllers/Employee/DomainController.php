<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Http\Resources\DomainResource;
use App\Http\Requests\UpdateDomainRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DomainController extends Controller
{
    /**
     * Display a listing of the domains created by the employee.
     */
    public function index(Request $request): JsonResponse
    {
        $employee = $request->user();
        $query = Domain::where('sales_person_id', $employee->id)
            ->with(['branch', 'salesPerson', 'renewedBy', 'payments']);

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

        $perPage = $request->get('per_page', 15);
        $domains = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => DomainResource::collection($domains),
            'meta' => [
                'current_page' => $domains->currentPage(),
                'last_page' => $domains->lastPage(),
                'per_page' => $domains->perPage(),
                'total' => $domains->total(),
            ]
        ]);
    }

    /**
     * Display the specified domain.
     */
    public function show(Domain $domain): JsonResponse
    {
        $this->authorizeCreator($domain);

        return response()->json([
            'success' => true,
            'data' => new DomainResource($domain->load(['branch', 'salesPerson', 'renewedBy', 'payments']))
        ]);
    }

    /**
     * Update the specified domain.
     */
    public function update(UpdateDomainRequest $request, Domain $domain): JsonResponse
    {
        $this->authorizeCreator($domain);

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
            'data' => new DomainResource($domain->load(['branch', 'salesPerson', 'renewedBy', 'payments']))
        ]);
    }

    /**
     * Authorize that the domain was created by the employee.
     */
    protected function authorizeCreator(Domain $domain)
    {
        if ($domain->sales_person_id !== auth()->id()) {
            abort(403, 'Unauthorized. You can only access domains you created.');
        }
    }
}

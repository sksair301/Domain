<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Domain;
use App\Http\Resources\PaymentResource;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $manager = $request->user();
        $payments = Payment::whereHas('domain', function ($query) use ($manager) {
            $query->where('branch_id', $manager->branch_id);
        })->with(['domain', 'status'])->get();

        return response()->json([
            'success' => true,
            'data' => PaymentResource::collection($payments)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'domain_id' => 'required|exists:domains,id',
            'amount' => 'required|numeric',
            'payment_date' => 'required|date',
            'payment_status_id' => 'required|exists:payment_statuses,id',
        ]);

        $domain = Domain::findOrFail($data['domain_id']);
        if ($domain->branch_id !== $request->user()->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only add payments for domains in your branch.'
            ], 403);
        }

        $payment = Payment::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded successfully',
            'data' => new PaymentResource($payment->load(['domain', 'status']))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Payment $payment): JsonResponse
    {
        $payment->load('domain');
        if ($payment->domain->branch_id !== $request->user()->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only access payments for domains in your branch.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => new PaymentResource($payment->load(['domain', 'status']))
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment): JsonResponse
    {
        $payment->load('domain');
        if ($payment->domain->branch_id !== $request->user()->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only modify payments for domains in your branch.'
            ], 403);
        }

        $data = $request->validate([
            'domain_id' => 'sometimes|required|exists:domains,id',
            'amount' => 'sometimes|required|numeric',
            'payment_date' => 'sometimes|required|date',
            'payment_status_id' => 'sometimes|required|exists:payment_statuses,id',
        ]);

        if (isset($data['domain_id']) && $data['domain_id'] != $payment->domain_id) {
            $newDomain = Domain::findOrFail($data['domain_id']);
            if ($newDomain->branch_id !== $request->user()->branch_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. The new domain must also belong to your branch.'
                ], 403);
            }
        }

        $payment->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Payment updated successfully',
            'data' => new PaymentResource($payment->load(['domain', 'status']))
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Payment $payment): JsonResponse
    {
        $payment->load('domain');
        if ($payment->domain->branch_id !== $request->user()->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only delete payments for domains in your branch.'
            ], 403);
        }

        $payment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment deleted successfully'
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Payment;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $payments = Payment::with(['domain', 'status'])->get();

        return response()->json([
            'success' => true,
            'data' => $payments
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

        $payment = Payment::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded successfully',
            'data' => $payment->load(['domain', 'status'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $payment->load(['domain', 'status'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment): JsonResponse
    {
        $data = $request->validate([
            'domain_id' => 'sometimes|required|exists:domains,id',
            'amount' => 'sometimes|required|numeric',
            'payment_date' => 'sometimes|required|date',
            'payment_status_id' => 'sometimes|required|exists:payment_statuses,id',
        ]);

        $payment->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Payment updated successfully',
            'data' => $payment->load(['domain', 'status'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment): JsonResponse
    {
        $payment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment deleted successfully'
        ]);
    }
}

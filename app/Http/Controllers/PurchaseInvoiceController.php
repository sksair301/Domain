<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Http\Resources\PurchaseInvoiceResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class PurchaseInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // Purchase invoices are restricted to Mumbai branch users only
        $userBranch = $request->user()->branch;
        if (!$userBranch || strtolower($userBranch->name) !== 'mumbai') {
            return response()->json([
                'success' => false,
                'message' => 'Purchase invoices can only be accessed by users in the Mumbai branch.'
            ], 403);
        }

        $invoices = PurchaseInvoice::with('user')->get();

        return response()->json([
            'success' => true,
            'data' => PurchaseInvoiceResource::collection($invoices)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Purchase invoices are restricted to Mumbai branch users only
        $userBranch = $request->user()->branch;
        if (!$userBranch || strtolower($userBranch->name) !== 'mumbai') {
            return response()->json([
                'success' => false,
                'message' => 'Purchase invoices can only be managed by users in the Mumbai branch.'
            ], 403);
        }

        $data = $request->validate([
            'amount' => 'required|numeric',
            'payment_date' => 'required|date',
            'status' => 'required|string|in:Pending,Completed,Processed',
            'invoice_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            'company_name' => 'required|string',
        ]);

        $filePath = null;
        if ($request->hasFile('invoice_file')) {
            $filePath = $request->file('invoice_file')->store('purchase_invoices', 'public');
        }

        $invoice = PurchaseInvoice::create([
            'amount' => $data['amount'],
            'payment_date' => $data['payment_date'],
            'status' => $data['status'],
            'invoice_file_path' => $filePath,
            'company_name' => $data['company_name'],
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Purchase invoice recorded successfully',
            'data' => new PurchaseInvoiceResource($invoice->load('user'))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $invoice = PurchaseInvoice::find($id);
        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase invoice not found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => new PurchaseInvoiceResource($invoice)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        // Purchase invoices are restricted to Mumbai branch users only
        $userBranch = $request->user()->branch;
        if (!$userBranch || strtolower($userBranch->name) !== 'mumbai') {
            return response()->json([
                'success' => false,
                'message' => 'Purchase invoices can only be managed by users in the Mumbai branch.'
            ], 403);
        }

        $invoice = PurchaseInvoice::find($id);
        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase invoice not found'
            ], 404);
        }

        $data = $request->validate([
            'amount' => 'sometimes|required|numeric',
            'payment_date' => 'sometimes|required|date',
            'status' => 'sometimes|required|string|in:Pending,Completed,Processed',
            'invoice_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            'company_name' => 'sometimes|required|string',
        ]);

        if ($request->hasFile('invoice_file')) {
            if ($invoice->invoice_file_path) {
                Storage::disk('public')->delete($invoice->invoice_file_path);
            }
            $invoice->invoice_file_path = $request->file('invoice_file')->store('purchase_invoices', 'public');
        }

        if (isset($data['amount'])) $invoice->amount = $data['amount'];
        if (isset($data['payment_date'])) $invoice->payment_date = $data['payment_date'];
        if (isset($data['status'])) $invoice->status = $data['status'];
        if (isset($data['company_name'])) $invoice->company_name = $data['company_name'];
        
        $invoice->save();

        return response()->json([
            'success' => true,
            'message' => 'Purchase invoice updated successfully',
            'data' => new PurchaseInvoiceResource($invoice->load('user'))
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $invoice = PurchaseInvoice::find($id);
        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase invoice not found'
            ], 404);
        } 

        if ($invoice->invoice_file_path) {
            Storage::disk('public')->delete($invoice->invoice_file_path);
        }
        $purchaseInvoice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Purchase invoice deleted successfully'
        ]);
    }
}

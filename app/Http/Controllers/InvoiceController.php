<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        return response()->json(Invoice::with(['client', 'subscription'])->latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'subscription_id' => 'nullable|exists:subscriptions,id',
            'invoice_number' => 'required|string|unique:invoices',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:setup,monthly,extra',
            'status' => 'nullable|in:pending,paid,overdue,cancelled',
            'due_date' => 'required|date',
            'paid_date' => 'nullable|date',
        ]);

        $invoice = Invoice::create($validated);
        return response()->json($invoice->load(['client', 'subscription']), 201);
    }

    public function show(Invoice $invoice)
    {
        return response()->json($invoice->load(['client', 'subscription']));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'client_id' => 'sometimes|exists:clients,id',
            'subscription_id' => 'nullable|exists:subscriptions,id',
            'invoice_number' => 'sometimes|string|unique:invoices,invoice_number,' . $invoice->id,
            'amount' => 'sometimes|numeric|min:0',
            'type' => 'sometimes|in:setup,monthly,extra',
            'status' => 'nullable|in:pending,paid,overdue,cancelled',
            'due_date' => 'sometimes|date',
            'paid_date' => 'nullable|date',
        ]);

        $invoice->update($validated);
        return response()->json($invoice);
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return response()->json(null, 204);
    }

    public function markAsPaid(Invoice $invoice)
    {
        $invoice->update([
            'status' => 'paid',
            'paid_date' => now()->toDateString()
        ]);
        return response()->json($invoice);
    }
}

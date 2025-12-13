<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Tenant;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $tenants = Tenant::orderBy('name')->get();
        
        $query = Payment::with(['tenant', 'room'])
                        ->orderBy('created_at', 'desc');

        if ($request->filled('period')) {
            $period = Carbon::createFromFormat('Y-m', $request->input('period')); 
            $query->whereMonth('period_month', $period->month)
                    ->whereYear('period_month', $period->year);
        }

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->input('tenant_id'));
        }

        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', 'like', '%' . $request->input('invoice_number') . '%');
        }

        $payments = $query->paginate(15)->appends($request->query());

        return view('payments.index', compact('payments', 'tenants'));
    }

    public function create()
    {
        $tenants = Tenant::where('status', 'active')->with('room')->get();
        return view('payments.create', compact('tenants'));
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $amountClean = preg_replace('/[^0-9]/', '', $data['amount']);
        $lateFeeClean = preg_replace('/[^0-9]/', '', $data['late_fee']);
        
        $data['amount'] = (int)$amountClean;
        $data['late_fee'] = ($lateFeeClean === '') ? 0 : (int)$lateFeeClean;
        
        $request->merge($data);
        
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'payment_date' => 'required|date',
            'period_month' => 'required|date',
            'amount' => 'required|numeric|min:0', 
            'late_fee' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer,e-wallet',
            'notes' => 'nullable|string',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        $tenant = Tenant::find($validated['tenant_id']);
        $validated['room_id'] = $tenant->room_id;
        // $validated['late_fee'] = $validated['late_fee'] ?? 0;
        $validated['total'] = $validated['amount'] + $validated['late_fee'];
        $validated['status'] = 'paid';

        if ($request->hasFile('receipt_file')) {
            $validated['receipt_file'] = $request->file('receipt_file')->store('receipts', 'public');
        }

        Payment::create($validated);

        return redirect()->route('payments.index')
                        ->with('success', 'Pembayaran berhasil ditambahkan');
    }

    public function show(Payment $payment)
    {
        $payment->load(['tenant', 'room']);
        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $tenants = Tenant::where('status', 'active')->with('room')->get();
        return view('payments.edit', compact('payment', 'tenants'));
    }

    public function update(Request $request, Payment $payment)
    {
        $data = $request->all();

        $amountClean = preg_replace('/[^0-9]/', '', $data['amount']);
        $lateFeeClean = preg_replace('/[^0-9]/', '', $data['late_fee'] ?? ''); 

        $data['amount'] = (int)$amountClean;
        $data['late_fee'] = ($lateFeeClean === '') ? 0 : (int)$lateFeeClean; 
        
        $request->merge($data);

        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'payment_date' => 'required|date',
            'period_month' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'late_fee' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,paid,overdue',
            'payment_method' => 'nullable|in:cash,transfer,e-wallet',
            'notes' => 'nullable|string',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        $tenant = Tenant::find($validated['tenant_id']);
        $validated['room_id'] = $tenant->room_id;
        $validated['total'] = $validated['amount'] + $validated['late_fee']; // Penjumlahan aman

        if ($request->hasFile('receipt_file')) {
            if ($payment->receipt_file) {
                Storage::disk('public')->delete($payment->receipt_file);
            }
            $validated['receipt_file'] = $request->file('receipt_file')->store('receipts', 'public');
        }

        $payment->update($validated);

        return redirect()->route('payments.index')
                        ->with('success', 'Pembayaran berhasil diupdate');
    }

    public function destroy(Payment $payment)
    {
        if ($payment->receipt_file) {
            Storage::disk('public')->delete($payment->receipt_file);
        }

        $payment->delete();

        return redirect()->route('payments.index')
                        ->with('success', 'Pembayaran berhasil dihapus');
    }

    public function updateStatus(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,overdue'
        ]);

        $payment->update($validated);

        return redirect()->back()
                        ->with('success', 'Status pembayaran berhasil diupdate');
    }

    public function downloadReceipt(Payment $payment)
    {
        $payment->load(['tenant', 'room']);
        
        $pdf = Pdf::loadView('payments.receipt', compact('payment'));
        
        return $pdf->download('receipt-' . $payment->invoice_number . '.pdf');
    }

    public function report(Request $request)
    {
        $query = Payment::with(['tenant', 'room']);

        if ($request->filled('start_date')) {
            $query->where('payment_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('payment_date', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->orderBy('payment_date', 'desc')->get();
        $totalAmount = $payments->sum('total');

        if ($request->has('download')) {
            // $pdf = Pdf::loadView('payments.report', compact('payments', 'totalAmount'));
            $pdf = Pdf::loadView('payments.report_pdf', compact('payments', 'totalAmount'));
            return $pdf->download('payment-report-' . date('Y-m-d') . '.pdf');
        }

        return view('payments.report', compact('payments', 'totalAmount'));
    }
}
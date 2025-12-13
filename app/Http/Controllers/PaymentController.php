<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Tenant;
use App\Models\Finance;
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
            $period = Carbon::createFromFormat('Y-m', $request->period);
            $query->whereMonth('period_month', $period->month)
                    ->whereYear('period_month', $period->year);
        }

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }

        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', 'like', "%{$request->invoice_number}%");
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
        // Bersihkan format rupiah
        $request->merge([
            'amount' => (int) preg_replace('/[^0-9]/', '', $request->amount),
            'late_fee' => (int) preg_replace('/[^0-9]/', '', $request->late_fee ?? 0),
        ]);

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

        $tenant = Tenant::findOrFail($validated['tenant_id']);

        $validated['room_id'] = $tenant->room_id;
        $validated['total'] = $validated['amount'] + $validated['late_fee'];
        $validated['status'] = 'paid';

        if ($request->hasFile('receipt_file')) {
            $validated['receipt_file'] = $request->file('receipt_file')
                ->store('receipts', 'public');
        }

        $payment = Payment::create($validated);
        $payment->load(['room', 'tenant']);

        // === FINANCE AUTO CREATE ===
        Finance::create([
            'type' => 'income',
            'category' => 'Pembayaran Sewa',
            'transaction_date' => $payment->payment_date,
            'amount' => $payment->total,
            'description' =>
                'Pembayaran Sewa ' .
                Carbon::parse($payment->period_month)->translatedFormat('F Y') .
                ' - Kamar ' . $payment->room->room_number .
                ' (' . $payment->tenant->name . ')',
            'notes' => 'Dicatat otomatis dari pembayaran',
            'payment_id' => $payment->id,
        ]);

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
        $request->merge([
            'amount' => (int) preg_replace('/[^0-9]/', '', $request->amount),
            'late_fee' => (int) preg_replace('/[^0-9]/', '', $request->late_fee ?? 0),
        ]);

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

        $tenant = Tenant::findOrFail($validated['tenant_id']);

        $validated['room_id'] = $tenant->room_id;
        $validated['total'] = $validated['amount'] + $validated['late_fee'];

        if ($request->hasFile('receipt_file')) {
            if ($payment->receipt_file) {
                Storage::disk('public')->delete($payment->receipt_file);
            }
            $validated['receipt_file'] = $request->file('receipt_file')
                ->store('receipts', 'public');
        }

        $oldStatus = $payment->status;

        $payment->update($validated);
        $payment->load(['room', 'tenant']);

        $finance = Finance::where('payment_id', $payment->id)->first();

        if ($validated['status'] === 'paid') {
            $description =
                'Pembayaran Sewa ' .
                Carbon::parse($payment->period_month)->translatedFormat('F Y') .
                ' - Kamar ' . $payment->room->room_number .
                ' (' . $payment->tenant->name . ')';

            if ($finance) {
                $finance->update([
                    'transaction_date' => $payment->payment_date,
                    'amount' => $payment->total,
                    'description' => $description,
                ]);
            } else {
                Finance::create([
                    'type' => 'income',
                    'category' => 'Pembayaran Sewa',
                    'transaction_date' => $payment->payment_date,
                    'amount' => $payment->total,
                    'description' => $description,
                    'notes' => 'Dicatat otomatis dari update pembayaran',
                    'payment_id' => $payment->id,
                ]);
            }
        }

        if ($oldStatus === 'paid' && $validated['status'] !== 'paid' && $finance) {
            $finance->delete();
        }

        return redirect()->route('payments.index')
            ->with('success', 'Pembayaran berhasil diupdate');
    }

    public function destroy(Payment $payment)
    {
        $finance = Finance::where('payment_id', $payment->id)->first();
        if ($finance) {
            $finance->delete();
        }

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

        $oldStatus = $payment->status;
        $payment->update($validated);
        $payment->load(['room', 'tenant']);

        $finance = Finance::where('payment_id', $payment->id)->first();

        if ($validated['status'] === 'paid' && !$finance) {
            Finance::create([
                'type' => 'income',
                'category' => 'Pembayaran Sewa',
                'transaction_date' => $payment->payment_date,
                'amount' => $payment->total,
                'description' =>
                    'Pembayaran Sewa ' .
                    Carbon::parse($payment->period_month)->translatedFormat('F Y') .
                    ' - Kamar ' . $payment->room->room_number .
                    ' (' . $payment->tenant->name . ')',
                'notes' => 'Dicatat dari update status',
                'payment_id' => $payment->id,
            ]);
        }

        if ($oldStatus === 'paid' && $validated['status'] !== 'paid' && $finance) {
            $finance->delete();
        }

        return back()->with('success', 'Status pembayaran berhasil diperbarui');
    }

    public function downloadReceipt(Payment $payment)
    {
        $payment->load(['tenant', 'room']);
        $pdf = Pdf::loadView('payments.receipt', compact('payment'));
        return $pdf->download('receipt-' . $payment->invoice_number . '.pdf');
    }
}

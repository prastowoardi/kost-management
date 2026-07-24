<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Models\Payment;
use App\Models\Tenant;
use App\Services\PaymentService;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function __construct(
        private WhatsAppService $whatsapp,
        private PaymentService $paymentService,
    ) {}

    public function index(Request $request)
    {
        $tenants = Tenant::orderBy('name')->get();

        $query = Payment::with(['tenant', 'room'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('filter_month')) {
            $query->whereMonth('payment_date', $request->filter_month);
        }

        if ($request->filled('filter_year')) {
            $query->whereYear('period_month', $request->filter_year);
        }

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
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
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $tenant = Tenant::findOrFail($validated['tenant_id']);
        $validated['room_id'] = $tenant->room_id;
        $validated['total'] = $validated['amount'] + $validated['late_fee'];
        $validated['status'] = 'paid';

        if ($request->hasFile('receipt_file')) {
            $validated['receipt_file'] = $request->file('receipt_file')->store('receipts', 'public');
        }

        $payment = Payment::create($validated);
        $payment->load(['room', 'tenant']);

        NotificationHelper::create(
            'bayar_masuk',
            'Pembayaran Masuk: '.$payment->invoice_number,
            $payment->tenant->name.' — Rp '.number_format($payment->total, 0, ',', '.'),
            route('payments.index')
        );

        $this->paymentService->createFinanceRecord($payment);
        $this->sendWhatsAppReceipt($payment);

        return redirect()->route('payments.index')->with('success', 'Pembayaran dicatat & Kwitansi dikirim!');
    }

    private function sendWhatsAppReceipt($payment)
    {
        $tenant = $payment->tenant;
        if (! $tenant || ! $tenant->phone) {
            return;
        }

        $htmlContent = view('payments.receipt', compact('payment'))->render();
        $caption = $this->whatsapp->getPaymentReceiptCaption($tenant->name);

        $success = $this->whatsapp->sendImage($tenant->phone, $htmlContent, $caption);

        \App\Models\BroadcastLog::create([
            'broadcast_id' => null,
            'tenant_name' => $tenant->name,
            'phone' => $tenant->phone,
            'status' => $success ? 'success' : 'failed',
            'error_message' => $success ? null : 'Gagal kirim gambar WA',
        ]);
    }

    public function sendGatewayWA(Payment $payment)
    {
        $tenantName = $payment->tenant->name;
        $period = Carbon::parse($payment->period_month)->translatedFormat('F Y');
        $invoice = $payment->invoice_number;
        $total = $payment->total;

        $message = $this->whatsapp->getPaymentConfirmationMessage($tenantName, $period, $invoice, $total);
        $html = view('payments.receipt', compact('payment'))->render();

        $success = $this->whatsapp->sendImage($payment->tenant->phone, $html, $message);

        if ($success) {
            return response()->json([
                'status' => 'success',
                'message' => 'Kwitansi dan pesan berhasil dikirim!',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Gateway gagal mengirim pesan.',
        ], 500);
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
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
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

        $payment->update($validated);
        $payment->load(['room', 'tenant']);

        $this->paymentService->syncFinanceRecord($payment);

        return redirect()->route('payments.index')
            ->with('success', 'Pembayaran berhasil diupdate');
    }

    public function destroy(Payment $payment)
    {
        $this->paymentService->deleteFinanceRecord($payment);

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
            'status' => 'required|in:pending,paid,overdue',
        ]);

        $payment->update($validated);
        $payment->load(['room', 'tenant']);

        $this->paymentService->syncFinanceRecord($payment);

        return back()->with('success', 'Status pembayaran berhasil diperbarui');
    }

    public function downloadReceipt(Payment $payment)
    {
        $payment->load(['tenant', 'room']);
        $pdf = Pdf::loadView('payments.receipt', compact('payment'));

        return $pdf->download('receipt-'.$payment->invoice_number.'.pdf');
    }

    public function receipt(Payment $payment)
    {
        $payment->load(['tenant', 'room']);

        return view('payments.receipt', compact('payment'));
    }

    public function upload(Request $request, $hash)
    {
        $id = decrypt($hash);
        $payment = Payment::findOrFail($id);

        $request->validate([
            'proof' => 'required|image|max:2048',
        ]);

        if ($request->hasFile('proof')) {
            $file = $request->file('proof');
            $filename = 'proof_'.$id.'_'.time().'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/proofs', $filename);

            $payment->update([
                'proof_of_payment' => $filename,
                'status' => 'pending',
            ]);

            return redirect()->route('public.pay.success', $hash);
        }
    }
}

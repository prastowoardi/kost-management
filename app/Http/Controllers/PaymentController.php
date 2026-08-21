<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Helpers\NotificationHelper;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
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

    /**
     * Normalisasi periode dari form ("2026-08") menjadi tanggal pertama
     * bulan tersebut ("2026-08-01") agar perbandingan duplikat akurat.
     */
    private function normalizePeriod(mixed $value): string
    {
        return Carbon::parse($value)->startOfMonth()->toDateString();
    }

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

    public function store(StorePaymentRequest $request)
    {
        $validated = $request->validated();

        $validated['period_month'] = $this->normalizePeriod($validated['period_month']);

        $tenant = Tenant::findOrFail($validated['tenant_id']);

        if ($request->hasFile('receipt_file')) {
            $validated['receipt_file'] = $request->file('receipt_file')->store('receipts');
        }

        // Mendukung cicilan: validasi sisa tagihan & auto-note ada di service
        $payment = $this->paymentService->createInstallmentPayment($tenant, $validated);

        $payment->load(['room', 'tenant']);

        NotificationHelper::create(
            'bayar_masuk',
            'Pembayaran Masuk: '.$payment->invoice_number,
            $payment->tenant->name.' — Rp '.number_format($payment->total, 0, ',', '.'),
            route('payments.show', $payment)
        );

        $this->paymentService->createFinanceRecord($payment);
        $this->sendWhatsAppReceipt($payment);

        LogHelper::log('CREATE_PAYMENT', "Mencatat pembayaran {$payment->invoice_number} untuk {$payment->tenant->name}", $payment);

        return redirect()->route('payments.index')->with('success', 'Pembayaran dicatat & Kwitansi dikirim!');
    }

    private function sendWhatsAppReceipt($payment)
    {
        if (! $payment->tenant?->phone) {
            return;
        }

        // Render + kirim kwitansi berat (Puppeteer), jalankan di queue
        \App\Jobs\SendWhatsAppReceiptJob::dispatch($payment->id);
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

    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        $validated = $request->validated();

        $validated['period_month'] = $this->normalizePeriod($validated['period_month']);

        $tenant = Tenant::findOrFail($validated['tenant_id']);

        $validated['room_id'] = $tenant->room_id;
        $validated['total'] = $validated['amount'] + $validated['late_fee'];

        if ($request->hasFile('receipt_file')) {
            if ($payment->receipt_file) {
                Storage::delete($payment->receipt_file);
            }
            $validated['receipt_file'] = $request->file('receipt_file')
                ->store('receipts');
        }

        $before = $payment->toArray();
        $payment->update($validated);
        $payment->load(['room', 'tenant']);
        $after = $payment->fresh()->toArray();

        $this->paymentService->syncFinanceRecord($payment);

        LogHelper::log('UPDATE_PAYMENT', "Mengubah pembayaran {$payment->invoice_number}", $payment, [
            'before' => $before,
            'after' => $after,
        ]);

        return redirect()->route('payments.index')
            ->with('success', 'Pembayaran berhasil diupdate');
    }

    public function destroy(Payment $payment)
    {
        $deletedData = $payment->toArray();

        $this->paymentService->deleteFinanceRecord($payment);

        if ($payment->receipt_file) {
            Storage::delete($payment->receipt_file);
        }

        $payment->delete();

        LogHelper::log('DELETE_PAYMENT', "Menghapus pembayaran {$deletedData['invoice_number']}", null, [
            'deleted' => $deletedData,
        ]);

        return redirect()->route('payments.index')
            ->with('success', 'Pembayaran berhasil dihapus');
    }

    public function updateStatus(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,overdue',
        ]);

        $before = $payment->toArray();
        $payment->update($validated);
        $payment->load(['room', 'tenant']);
        $after = $payment->fresh()->toArray();

        $this->paymentService->syncFinanceRecord($payment);

        LogHelper::log('UPDATE_PAYMENT_STATUS', "Mengubah status pembayaran {$payment->invoice_number} dari {$before['status']} ke {$after['status']}", $payment, [
            'before' => $before,
            'after' => $after,
        ]);

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
}

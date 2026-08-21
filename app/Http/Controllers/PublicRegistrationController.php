<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Services\TenantRegistrationService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PublicRegistrationController extends Controller
{
    public function __construct(
        private WhatsAppService $whatsapp,
        private TenantRegistrationService $registration,
    ) {}

    public function index()
    {
        $availableRooms = Room::where('status', 'available')->get();

        return view('public.register', compact('availableRooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('tenants')->whereNull('deleted_at')],
            'phone' => 'required|string|max:20',
            'id_card' => ['required', 'string', Rule::unique('tenants')->whereNull('deleted_at')],
            'address' => 'required|string',
            'entry_date' => 'required|date',
            'payment_method' => 'required|in:transfer,cash',
            'photo' => 'required|image|max:5120',
            'receipt_file' => 'nullable|image|max:5120',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('tenants');
        }

        $receiptPath = null;
        if ($request->hasFile('receipt_file')) {
            $receiptPath = $request->file('receipt_file')->store('receipts');
        }

        $validated['status'] = 'active';

        try {
            $tenant = $this->registration->registerWithPayment($validated, [
                'payment_method' => $request->payment_method,
                'receipt_file' => $receiptPath,
            ]);
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['room_id' => $e->getMessage()]);
        }

        $this->sendWelcomeMessage($tenant);

        return redirect()->route('public.register.success');
    }

    private function sendWelcomeMessage($tenant)
    {
        $messages = $this->whatsapp->getRegistrationMessages(
            $tenant->name,
            $tenant->room->room_number,
            date('d M Y', strtotime($tenant->entry_date)),
            $tenant->room->price
        );

        // Kirim via queue agar registrasi publik tidak menunggu gateway
        foreach ($messages as $message) {
            \App\Jobs\SendWhatsAppMessageJob::dispatch($tenant->phone, $message);
        }
    }

    public function success()
    {
        return view('public.register_success');
    }
}

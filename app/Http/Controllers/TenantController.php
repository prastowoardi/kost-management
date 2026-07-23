<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Room;
use App\Models\Tenant;
use App\Services\TenantRegistrationService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    public function __construct(
        private WhatsAppService $whatsapp,
        private TenantRegistrationService $registration,
    ) {}

    public function index()
    {
        $tenants = Tenant::with('room')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tenants.index', compact('tenants'));
    }

    public function create()
    {
        $availableRooms = Room::where('status', 'available')->get();

        return view('tenants.create', compact('availableRooms'));
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
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('tenants', 'public');
        }

        $tenant = $this->registration->registerWithUser($validated);

        LogHelper::log('CREATE_TENANT', "Menambah penghuni {$tenant->name}", $tenant);

        $message = $this->whatsapp->getWelcomeMessage($tenant->name);
        $waUrl = 'https://wa.me/'.$tenant->phone.'?text='.urlencode($message);

        $this->whatsapp->sendMessage($tenant->phone, $message);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'message' => 'Penghuni dan Akun Login berhasil dibuat.',
                'data' => $tenant->load('user'),
            ], 201);
        }

        return redirect()->route('tenants.index')
            ->with('success', 'Penghuni berhasil ditambahkan. Password default: password123')
            ->with('wa_url', $waUrl);
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['room', 'payments', 'complaints']);

        return view('tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        $rooms = Room::where('status', 'available')
            ->orWhere('id', $tenant->room_id)
            ->get();

        return view('tenants.edit', compact('tenant', 'rooms'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('tenants')->ignore($tenant->id)->whereNull('deleted_at')],
            'phone' => 'required|string|max:20',
            'id_card' => ['required', 'string', Rule::unique('tenants')->ignore($tenant->id)->whereNull('deleted_at')],
            'address' => 'required|string',
            'entry_date' => 'required|date',
            'exit_date' => 'nullable|date',
            'status' => 'required|in:active,inactive',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $oldRoomId = $tenant->room_id;
        $before = $tenant->toArray();

        if ($request->hasFile('photo')) {
            if ($tenant->photo) {
                Storage::disk('public')->delete($tenant->photo);
            }
            $validated['photo'] = $request->file('photo')->store('tenants', 'public');
        }

        $tenant->update($validated);
        $after = $tenant->fresh()->toArray();

        if ($tenant->status == 'inactive') {
            Room::where('id', $tenant->room_id)->update(['status' => 'available']);
        } else {
            if ($oldRoomId != $validated['room_id']) {
                Room::where('id', $oldRoomId)->update(['status' => 'available']);
            }
            Room::where('id', $validated['room_id'])->update(['status' => 'occupied']);
        }

        LogHelper::log('UPDATE_TENANT', "Mengubah data penghuni {$tenant->name}", $tenant, [
            'before' => $before,
            'after' => $after,
        ]);

        return redirect()->route('tenants.index')
            ->with('success', 'Data penghuni berhasil diupdate');
    }

    public function destroy(Tenant $tenant)
    {
        $deletedData = $tenant->toArray();

        if ($tenant->photo) {
            Storage::disk('public')->delete($tenant->photo);
        }

        Room::find($tenant->room_id)->update(['status' => 'available']);

        $tenant->delete();

        LogHelper::log('DELETE_TENANT', "Menghapus penghuni {$deletedData['name']}", null, [
            'deleted' => $deletedData,
        ]);

        return redirect()->route('tenants.index')
            ->with('success', 'Penghuni berhasil dihapus');
    }

    public function updateStatus(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive',
            'exit_date' => 'required_if:status,inactive|nullable|date',
        ]);

        $before = $tenant->toArray();
        $tenant->update($validated);
        $after = $tenant->fresh()->toArray();

        LogHelper::log('UPDATE_TENANT_STATUS', "Mengubah status penghuni {$tenant->name} dari {$before['status']} ke {$after['status']}", $tenant, [
            'before' => $before,
            'after' => $after,
        ]);

        if ($validated['status'] == 'inactive') {
            Room::find($tenant->room_id)->update(['status' => 'available']);
        }

        return redirect()->back()
            ->with('success', 'Status penghuni berhasil diupdate');
    }
}

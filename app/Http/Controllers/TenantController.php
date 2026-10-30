<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Room;
use App\Models\Tenant;
use App\Services\TenantRegistrationService;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class TenantController extends Controller
{
    public function __construct(
        private WhatsAppService $whatsapp,
        private TenantRegistrationService $registration,
    ) {}

    public function index(Request $request)
    {
        $tenants = Tenant::with('room')
            ->when($request->search, fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('phone', 'like', '%'.$request->search.'%');
            }))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->room_id, fn ($q) => $q->where('room_id', $request->room_id))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $rooms = Room::orderBy('room_number')->get();

        return view('tenants.index', compact('tenants', 'rooms'));
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
            $validated['photo'] = $request->file('photo')->store('tenants', 'private');
        }

        try {
            $tenant = $this->registration->registerWithUser($validated);
        } catch (\InvalidArgumentException $e) {
            LogHelper::logError(
                'CREATE_TENANT_FAILED',
                'Gagal mendaftarkan tenant: '.$e->getMessage(),
                $e
            );

            return back()->withInput()->withErrors(['room_id' => $e->getMessage()]);
        } catch (Throwable $e) {
            LogHelper::logError('CREATE_TENANT_FAILED', 'Gagal mendaftarkan tenant', $e);

            return back()->withInput()->withErrors(['room_id' => 'Gagal mendaftarkan tenant. Periksa kembali input.']);
        }

        LogHelper::log('CREATE_TENANT', "Menambah penghuni {$tenant->name}", $tenant);

        $message = $this->whatsapp->getWelcomeMessage($tenant->name);
        $waUrl = 'https://wa.me/'.$tenant->phone.'?text='.urlencode($message);

        \App\Jobs\SendWhatsAppMessageJob::dispatch($tenant->phone, $message);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'message' => 'Penghuni dan Akun Login berhasil dibuat.',
                'data' => $tenant->load('user'),
            ], 201);
        }

        $pwdMsg = ! empty($tenant->initial_password)
            ? " Akun login: {$tenant->email} (Password: {$tenant->initial_password})"
            : '';

        return redirect()->route('tenants.index')
            ->with('success', 'Penghuni berhasil ditambahkan.'.$pwdMsg)
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
        try {
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
                    Storage::disk('private')->delete($tenant->photo);
                    Storage::disk('local')->delete($tenant->photo);
                }
                $validated['photo'] = $request->file('photo')->store('tenants', 'private');
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
        } catch (Throwable $e) {
            LogHelper::logError('UPDATE_TENANT_FAILED', "Gagal update tenant #{$tenant->id}", $e);

            return back()->with('error', 'Gagal mengupdate data penghuni')->withInput();
        }
    }

    public function destroy(Tenant $tenant)
    {
        try {
            $deletedData = $tenant->toArray();

            if ($tenant->photo) {
                Storage::disk('private')->delete($tenant->photo);
                Storage::disk('local')->delete($tenant->photo);
            }

            Room::find($tenant->room_id)->update(['status' => 'available']);

            $tenant->delete();

            LogHelper::log('DELETE_TENANT', "Menghapus penghuni {$deletedData['name']}", null, [
                'deleted' => $deletedData,
            ]);

            return redirect()->route('tenants.index')
                ->with('success', 'Penghuni berhasil dihapus');
        } catch (Throwable $e) {
            LogHelper::logError('DELETE_TENANT_FAILED', "Gagal hapus tenant #{$tenant->id}", $e);

            return back()->with('error', 'Gagal menghapus penghuni');
        }
    }

    public function updateStatus(Request $request, Tenant $tenant)
    {
        try {
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
        } catch (Throwable $e) {
            LogHelper::logError('UPDATE_TENANT_STATUS_FAILED', "Gagal update status tenant #{$tenant->id}", $e);

            return back()->with('error', 'Gagal memperbarui status penghuni');
        }
    }

    /**
     * Akses aman foto / berkas identitas KTP penghuni.
     * Hanya admin, staff, atau penyewa bersangkutan yang diizinkan (UU PDP).
     */
    public function photo(Tenant $tenant)
    {
        $user = auth()->user();
        if (! $user) {
            abort(401);
        }

        if (! $user->isAdmin() && ! $user->isStaff() && $user->id !== $tenant->user_id) {
            abort(403, 'Akses data identitas tidak diizinkan.');
        }

        if (! $tenant->photo) {
            abort(404);
        }

        $disk = null;
        foreach (['private', 'local', 'public'] as $candidateDisk) {
            if (Storage::disk($candidateDisk)->exists($tenant->photo)) {
                $disk = $candidateDisk;
                break;
            }
        }

        if (! $disk) {
            abort(404);
        }

        return Storage::disk($disk)->response($tenant->photo);
    }

    /**
     * Unduh Surat Perjanjian Sewa Kos (SPK / Kontrak) Digital.
     */
    public function contractPdf(Tenant $tenant)
    {
        $tenant->loadMissing(['room', 'user']);
        $pdf = Pdf::loadView('tenants.contract_pdf', compact('tenant'));
        $safeName = Str::slug($tenant->name);
        $roomNumber = $tenant->room->room_number ?? 'kamar';

        return $pdf->download("SPK-{$roomNumber}-{$safeName}.pdf");
    }
}

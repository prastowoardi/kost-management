<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsAppMessageJob;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use App\Services\TenantRegistrationService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AdminTenantController extends Controller
{
    public function __construct(
        private TenantRegistrationService $registration,
        private WhatsAppService $whatsapp,
    ) {}

    public function index()
    {
        return \App\Models\Tenant::with(['user', 'room'])->get();
    }

    public function activeTenants()
    {
        $tenants = \App\Models\Tenant::with(['user', 'room'])
            ->with(['payments' => function ($q) {
                $q->whereNull('deleted_at')
                    ->where('status', 'paid')
                    ->select('id', 'tenant_id', 'period_month', 'status', 'total', 'amount');
            }])
            ->where('status', 'active')
            ->get()
            ->map(function (\App\Models\Tenant $tenant) {
                $paidMonths = $tenant->payments
                    ->pluck('period_month')
                    ->filter()
                    ->map(fn ($d) => \Carbon\Carbon::parse($d)->format('Y-m'))
                    ->unique()
                    ->values()
                    ->all();

                $lastPaidDate = $tenant->payments
                    ->pluck('period_month')
                    ->filter()
                    ->map(fn ($d) => \Carbon\Carbon::parse($d)->startOfMonth())
                    ->max();

                $lastPaidPeriod = $lastPaidDate ? $lastPaidDate->format('Y-m') : null;

                if ($lastPaidDate) {
                    $nextPeriod = $lastPaidDate->copy()->addMonth()->startOfMonth()->format('Y-m');
                } else {
                    $nextPeriod = $tenant->entry_date
                        ? \Carbon\Carbon::parse($tenant->entry_date)->startOfMonth()->format('Y-m')
                        : now()->format('Y-m');
                }

                $tenant->setAttribute('paid_periods', $paidMonths);
                $tenant->setAttribute('last_paid_period', $lastPaidPeriod);
                $tenant->setAttribute('next_period', $nextPeriod);

                return $tenant;
            });

        return response()->json([
            'status' => 'success',
            'data' => $tenants,
        ]);
    }

    public function allRooms()
    {
        return Room::all();
    }

    public function availableRooms()
    {
        return Room::where('status', 'available')->get();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'room_id' => 'required|string',
            'phone' => 'required',
            'id_card' => 'required',
            'password' => 'required|min:8',
            'address' => 'nullable|string',
            'entry_date' => 'nullable|date',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        return DB::transaction(function () use ($request) {
            try {
                $room = Room::where(is_numeric($request->room_id) ? 'id' : 'uuid', $request->room_id)->firstOrFail();

                $photoPath = null;
                if ($request->hasFile('photo')) {
                    $photoPath = $request->file('photo')->store('tenants', 'private');
                }

                $tenant = $this->registration->registerWithUser([
                    'room_id' => $room->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'id_card' => $request->id_card,
                    'address' => $request->address ?? '',
                    'entry_date' => $request->entry_date ?? now(),
                    'emergency_contact_name' => $request->emergency_contact_name ?? '',
                    'emergency_contact_phone' => $request->emergency_contact_phone ?? '',
                    'photo' => $photoPath,
                    'status' => 'active',
                ], $request->password);

                LogHelper::log(
                    'CREATE_TENANT',
                    "Mendaftarkan tenant: {$request->name} di Kamar ".$room->room_number,
                    $tenant->user
                );

                $message = $this->whatsapp->getWelcomeMessage($tenant->name);

                $cleanPhone = preg_replace('/[^0-9]/', '', (string) $tenant->phone);
                $waPhone = str_starts_with($cleanPhone, '0') ? '62'.substr($cleanPhone, 1) : $cleanPhone;
                $waUrl = 'https://wa.me/'.$waPhone.'?text='.urlencode($message);

                if (! empty($tenant->phone)) {
                    try {
                        SendWhatsAppMessageJob::dispatch($tenant->phone, $message);
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning('SendWhatsAppMessageJob dispatch error: '.$e->getMessage());
                    }
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Tenant berhasil didaftarkan!',
                    'data' => $tenant->fresh()->load(['user', 'room']),
                    'wa_url' => $waUrl,
                    'message_text' => $message,
                ]);

            } catch (\InvalidArgumentException $e) {
                LogHelper::logError(
                    'CREATE_TENANT_FAILED',
                    'Gagal simpan tenant: '.$e->getMessage(),
                    $e
                );

                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ], 422);
            } catch (\Exception $e) {
                LogHelper::logError('CREATE_TENANT_FAILED', 'Gagal simpan tenant', $e);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan data tenant. Periksa kembali input Anda.',
                ], 500);
            }
        });
    }

    public function payments($uuid)
    {
        $tenant = Tenant::where(is_numeric($uuid) ? 'id' : 'uuid', $uuid)->firstOrFail();

        $payments = Payment::where('tenant_id', $tenant->id)
            ->with(['room'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $payments,
        ]);
    }

    public function destroy($uuid)
    {
        try {
            $tenant = Tenant::where(is_numeric($uuid) ? 'id' : 'uuid', $uuid)->firstOrFail();
            $name = $tenant->name;

            $tenant->update(['status' => 'inactive']);
            $tenant->room?->update(['status' => 'available']);

            if ($tenant->photo) {
                Storage::disk('private')->delete($tenant->photo);
                Storage::disk('local')->delete($tenant->photo);
            }

            if ($tenant->user) {
                $tenant->user->tokens()->delete();
                $tenant->user->delete();
            }

            $tenant->delete();

            LogHelper::log(
                'DELETE_TENANT',
                "Menghapus tenant: {$name}",
                null,
                ['deleted' => $tenant->toArray()]
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Tenant berhasil dihapus!',
            ]);
        } catch (Throwable $e) {
            LogHelper::logError(
                'DELETE_TENANT_FAILED',
                'Gagal hapus tenant: '.$uuid,
                $e
            );

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus tenant',
            ], 500);
        }
    }

    public function update(Request $request, $uuid)
    {
        try {
            $tenant = Tenant::where(is_numeric($uuid) ? 'id' : 'uuid', $uuid)->firstOrFail();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,'.$tenant->user_id,
                'room_id' => 'required|string',
                'phone' => 'required',
                'id_card' => 'required',
                'address' => 'nullable|string',
                'entry_date' => 'nullable|date',
                'exit_date' => 'nullable|date',
                'emergency_contact_name' => 'nullable|string|max:255',
                'emergency_contact_phone' => 'nullable|string|max:20',
                'status' => 'nullable|in:active,inactive',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            ]);

            $room = Room::where(is_numeric($request->room_id) ? 'id' : 'uuid', $request->room_id)->firstOrFail();

            $updateData = [
                'room_id' => $room->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'id_card' => $validated['id_card'],
                'address' => $validated['address'] ?? $tenant->address,
                'entry_date' => $validated['entry_date'] ?? $tenant->entry_date,
                'exit_date' => $validated['exit_date'] ?? $tenant->exit_date,
                'emergency_contact_name' => $validated['emergency_contact_name'] ?? $tenant->emergency_contact_name,
                'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? $tenant->emergency_contact_phone,
                'status' => $validated['status'] ?? $tenant->status,
            ];

            if ($request->hasFile('photo')) {
                if ($tenant->photo) {
                    Storage::disk('private')->delete($tenant->photo);
                    Storage::disk('local')->delete($tenant->photo);
                }
                $updateData['photo'] = $request->file('photo')->store('tenants', 'private');
            }

            $oldRoomId = $tenant->room_id;
            $tenant->update($updateData);

            if (($validated['status'] ?? $tenant->status) === 'inactive') {
                Room::where('id', $tenant->room_id)->update(['status' => 'available']);
            } else {
                if ($oldRoomId != $room->id) {
                    Room::where('id', $oldRoomId)->update(['status' => 'available']);
                }
                Room::where('id', $room->id)->update(['status' => 'occupied']);
            }

            if ($tenant->user && $tenant->user->email !== $validated['email']) {
                $tenant->user->update(['email' => $validated['email']]);
            }

            if (!empty($request->password) && strlen($request->password) >= 6) {
                $tenant->user?->update(['password' => bcrypt($request->password)]);
            }

            LogHelper::log(
                'UPDATE_TENANT',
                "Mengupdate tenant: {$validated['name']}",
                $tenant->user
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Tenant berhasil diupdate!',
                'data' => $tenant->fresh()->load(['user', 'room']),
            ]);
        } catch (Throwable $e) {
            LogHelper::logError(
                'UPDATE_TENANT_FAILED',
                'Gagal update tenant: '.($request->name ?? $uuid),
                $e
            );

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengupdate tenant',
            ], 500);
        }
    }
}

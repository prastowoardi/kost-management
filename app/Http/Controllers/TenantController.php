<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
class TenantController extends Controller
{
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
            'email' => 'required|email|unique:tenants',
            'phone' => 'required|string|max:20',
            'id_card' => 'required|string|unique:tenants',
            'address' => 'required|string',
            'entry_date' => 'required|date',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password123'), // Password default
            'role' => 'tenant',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('tenants', 'public');
        }

        $validated['user_id'] = $user->id;
        $tenant = Tenant::create($validated);
        $tenant->room->update(['status' => 'occupied']);

        $waUrl = $this->sendWelcomeMessage($tenant);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'message' => 'Penghuni dan Akun Login berhasil dibuat.',
                'data' => $tenant->load('user')
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
            'email' => 'required|email|unique:tenants,email,' . $tenant->id,
            'phone' => 'required|string|max:20',
            'id_card' => 'required|string|unique:tenants,id_card,' . $tenant->id,
            'address' => 'required|string',
            'entry_date' => 'required|date',
            'exit_date' => 'nullable|date',
            'status' => 'required|in:active,inactive',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        $oldRoomId = $tenant->room_id;

        if ($request->hasFile('photo')) {
            if ($tenant->photo) {
                Storage::disk('public')->delete($tenant->photo);
            }
            $validated['photo'] = $request->file('photo')->store('tenants', 'public');
        }

        $tenant->update($validated);

        if ($tenant->status == 'inactive') {
            Room::where('id', $tenant->room_id)->update(['status' => 'available']);
        } else {
            if ($oldRoomId != $validated['room_id']) {
                Room::where('id', $oldRoomId)->update(['status' => 'available']);
            }
            Room::where('id', $validated['room_id'])->update(['status' => 'occupied']);
        }

        return redirect()->route('tenants.index')
                ->with('success', 'Data penghuni berhasil diupdate');
    }

    public function destroy(Tenant $tenant)
    {
        if ($tenant->photo) {
            Storage::disk('public')->delete($tenant->photo);
        }

        Room::find($tenant->room_id)->update(['status' => 'available']);
        
        $tenant->delete();

        return redirect()->route('tenants.index')
                        ->with('success', 'Penghuni berhasil dihapus');
    }

    public function updateStatus(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive',
            'exit_date' => 'required_if:status,inactive|nullable|date'
        ]);

        $tenant->update($validated);

        if ($validated['status'] == 'inactive') {
            Room::find($tenant->room_id)->update(['status' => 'available']);
        }

        return redirect()->back()
                        ->with('success', 'Status penghuni berhasil diupdate');
    }

    private function sendWelcomeMessage($tenant)
    {
        $message = "Halo {$tenant->name}! Selamat datang di Serrata Kost! 👋✨\n\n" .

                "Makasih banyak ya sudah memilih Serrata Kost jadi rumah barumu. Semoga betah, nyaman, dan produktif selama tinggal di sini! 😊\n\n" .

                "*Biar makin nyaman bareng, yuk intip 'House Rules' kita sebentar:* 📝\n\n" .

                "📍 **UMUM**\n" .
                    "1. Saling jaga ketenangan dan keamanan ya, biar istirahat makin pol.\n" .
                    "2. Kost kita bersih dari Miras, Narkoba, atau barang terlarang lainnya.\n" .
                    "3. No smoking inside! Kamar tetap wangi tanpa asap rokok ya.\n" .
                    "4. Mohon maaf, kita belum bisa terima anabul atau hewan peliharaan.\n" .
                    "5. Saling jaga etika dan hindari asusila demi kenyamanan bersama.\n" .
                    "6. Kita jaga nama baik Serrata Kost bareng-bareng ya, Kak.\n" .
                    "7. Jangan lupa pembayaran kost tepat waktu sesuai tanggal janjian.\n" .
                    "8. Khusus tamu laki-laki, cukup sampai ruang tamu/area luar ya, tidak masuk kamar.\n" .
                    "9. Kalau ada keluarga mau menginap, info ke Ibu Kost dulu ya (max 2 orang).\n\n" .

                "✨ **KEBERSIHAN & KERAPIHAN**\n" .
                    "1. Kamar dan kamar mandi sendiri dijaga tetap bersih ya, biar makin betah.\n" .
                    "2. Buang sampah di tempatnya. Tolong banget jangan buang sampah/pembalut di kloset biar nggak mampet.\n" .
                    "3. Jemur pakaian di tempat jemuran yang sudah ada ya, jangan di depan kamar agar tetap rapi.\n\n" .

                "🚰 **FASILITAS**\n" .
                    "1. Gunakan fasilitas kost dengan bijak dan penuh rasa tanggung jawab.\n" .
                    "2. Jika ada kerusakan karena kelalaian, biaya perbaikannya ditanggung penghuni dulu ya.\n" .
                    "3. Hemat air ya Kak, gunakan secukupnya saja sesuai kebutuhan.\n\n" .

                "Sekali lagi, selamat bergabung di keluarga besar Serrata Kost! Kalau ada apa-apa, jangan sungkan hubungi kami ya. Enjoy your stay! 🏠💖";

        try {
            $response = Http::timeout(10)->post('http://localhost:3000/send-message', [
                'number'  => $tenant->phone,
                'message' => $message
            ]);

            if ($response->successful()) {
                Log::info("Pesan WA Terkirim ke {$tenant->phone}");
            } else {
                Log::error("API Gateway Error: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Gagal koneksi ke WhatsApp Gateway: " . $e->getMessage());
        }

        return "https://wa.me/" . $tenant->phone . "?text=" . urlencode($message);
    }
}
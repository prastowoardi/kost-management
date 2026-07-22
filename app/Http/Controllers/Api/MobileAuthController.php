<?php

namespace App\Http\Controllers\Api;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Throwable;

class MobileAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'push_token' => 'nullable|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            \Illuminate\Support\Facades\Log::error('LOGIN: User not found for email: '.$request->email);

            LogHelper::log(
                'LOGIN_FAILED',
                "Percobaan login gagal pada email: {$request->email}",
                null,
                ['ip' => $request->ip(), 'user_agent' => $request->userAgent()]
            );

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal Login.',
            ], 401);
        }

        \Illuminate\Support\Facades\Log::info('LOGIN: User found', [
            'email' => $user->email,
            'password_hash_prefix' => substr($user->password, 0, 20),
            'password_check' => Hash::check($request->password, $user->password),
        ]);

        if (! Hash::check($request->password, $user->password)) {
            \Illuminate\Support\Facades\Log::error('LOGIN: Password mismatch for: '.$request->email);

            LogHelper::log(
                'LOGIN_FAILED',
                "Percobaan login gagal pada email: {$request->email}",
                null,
                ['ip' => $request->ip(), 'user_agent' => $request->userAgent()]
            );

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal Login.',
            ], 401);
        }

        if ($request->push_token) {
            User::where('expo_push_token', $request->push_token)->update(['expo_push_token' => null]);

            $user->expo_push_token = $request->push_token;
            $user->save();
        }

        $user->tokens()->delete();
        $token = $user->createToken('mobile_token')->plainTextToken;

        \App\Helpers\LogHelper::log('LOGIN_MOBILE', "User {$user->name} login...");

        return response()->json([
            'status' => 'success',
            'message' => 'Login Berhasil',
            'token' => $token,
            'user' => [
                'uuid' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->update(['expo_push_token' => null]);

        $user->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil Logout',
        ]);
    }

    public function getAdminStats()
    {
        return response()->json([
            'total_rooms' => Room::count(),
            'occupied_rooms' => Room::where('status', 'occupied')->count(),
            'pending_complaints' => Complaint::where('status', 'pending')->count(),
            'monthly_income' => Payment::whereMonth('created_at', now()->month)->sum('amount'),
        ]);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required'],
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'new_password.min' => 'Password baru minimal harus 8 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            LogHelper::logError(
                'CHANGE_PASSWORD_FAILED',
                "User {$user->email} gagal ganti password: password lama salah",
            );

            return response()->json([
                'status' => 'error',
                'message' => 'Password lama yang Anda masukkan salah.',
            ], 401);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil diperbarui.',
        ], 200);
    }
}

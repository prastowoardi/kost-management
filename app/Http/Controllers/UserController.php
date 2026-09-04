<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Throwable;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->whereNull('deleted_at')],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'role' => ['required', 'in:admin,staff,tenant'],
                'is_active' => ['boolean'],
            ]);

            $validated['password'] = Hash::make($validated['password']);
            $validated['is_active'] = $request->has('is_active');

            $user = User::create($validated);

            LogHelper::log('CREATE_USER', "Menambah user {$user->name} ({$user->email})", $user);

            return redirect()->route('users.index')
                ->with('success', 'User berhasil ditambahkan');
        } catch (Throwable $e) {
            LogHelper::logError('CREATE_USER_FAILED', 'Gagal menambah user', $e);

            return back()->with('error', 'Gagal menambah user')->withInput();
        }
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)->whereNull('deleted_at')],
                'role' => ['required', 'in:admin,staff,tenant'],
                'is_active' => ['boolean'],
            ]);

            if ($request->filled('password')) {
                $request->validate([
                    'password' => ['confirmed', Rules\Password::defaults()],
                ]);
                $validated['password'] = Hash::make($request->password);
            }

            $validated['is_active'] = $request->has('is_active');

            $before = $user->toArray();
            $user->update($validated);
            $after = $user->fresh()->toArray();

            LogHelper::log('UPDATE_USER', "Mengubah user {$user->name} ({$user->email})", $user, [
                'before' => $before,
                'after' => $after,
            ]);

            return redirect()->route('users.index')
                ->with('success', 'User berhasil diupdate');
        } catch (Throwable $e) {
            LogHelper::logError('UPDATE_USER_FAILED', "Gagal update user #{$user->id}", $e);

            return back()->with('error', 'Gagal mengupdate user')->withInput();
        }
    }

    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri');
        }

        try {
            $deletedData = $user->toArray();
            $user->delete();

            LogHelper::log('DELETE_USER', "Menghapus user {$deletedData['name']} ({$deletedData['email']})", null, [
                'deleted' => $deletedData,
            ]);

            return redirect()->route('users.index')
                ->with('success', 'User berhasil dihapus');
        } catch (Throwable $e) {
            LogHelper::logError('DELETE_USER_FAILED', "Gagal hapus user #{$user->id}", $e);

            return back()->with('error', 'Gagal menghapus user');
        }
    }

    public function toggleStatus(User $user)
    {
        // Prevent deactivating yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menonaktifkan akun sendiri');
        }

        try {
            $user->update([
                'is_active' => ! $user->is_active,
            ]);

            $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

            LogHelper::log('TOGGLE_USER_STATUS', "{$status} user {$user->name} ({$user->email})", $user);

            return redirect()->route('users.index')
                ->with('success', "User berhasil {$status}");
        } catch (Throwable $e) {
            LogHelper::logError('TOGGLE_USER_STATUS_FAILED', "Gagal toggle status user #{$user->id}", $e);

            return back()->with('error', 'Gagal mengubah status user');
        }
    }
}

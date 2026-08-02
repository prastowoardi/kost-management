<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Edit User') }} - {{ $user->name }}
        </h2>
    </x-slot>

    <div class="pt-4 sm:pt-5 pb-8 sm:pb-10">
        <div class="mx-auto w-full max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="md:col-span-2 form-group">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                    class="rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                @error('name')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2 form-group">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                    class="rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                @error('email')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2 form-group">
                                <label for="role" class="form-label">Role</label>
                                <select name="role" id="role" required class="rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                                    {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                                    <option value="tenant" {{ old('role', $user->role) == 'tenant' ? 'selected' : '' }}>Tenant</option>
                                </select>
                                @if($user->id === auth()->id())
                                <p class="text-xs text-stone-400">Anda tidak dapat mengubah role sendiri</p>
                                <input type="hidden" name="role" value="{{ $user->role }}">
                                @endif
                                @error('role')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2 border-t border-stone-100 pt-5">
                                <p class="text-sm font-semibold text-stone-700 mb-3">Ubah Password (Opsional)</p>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="form-group">
                                        <label for="password" class="form-label">Password Baru</label>
                                        <input type="password" id="password" name="password"
                                            class="rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                        <p class="text-xs text-stone-400">Kosongkan jika tidak ingin mengubah password</p>
                                        @error('password')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                        <input type="password" id="password_confirmation" name="password_confirmation"
                                            class="rounded-xl border-stone-200 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                    </div>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1"
                                        {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                        {{ $user->id === auth()->id() ? 'disabled' : '' }}
                                        class="h-4 w-4 rounded border-stone-300 text-brand-600 focus:ring-brand-500">
                                    <span class="text-sm text-stone-700">User aktif</span>
                                </label>
                                @if($user->id === auth()->id())
                                <p class="mt-1 text-xs text-stone-400">Anda tidak dapat menonaktifkan akun sendiri</p>
                                <input type="hidden" name="is_active" value="1">
                                @endif
                            </div>
                        </div>

                        <div class="mt-6 flex flex-wrap items-center justify-end gap-3 border-t border-stone-100 pt-5">
                            <a href="{{ route('users.index') }}" class="btn-secondary">
                                Batal
                            </a>
                            <button type="submit" class="btn-primary">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Penghuni') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('tenants.update', $tenant) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Copy isi form dari create.blade.php -->
                        <!-- Ubah value="{{ old('name') }}" jadi value="{{ old('name', $tenant->name) }}" -->
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
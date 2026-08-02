<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Update Status Keluhan') }}
        </h2>
    </x-slot>

    <div class="pt-4 sm:pt-5 pb-8 sm:pb-10">
        <div class="page-container">
            <div class="mx-auto max-w-4xl">
                <div class="card">
                    <div class="card-body">

                        <!-- Read Only Information -->
                        <div class="rounded-2xl border border-stone-100 bg-stone-50/70 p-6">
                            <h3 class="section-title mb-4">Informasi Keluhan</h3>

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-stone-400">Penghuni</p>
                                    <p class="mt-1 font-semibold text-stone-900">{{ $complaint->tenant->name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-stone-400">Kamar</p>
                                    <p class="mt-1 font-semibold text-stone-900">{{ $complaint->room->room_number }}</p>
                                </div>
                                <div class="sm:col-span-2">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-stone-400">Judul Keluhan</p>
                                    <p class="mt-1 font-semibold text-stone-900">{{ $complaint->title }}</p>
                                </div>
                                <div class="sm:col-span-2">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-stone-400">Deskripsi</p>
                                    <p class="mt-1 text-stone-700">{{ $complaint->description }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-stone-400">Kategori</p>
                                    <div class="mt-1">
                                        <span class="badge-neutral">
                                            {{ ucfirst($complaint->category) }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-stone-400">Prioritas</p>
                                    <div class="mt-1">
                                        <span class="@if($complaint->priority == 'high') badge-danger @elseif($complaint->priority == 'medium') badge-warning @else badge-success @endif">
                                            {{ ucfirst($complaint->priority) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-stone-400">Tanggal Dibuat</p>
                                    <p class="mt-1 text-stone-700">{{ $complaint->created_at->format('d F Y H:i') }}</p>
                                </div>
                            </div>

                            <!-- Images -->
                            @if($complaint->images && count($complaint->images) > 0)
                            <div class="mt-6">
                                <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-stone-400">Foto Keluhan</p>
                                <div class="grid grid-cols-3 gap-4 sm:grid-cols-5">
                                    @foreach($complaint->images as $image)
                                    <img src="{{ asset('storage/' . $image) }}"
                                            alt="Foto Keluhan"
                                            class="h-24 w-full cursor-pointer rounded-2xl border border-stone-100 object-cover shadow-sm transition hover:opacity-75"
                                            onclick="openImageModal('{{ asset('storage/' . $image) }}')">
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Editable Status Form -->
                        <form action="{{ route('complaints.update', $complaint) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Hidden fields untuk data yang tidak diubah -->
                            <input type="hidden" name="tenant_id" value="{{ $complaint->tenant_id }}">
                            <input type="hidden" name="title" value="{{ $complaint->title }}">
                            <input type="hidden" name="description" value="{{ $complaint->description }}">
                            <input type="hidden" name="category" value="{{ $complaint->category }}">
                            <input type="hidden" name="priority" value="{{ $complaint->priority }}">

                            <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div class="form-group md:col-span-2">
                                    <label class="form-label">Status Keluhan</label>
                                    <select name="status" required class="mt-1 block w-full">
                                        <option value="open" {{ old('status', $complaint->status) == 'open' ? 'selected' : '' }}>Open - Belum Ditangani</option>
                                        <option value="in_progress" {{ old('status', $complaint->status) == 'in_progress' ? 'selected' : '' }}>In Progress - Sedang Ditangani</option>
                                        <option value="resolved" {{ old('status', $complaint->status) == 'resolved' ? 'selected' : '' }}>Resolved - Sudah Selesai</option>
                                        <option value="closed" {{ old('status', $complaint->status) == 'closed' ? 'selected' : '' }}>Closed - Ditutup</option>
                                    </select>
                                    @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group md:col-span-2">
                                    <label class="form-label">Tanggapan / Response</label>
                                    <textarea name="response" rows="4" class="mt-1 block w-full" placeholder="Tulis tanggapan untuk keluhan ini...">{{ old('response', $complaint->response) }}</textarea>
                                    @error('response')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Tanggal Selesai</label>
                                    <input type="date" name="resolved_date" value="{{ old('resolved_date', $complaint->resolved_date?->format('Y-m-d')) }}"
                                        onclick="this.showPicker()"
                                        class="mt-1 block w-full cursor-pointer">
                                    <p class="form-hint">Isi jika keluhan sudah resolved/closed</p>
                                    @error('resolved_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-8 flex items-center justify-end gap-3 border-t border-stone-100 pt-6">
                                <a href="{{ route('complaints.index') }}" class="btn-secondary">
                                    Batal
                                </a>
                                <button type="submit" class="btn-primary">
                                    Update Status
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4" onclick="closeImageModal()">
        <div class="max-w-4xl max-h-full">
            <img id="modalImage" src="" alt="Full Image" class="max-w-full max-h-[90vh] object-contain rounded-2xl">
        </div>
    </div>

    <script>
        function openImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }
    </script>
</x-app-layout>

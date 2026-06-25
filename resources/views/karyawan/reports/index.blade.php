@extends('layouts.karyawan')
@section('title', 'Lapor')
@section('content')
<div class="container mx-auto p-4">
    @include('components.session-message')
    <div class="flex items-center mb-2">
        <a href="{{ route('dashboard') }}" class="text-lg font-semibold flex items-center w-fit">
        <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>
    <h5 class="font-semibold text-gray-600 mb-4">Silahkan mengisi form berikut untuk melaporkan masalah terkait fasilitas kantor</h5>
    <form action="{{ route('reports.store') }}" method="POST" class="w-full" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
        {{-- Bukti Foto Field --}}
        <div class="mb-6">
            <label for="bukti_foto" class="block text-gray-700 font-medium mb-2">Bukti Foto</label>
            
            <button type="button" onclick="document.getElementById('foto').click()" class="bg-red-700 text-white px-6 py-3 rounded-full flex items-center gap-2 hover:bg-red-800 transition mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Unggah Foto
            </button>
            
            <input type="file" id="foto" name="foto[]" multiple accept="image/*" class="hidden" onchange="previewImages(event)">
            
            <!-- Image Preview Grid -->
            <div id="imagePreview" class="grid grid-cols-3 gap-3">
                <!-- Preview images will be inserted here -->
            </div>
        </div>
        
        {{-- Lokasi Field --}}
        <div class="mb-6">
            <label for="lokasi_area" class="block text-gray-700 font-medium mb-2">Lokasi Daerah</label>
            <select id="lokasi_area" name="lokasi_area" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-700 focus:border-transparent" required>
                <option value="">Pilih lokasi daerah</option>
                @foreach($locations as $area => $details)
                    <option value="{{ $area }}" @selected(old('lokasi_area') === $area)>{{ $area }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-6">
            <label for="lokasi_detail" class="block text-gray-700 font-medium mb-2">Gedung atau Lantai</label>
            <select id="lokasi_detail" name="lokasi_detail" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-700 focus:border-transparent" required disabled>
                <option value="">Pilih gedung atau lantai</option>
            </select>
        </div>

        <div class="mb-6">
            <label for="lokasi_catatan" class="block text-gray-700 font-medium mb-2">Keterangan Lokasi</label>
            <input type="text" id="lokasi_catatan" name="lokasi_catatan" value="{{ old('lokasi_catatan') }}" placeholder="Contoh: ruang meeting, area pantry, sisi kanan lift" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-700 focus:border-transparent" required>
            <p class="text-gray-400 text-xs mt-1">Isi detail area setelah memilih lokasi.</p>
        </div>

        {{-- Kategori Field --}}
        <div class="mb-6">
            <label for="kategori" class="block text-gray-700 font-medium mb-2">Kategori</label>
            <select id="kategori" name="kategori" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-700 focus:border-transparent" required>
                <option value="">Pilih kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" @selected(old('kategori') === $category)>{{ $category }}</option>
                @endforeach
            </select>
            <p class="text-gray-400 text-xs mt-1">Pilihan kategori dikelola oleh admin.</p>
        </div>

        {{-- Permasalahan --}}
        <div class="mb-8">
            <label for="permasalahan" class="block text-gray-700 font-medium mb-2">Permasalahan</label>
            <textarea name="permasalahan" rows="6" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-700 focus:border-transparent resize-none" placeholder="Masukkan permasalahan" required></textarea>
            <p class="text-gray-400 text-xs mt-1">Jelaskan permasalahan secara jelas dan detail</p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex gap-4">
            <a href="{{ route('dashboard') }}" class="flex-1 px-6 py-3 border-2 border-red-700 text-red-700 rounded-full font-semibold hover:bg-red-50 transition text-center">Batal</a>
            <button 
                type="submit"
                class="flex-1 px-6 py-3 bg-red-700 text-white rounded-full font-semibold hover:bg-red-800 transition"
            >
                Kirim
            </button>
        </div>
    </form>
</div>
<!-- Image Preview Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50">
    <div class="relative max-w-3xl w-full px-4">
        <img
            id="modalImage"
            src=""
            class="w-full max-h-[80vh] object-contain rounded-lg shadow-lg bg-white"
        >
    </div>
</div>

@endsection
@push('scripts')
<script>
    const fileInput = document.getElementById('foto');
    const previewContainer = document.getElementById('imagePreview');
    const locations = @json($locations);
    const oldLocationArea = @json(old('lokasi_area'));
    const oldLocationDetail = @json(old('lokasi_detail'));
    const locationAreaInput = document.getElementById('lokasi_area');
    const locationDetailInput = document.getElementById('lokasi_detail');

    let selectedFiles = [];

    function populateLocationDetail(selectedArea, selectedDetail = '') {
        locationDetailInput.innerHTML = '<option value="">Pilih gedung atau lantai</option>';
        locationDetailInput.disabled = !selectedArea;

        if (!selectedArea || !locations[selectedArea]) {
            return;
        }

        locations[selectedArea].forEach(function (detail) {
            const option = document.createElement('option');
            option.value = detail;
            option.textContent = detail;
            option.selected = detail === selectedDetail;
            locationDetailInput.appendChild(option);
        });
    }

    locationAreaInput.addEventListener('change', function (event) {
        populateLocationDetail(event.target.value);
    });

    if (oldLocationArea) {
        populateLocationDetail(oldLocationArea, oldLocationDetail);
    }

    fileInput.addEventListener('change', function (e) {
        selectedFiles = Array.from(e.target.files);
        renderPreviews();
    });

    function renderPreviews() {
        previewContainer.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            if (!file.type.startsWith('image/')) return;

            const reader = new FileReader();

            reader.onload = function (e) {
                const div = document.createElement('div');
                div.className = 'relative bg-gray-200 rounded-lg overflow-hidden w-full max-w-[120px] h-[120px]';

                div.innerHTML = `
                    <img 
                        src="${e.target.result}" 
                        onclick="openModal('${e.target.result}')"
                        class="object-cover w-full h-full cursor-pointer hover:opacity-90 transition"
                    />
                    <button
                        type="button"
                        onclick="removeImage(${index})"
                        class="absolute top-1 right-1 bg-red-600 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs hover:bg-red-700"
                    >
                        X
                    </button>

                    <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs text-center py-1 truncate px-1">
                        ${file.name}
                    </div>
                `;

                previewContainer.appendChild(div);
            };

            reader.readAsDataURL(file);
        });

        // updateInputFiles();
    }

    function removeImage(index) {
        selectedFiles.splice(index, 1);
        renderPreviews();
    }

    function updateInputFiles() {
        const dataTransfer = new DataTransfer();

        selectedFiles.forEach(file => {
            dataTransfer.items.add(file);
        });

        fileInput.files = dataTransfer.files;
    }
    function openModal(imageSrc) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');

        modalImage.src = imageSrc;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        const modal = document.getElementById('imageModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.getElementById('imageModal').addEventListener('click', function (e) {
        if (e.target.id === 'imageModal') {
            closeModal();
        }
    });

</script>
@endpush

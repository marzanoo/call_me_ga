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
            <label for="lokasi" class="block text-gray-700 font-medium mb-2">Lokasi</label>
            <input type="text" name="lokasi" placeholder="Masukkan lokasi" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-700 focus:border-transparent" required>
            <p class="text-gray-400 text-xs mt-1">Gedung, lantai, area/ruangan, etc</p>
        </div>

        {{-- Kategori Field --}}
        <div class="mb-6">
            <label for="kategori" class="block text-gray-700 font-medium mb-2">Kategori</label>
            <input type="text" name="kategori" placeholder="Masukkan kategori" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-700 focus:border-transparent" required>
            <p class="text-gray-400 text-xs mt-1">Lampu, meja, kursi, etc</p>   
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
@endsection
@push('scripts')
<script>
    function previewImages(event) {
        const previewContainer = document.getElementById('imagePreview');
        const files = event.target.files;

        // Clear existing previews
        previewContainer.innerHTML = '';

        // Loop through selected files
        Array.from(files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative bg-gray-200 rounded-lg overflow-hidden aspect-square';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Preview ${index + 1}" class="object-cover w-full h-full">
                        <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs text-center py-1">
                            img.jpg
                        </div>
                    `;
                    previewContainer.appendChild(div);
                };

                reader.readAsDataURL(file);
            }
        });
    }
</script>
@endpush
@extends('layouts.teknisi')
@section('title', 'Detail Tugas')
@section('content')
<div class="container mx-auto p-4">
    @include('components.session-message')
    <div class="flex items-center mb-2">
        <a href="{{ route('teknisi.reports.processed.index') }}" class="text-lg font-semibold flex items-center w-fit">
            <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <h5 class="font-semibold text-gray-600 mb-4">Detail Tugas</h5>

    <div class="border border-gray-300 rounded-lg p-4 shadow-sm mb-6 bg-white">
        <div class="flex justify-between items-center mb-3">
            <span class="text-base font-medium text-gray-700">{{ $report->kategori }}</span>
            <span class="text-sm font-medium text-gray-500">{{ $report->created_at->format('d F Y - H:i') }}</span>
        </div>

        @if($report->detailFotoReports->count())
            <div class="grid grid-cols-3 gap-2 mb-4">
                @foreach($report->detailFotoReports as $foto)
                    <img src="{{ asset('storage/' . $foto->image_path) }}" alt="Foto Laporan" class="w-full h-28 object-cover rounded-lg cursor-pointer" onclick="openImageModal('{{ asset('storage/' . $foto->image_path) }}')">
                @endforeach
            </div>
        @endif

        <h6 class="font-semibold text-gray-800 mb-2">{{ $report->lokasi }}</h6>
        <p class="text-gray-600 text-sm mb-4">{{ $report->permasalahan }}</p>

        <hr class="border-gray-300 mb-4">

        <div class="bg-gray-50 rounded-lg p-4">
            <h6 class="font-semibold text-gray-800 mb-2">Riwayat Status</h6>
            @include('components.status-timeline', [
                'statuses' => $report->detailStatusReports()->with('creator:id,name')->orderBy('created_at', 'asc')->get()
            ])
        </div>

        <div class="mt-6 border rounded-lg p-4">
            <h6 class="font-semibold text-gray-800 mb-3">Update Progres</h6>
            <form method="POST" action="{{ route('teknisi.reports.processed.update-status', $report->id) }}">
                @csrf
                <input type="hidden" name="status" value="Diproses">
                <label for="keterangan_diproses" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" id="keterangan_diproses" rows="4" class="w-full border rounded-md p-2 text-sm" placeholder="Tulis progres penanganan terbaru..." required>{{ old('keterangan') }}</textarea>
                <button type="submit" class="mt-3 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    Simpan Masih Diproses
                </button>
            </form>
        </div>

        <div class="mt-6">
            <button type="button" onclick="openUploadFotoSelesaiModal()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">
                Selesai
            </button>
        </div>
    </div>
</div>

<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-70 hidden z-50 flex items-center justify-center p-4">
    <div class="relative bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[85vh] p-3">
        <button onclick="closeImageModal()" class="absolute -top-3 -right-3 bg-red-600 text-white w-8 h-8 rounded-full text-xl flex items-center justify-center">&times;</button>
        <img id="modalImage" src="" class="w-full max-h-[75vh] object-contain rounded-lg">
    </div>
</div>

<div id="uploadFotoSelesaiModal" class="fixed inset-0 bg-black bg-opacity-70 hidden z-50 flex items-center justify-center p-4">
    <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-6">
        <button onclick="closeUploadFotoSelesaiModal()" class="text-lg font-semibold flex items-center w-fit mb-4">
            <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
        </button>
        <h3 class="text-lg font-semibold mb-4">Upload Foto Penyelesaian</h3>
        <form method="POST" action="{{ route('teknisi.reports.processed.update-status', $report->id) }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="status" value="Selesai">
            <label for="keterangan_selesai" class="block text-sm font-medium text-gray-700 mb-2">Keterangan Penyelesaian</label>
            <textarea name="keterangan" id="keterangan_selesai" rows="4" class="w-full border rounded-md p-2 text-sm mb-4" placeholder="Jelaskan hasil penyelesaian laporan..." required>{{ old('keterangan') }}</textarea>

            <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">Foto bukti penyelesaian</label>
            <button type="button" onclick="document.getElementById('foto').click()" class="bg-red-700 text-white px-4 py-2 rounded-full flex items-center gap-2 hover:bg-red-800 transition mb-4">
                <i class="fa-solid fa-plus"></i>
                Unggah Foto
            </button>
            <input type="file" id="foto" name="buktiFoto[]" multiple accept="image/*" class="hidden">
            <div id="imagePreview" class="grid grid-cols-3 gap-3 mb-4"></div>
            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">Selesai</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const fileInput = document.getElementById('foto');
    const previewContainer = document.getElementById('imagePreview');
    let selectedFiles = [];

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
                    <img src="${e.target.result}" onclick="openImageModal('${e.target.result}')" class="object-cover w-full h-full cursor-pointer hover:opacity-90 transition" />
                    <button type="button" onclick="removeImage(${index})" class="absolute top-1 right-1 bg-red-600 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs hover:bg-red-700">X</button>
                    <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs text-center py-1 truncate px-1">${file.name}</div>
                `;
                previewContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
        });

        updateInputFiles();
    }

    function removeImage(index) {
        selectedFiles.splice(index, 1);
        renderPreviews();
    }

    function updateInputFiles() {
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        fileInput.files = dataTransfer.files;
    }

    function openUploadFotoSelesaiModal() {
        document.getElementById('uploadFotoSelesaiModal').classList.remove('hidden');
    }

    function closeUploadFotoSelesaiModal() {
        document.getElementById('uploadFotoSelesaiModal').classList.add('hidden');
    }

    function openImageModal(src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.getElementById('modalImage').src = '';
    }

    document.getElementById('imageModal').addEventListener('click', function (e) {
        if (e.target.id === 'imageModal') closeImageModal();
    });

    document.getElementById('uploadFotoSelesaiModal').addEventListener('click', function (e) {
        if (e.target.id === 'uploadFotoSelesaiModal') closeUploadFotoSelesaiModal();
    });
</script>
@endpush

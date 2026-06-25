@extends('layouts.teknisi')
@section('title', 'Detail Tugas Ditolak')
@section('content')
<div class="container mx-auto p-4">
    @include('components.session-message')
    <div class="flex items-center mb-2">
        <a href="{{ route('teknisi.reports.finish.index', ['status' => 'ditolak']) }}" class="text-lg font-semibold flex items-center w-fit">
            <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <h5 class="font-semibold text-gray-600 mb-4">Detail Tugas Ditolak</h5>

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

        <div class="bg-red-50 border border-red-100 rounded-lg p-4 mb-4">
            <h6 class="font-semibold text-red-800 mb-2">Alasan Penolakan</h6>
            <p class="text-sm text-red-700">{{ $report->detailStatusReports->first()?->feedback ?? 'Tidak ada alasan penolakan.' }}</p>
        </div>

        <div class="bg-gray-50 rounded-lg p-4">
            <h6 class="font-semibold text-gray-800 mb-2">Riwayat Status</h6>
            @include('components.status-timeline', [
                'statuses' => $report->detailStatusReports()->with('creator:id,name')->orderBy('created_at', 'asc')->get()
            ])
        </div>
    </div>
</div>

<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-70 hidden z-50 flex items-center justify-center p-4">
    <div class="relative bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[85vh] p-3">
        <button onclick="closeImageModal()" class="absolute -top-3 -right-3 bg-red-600 text-white w-8 h-8 rounded-full text-xl flex items-center justify-center">&times;</button>
        <img id="modalImage" src="" class="w-full max-h-[75vh] object-contain rounded-lg">
    </div>
</div>
@endsection
@push('scripts')
<script>
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
</script>
@endpush

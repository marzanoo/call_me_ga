@extends('layouts.admin')
@section('title', 'Detail Laporan')
@section('content')
<div class="container mx-auto p-4">
    @include('components.session-message')
    <div class="flex items-center mb-2">
        @if (!session('login_via_superapp'))
        <a href="{{ route('admin.reports.index') . '?status=menunggu' }}" class="text-lg font-semibold flex items-center w-fit">
        @endif
        @if (session('login_via_superapp'))
        <a href="{{ route('admin.reports.waiting.index') }}" class="text-lg font-semibold flex items-center w-fit">
        @endif
        <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>
    <h5 class="font-semibold text-gray-600 mb-4">Detail Riwayat Laporan</h5>
    <div class="border border-gray-300 rounded-lg p-4 shadow-sm mb-6">
        <div class="flex justify-between items-center mb-3">
            <span class="text-base font-medium text-gray-700">{{ $report->kategori ?? 'not found' }}</span>
            <span class="text-sm font-medium text-gray-500">{{ $report->created_at->format('d F Y - H:i') }}</span>
        </div>
        
        {{-- Images Grid --}}
        @if($report->detailFotoReports->count())
            <div class="grid grid-cols-3 gap-2 mb-4">
                @foreach($report->detailFotoReports as $foto)
                    <img
                        src="{{ asset('storage/' . $foto->image_path) }}"
                        alt="Foto Laporan"
                        class="w-full h-28 object-cover rounded-lg cursor-pointer"
                        onclick="openImageModal('{{ asset('storage/' . $foto->image_path) }}')"
                    >
                @endforeach
            </div>
        @endif
        {{-- Report Title and Description --}}
        <h6 class="font-semibold text-gray-800 mb-2">{{ $report->lokasi ?? 'Gedung WMS Lantai 5, CMD' }}</h6>
        <p class="text-gray-600 text-sm mb-4">
            {{ $report->permasalahan ?? 'Lampu mengalami kerusakan mati total nih jadi gelap kaga bisa gawe jadinya' }}
        </p>
        
        <hr class="border-gray-300 mb-4">
        
        {{-- Status Section --}}
        <div class="bg-gray-50 rounded-lg p-4">
            <h6 class="font-semibold text-gray-800 mb-2">Status Laporan</h6>            
        
            {{-- Status Timeline --}}
            <div class="space-y-4">
                @foreach($report->detailStatusReports()->orderBy('created_at', 'asc')->get() as $status)
                    <div class="flex gap-3">
                        {{-- Status Icon --}}
                        <div class="flex flex-col items-center">
                            @if($status->status == 'Menunggu')
                                <div class="w-5 h-5 rounded-full bg-yellow-400 flex items-center justify-center flex-shrink-0">
                                    
                                </div>
                            @elseif($status->status == 'Diproses')
                                <div class="w-5 h-5 rounded-full bg-teal-400 flex items-center justify-center flex-shrink-0">
                                    
                                </div>
                            @elseif ($status->status == 'Ditolak')
                                <div class="w-5 h-5 rounded-full bg-red-400 flex items-center justify-center flex-shrink-0">
                                    
                                </div>
                            @elseif ($status->status == 'Selesai')
                                <div class="w-5 h-5 rounded-full bg-green-400 flex items-center justify-center flex-shrink-0">
                                    
                                </div>
                            @endif
                            
                            {{-- Connector Line --}}
                            @if(!$loop->last)
                                <div class="w-0.5 h-12 bg-gray-300 my-1"></div>
                            @endif
                        </div>
                        
                        {{-- Status Content --}}
                        <div class="flex-1 pb-4">
                            <div class="flex justify-between items-start mb-1">
                                <span class="font-semibold text-gray-800">{{ $status->status }}</span>
                                <span class="text-xs text-gray-500">{{ $status->created_at->format('d F Y - H:i') }}</span>
                            </div>
                            <p class="text-sm text-gray-600">{{ $status->keterangan ?? 'Sedang dalam proses' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <form method="POST" action="{{ route('admin.reports.waiting.update-status', $report->id) }}">
            @csrf

            <div class="mt-6 flex gap-3">
                <button type="submit"
                        name="status"
                        value="Diproses"
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">
                    Verifikasi
                </button>

                <button type="submit"
                        name="status"
                        value="Ditolak"
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md">
                    Tolak
                </button>
            </div>
        </form>
    </div>
</div>
{{-- Image Modal --}}
<div id="imageModal"
     class="fixed inset-0 bg-black bg-opacity-70 hidden z-50 flex items-center justify-center p-4">

    <div class="relative bg-white rounded-xl shadow-xl
                max-w-3xl w-full max-h-[85vh] p-3">

        <button onclick="closeImageModal()"
                class="absolute -top-3 -right-3 bg-red-600 text-white
                       w-8 h-8 rounded-full text-xl flex items-center justify-center">
            &times;
        </button>

        <img id="modalImage"
             src=""
             class="w-full max-h-[75vh] object-contain rounded-lg">
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
        if (e.target.id === 'imageModal') {
            closeImageModal();
        }
    });
</script>
@endpush
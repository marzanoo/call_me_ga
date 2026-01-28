@extends('layouts.karyawan')
@section('title', 'Riwayat Laporan')
@section('content')
<div class="container mx-auto p-4">
    @include('components.session-message')
    <div class="flex items-center mb-2">
        <a href="{{ route('dashboard') }}" class="text-lg font-semibold flex items-center w-fit">
        <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>
    <h5 class="font-semibold text-gray-600 mb-4">Riwayat Laporan</h5>
    <div class="space-y-4">
        @foreach ($reports as $report)
            <div class="border border-gray-300 rounded-lg p-4 shadow-sm">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-500">Laporan ID: {{ $report->id }}</span>
                    <span class="text-sm font-medium text-gray-500">{{ $report->created_at->format('d M Y, H:i') }}</span>
                </div>
                <h6 class="font-semibold text-gray-700 mb-2">{{ $report->kategori }} - {{ $report->lokasi }}</h6>
                <p class="text-gray-600 mb-4">{{ $report->permasalahan }}</p>
                @if($report->detailFotoReports->count() > 0)
                    <div class="grid grid-cols-3 gap-2">
                        @foreach ($report->detailFotoReports as $foto)
                            <img src="{{ asset('storage/' . $foto->image_path) }}" alt="Bukti Foto" class="w-full h-24 object-cover rounded-md">
                        @endforeach
                    </div>
                @endif
                {{-- <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-gray-500">Status: {{ $report->detailStatusReports->status }}</span>
                </div> --}}
                {{-- Button Detail --}}
                <div class="mt-4 text-right">
                    <a href="{{ route('history.show', $report->id) }}" class="text-red-700 font-semibold hover:underline">Lihat Detail</a>
                </div>
            </div>            
        @endforeach
    </div>
</div>
@endsection
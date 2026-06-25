@extends('layouts.karyawan')
@section('title', 'Detail Riwayat Laporan')
@section('content')
<div class="container mx-auto p-4">
    @include('components.session-message')
    <div class="flex items-center mb-2">
        <a href="{{ route('history.index') }}" class="text-lg font-semibold flex items-center w-fit">
        <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>
    <h5 class="font-semibold text-gray-600 mb-4">Detail Riwayat Laporan</h5>
    <div class="border border-gray-300 rounded-lg p-4 shadow-sm mb-6">
        <div class="flex justify-between items-center mb-3">
            <span class="text-base font-medium text-gray-700">{{ $report->kategori ?? 'not found' }}</span>
            <span class="text-sm font-medium text-gray-500">{{ $report->created_at->format('d F Y - H:i') }}</span>
        </div>
        
        {{-- Image Placeholder --}}
        <div class="w-full bg-gray-300 rounded-lg mb-3" style="height: 280px;">
            @if($report->detailFotoReports->count() > 0)
                <img src="{{ asset('storage/' . $report->detailFotoReports->first()->image_path) }}" 
                     alt="Foto Laporan" 
                     class="w-full h-full object-cover rounded-lg">
            @endif
        </div>
        
        {{-- Report Title and Description --}}
        <h6 class="font-semibold text-gray-800 mb-2">{{ $report->lokasi ?? 'Gedung WMS Lantai 5, CMD' }}</h6>
        <p class="text-gray-600 text-sm mb-4">
            {{ $report->permasalahan ?? 'Lampu mengalami kerusakan mati total nih jadi gelap kaga bisa gawe jadinya' }}
        </p>
        
        <hr class="border-gray-300 mb-4">
        
        {{-- Status Section --}}
        <div class="bg-gray-50 rounded-lg">
            <h6 class="font-semibold text-gray-800 mb-2">Status Laporan</h6>            
            
            @include('components.status-timeline', [
                'statuses' => $report->detailStatusReports()->with('creator:id,name')->orderBy('created_at', 'asc')->get()
            ])
        </div>
    </div>
</div>
@endsection

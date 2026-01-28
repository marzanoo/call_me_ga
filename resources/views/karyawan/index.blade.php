@extends('layouts.karyawan')

@section('title', 'Beranda')

@section('content')
<div class="container mx-auto p-4">    
    @include('components.session-message')

    <div class="bg-[#B3282D] rounded-lg px-6 py-8 shadow-md text-white">
        <div class="grid grid-cols-[55%_45%] items-center">
            <div>
                <h2 class="text-2xl font-bold">Welcome {{ ucwords(strtolower($user->name)) }}</h2>
                <p class="text-lg opacity-90 mt-1">Laporkan masalah fasilitas kantor dengan mudah.</p>
            </div>
            <div class="text-6xl text-right">
                ✨📷
            </div>
        </div>
    </div>
</div>
{{-- KPI Section --}}
<div class="container mx-auto p-4">
    <div class="bg-white p-4 rounded-lg shadow-md text-center mb-4">
        <h3 class="flex text-xl font-bold text-gray-800 mb-1">Status Laporan Terakhir</h3>
        <p class="flex text-sm text-gray-500 mb-6">{{ $lastReportStatus->created_at->format('d F Y') }}</p>
        <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
            <div class="flex items-start gap-3">
                <div class="w-6 h-6 bg-yellow-400 rounded-full flex-shrink-0 mt-0.5"></div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <h4 class="font-semibold text-gray-900"> {{ $lastReportStatus->detailStatusReports->first()->status }}</h4>
                        <span class="text-gray-400">|</span>
                        <span class="text-sm text-gray-500">{{ $lastReportStatus->detailStatusReports->first()->created_at->format('d F Y - H:i') }}</span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed text-left">
                        {{ $lastReportStatus->detailStatusReports->first()->keterangan ?? 'Tidak ada keterangan tambahan.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white p-4 rounded-lg shadow-md text-center mb-4">
        <h3 class="flex text-xl font-bold text-gray-800 mb-1">Total Laporan</h3>
        <p class="flex text-sm text-gray-500 mb-6">{{ now()->format('Y') }}</p>

        <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div class="flex mb-2">
                    <div class="w-4 h-4 bg-green-500 rounded-full mr-2"></div>
                    <p class="text-sm text-gray-600">Selesai</p>
                </div>            
                <p class="flex text-2xl font-semibold text-gray-800">{{ $laporanSelesaiCount }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div class="flex mb-2">
                    <div class="w-4 h-4 bg-blue-500 rounded-full mr-2"></div>
                    <span class="text-sm text-gray-600">Diproses</span>
                </div>            
                <p class="flex text-2xl font-semibold text-gray-800">{{ $laporanDiprosesCount }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
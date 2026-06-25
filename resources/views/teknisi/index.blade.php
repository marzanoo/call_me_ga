@extends('layouts.teknisi')
@section('title', 'Dashboard Teknisi')
@section('content')
<div class="container mx-auto p-4">
    @include('components.session-message')

    <div class="bg-[#B3282D] rounded-lg px-6 py-8 shadow-md text-white">
        <h2 class="text-2xl font-bold">Welcome {{ ucwords(strtolower($user->name)) }}</h2>
        <p class="text-lg opacity-90 mt-1">Tangani laporan fasilitas kantor yang sudah ditugaskan.</p>
    </div>
</div>

<div class="container mx-auto p-4">
    <div class="grid grid-cols-2 gap-4 mb-4">
        <a href="{{ route('teknisi.reports.processed.index') }}" class="bg-white p-4 rounded-lg shadow-md border border-gray-200">
            <div class="flex mb-2">
                <div class="w-4 h-4 bg-blue-500 rounded-full mr-2"></div>
                <span class="text-sm text-gray-600">Sedang Diproses</span>
            </div>
            <p class="text-2xl font-semibold text-gray-800">{{ $laporanDiprosesCount }}</p>
        </a>
        <a href="{{ route('teknisi.reports.finish.index') }}" class="bg-white p-4 rounded-lg shadow-md border border-gray-200">
            <div class="flex mb-2">
                <div class="w-4 h-4 bg-green-500 rounded-full mr-2"></div>
                <span class="text-sm text-gray-600">Selesai</span>
            </div>
            <p class="text-2xl font-semibold text-gray-800">{{ $laporanSelesaiCount }}</p>
        </a>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-md border border-gray-200">
        <h3 class="text-lg font-bold text-gray-800 mb-2">Ringkasan Tugas</h3>
        <p class="text-sm text-gray-600">Gunakan menu Tugas untuk memberi update proses atau menyelesaikan laporan dengan foto bukti.</p>
    </div>
</div>
@endsection

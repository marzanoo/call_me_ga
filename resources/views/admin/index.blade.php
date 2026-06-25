@extends('layouts.admin')
@section('title', 'Dashboard Admin')
@section('content')
<div class="container mx-auto p-4">
    @include('components.session-message')

    <div class="bg-[#B3282D] rounded-lg px-6 py-8 shadow-md text-white">
        <div class="grid grid-cols-1 items-center">
            <div>
                <h2 class="text-2xl font-bold">Welcome {{ ucwords(strtolower($user->name)) }}</h2>
                <p class="text-lg opacity-90 mt-1">
                    {{ $user->role === 2 ? 'Tangani laporan fasilitas kantor yang sudah ditugaskan.' : 'Kelola laporan fasilitas kantor dengan efisien.' }}
                </p>
            </div>
        </div>
    </div>
</div>
<div class="container mx-auto p-4">
    {{-- dashboard public button --}}
    <div class="mb-4">
        <a href="{{ route('dashboard.public') }}" class="block w-full bg-[#B3282D] text-white py-2 px-4 rounded-lg shadow-md text-center">
            Buka Dashboard Publik
        </a>
    </div>
    <div class="bg-white p-4 rounded-lg shadow-md text-center mb-4">
        <h3 class="flex text-xl font-bold text-gray-800 mb-1">Laporan Masuk</h3>
        <p class="flex text-sm text-gray-500 mb-6">{{ now()->format('Y') }}</p>
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div class="flex mb-2">
                    <div class="w-4 h-4 bg-green-500 rounded-full mr-2"></div>
                    <p class="text-sm text-gray-600">Menunggu</p>
                </div>
                <p class="flex text-2xl font-semibold text-gray-800">{{ $laporanMenungguCount }}</p>
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
    <div class="bg-white p-4 rounded-lg shadow-md text-center mb-4">
        <h3 class="flex text-xl font-bold text-gray-800 mb-1">Laporan Selesai</h3>
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
                    <div class="w-4 h-4 bg-red-500 rounded-full mr-2"></div>
                    <span class="text-sm text-gray-600">Ditolak</span>
                </div>
                <p class="flex text-2xl font-semibold text-gray-800">{{ $laporanDitolakCount }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white p-4 rounded-lg shadow-md text-center mb-4">
        <h3 class="flex text-xl font-bold text-gray-800 mb-1">Master Data</h3>
        <p class="flex text-sm text-gray-500 mb-6">Kelola data referensi sistem</p>
        <a href="{{ route('admin.master.index') }}" class="block w-full bg-[#B3282D] text-white py-2 px-4 rounded-lg shadow-md">
            Buka Master Data
        </a>
    </div>
</div>
@endsection

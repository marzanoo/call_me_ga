@extends('layouts.admin')
@section('title', 'Master Data')
@section('content')
<div class="container mx-auto p-4">
    @include('components.session-message')

    <div class="flex items-center justify-between gap-3 mb-4">
        <div>
            <h5 class="font-bold text-gray-800 text-2xl">Master Data</h5>
            <p class="text-sm text-gray-500 mt-1">Kelola data referensi dan akun sistem.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-[#B3282D] hover:underline">Kembali</a>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <a href="{{ route('admin.master.locations.index') }}" class="bg-white p-5 rounded-lg shadow-md border border-gray-100 hover:border-[#B3282D] transition">
            <p class="text-sm text-gray-500 mb-2">Total: {{ $locationCount }}</p>
            <h6 class="text-lg font-bold text-gray-800 mb-2">Master Lokasi</h6>
            <p class="text-sm text-gray-600">Kelola lokasi daerah dan gedung/lantai untuk form pelaporan.</p>
        </a>

        <a href="{{ route('admin.master.users.index') }}" class="bg-white p-5 rounded-lg shadow-md border border-gray-100 hover:border-[#B3282D] transition">
            <p class="text-sm text-gray-500 mb-2">Total: {{ $userCount }}</p>
            <h6 class="text-lg font-bold text-gray-800 mb-2">Master User</h6>
            <p class="text-sm text-gray-600">Kelola akun GA admin, teknisi, dan karyawan.</p>
        </a>
    </div>
</div>
@endsection

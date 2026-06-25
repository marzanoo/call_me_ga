@extends('layouts.teknisi')
@section('title', 'Tugas Ditolak')
@section('content')
<div class="container mx-auto p-4">
    @include('components.session-message')
    <div class="flex items-center mb-2">
        <a href="{{ route('teknisi.reports.finish.index', ['status' => 'ditolak']) }}" class="text-lg font-semibold flex items-center w-fit">
            <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>
    <h5 class="font-semibold text-gray-800 text-2xl mb-4">Tugas Ditolak</h5>
    @forelse ($reports as $report)
        <div class="bg-white p-4 rounded-lg shadow-md mb-4">
            <h5 class="text-lg font-bold text-gray-800 mb-1">{{ ucwords(strtolower($report->user->name)) }} - {{ $report->created_at->format('d M Y') }}</h5>
            <p class="text-sm text-gray-500 mb-2">Kategori: {{ $report->kategori }}</p>
            <p class="text-sm text-gray-500 mb-4">Lokasi: {{ $report->lokasi }}</p>
            <a href="{{ route('teknisi.reports.finish.declined.show', ['id' => $report->id]) }}" class="block w-full bg-[#B3282D] text-white py-2 px-4 rounded-lg shadow-md text-center">Lihat Detail</a>
        </div>
    @empty
        <div class="bg-white p-6 rounded-lg shadow-md text-center text-gray-500">Belum ada tugas ditolak.</div>
    @endforelse
</div>
@endsection

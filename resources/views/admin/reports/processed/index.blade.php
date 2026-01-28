@extends('layouts.admin')
@section('title', 'Laporan Diproses')
@section('content')
<div class="container mx-auto p-4">
    @include('components.session-message')
    <div class="flex items-center mb-2">
        <a href="{{ route('admin.reports.index') }}" class="text-lg font-semibold flex items-center w-fit">
        <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>
    <h5 class="font-semibold text-gray-800 text-2xl mb-4">Laporan Diproses</h5>
    @foreach ($reports as $report)
    <div class="bg-white p-4 rounded-lg shadow-md mb-4">
        <h5 class="flex text-lg font-bold text-gray-800 mb-1">
            {{ ucwords(strtolower($report->user->name)) }} - {{ $report->detailStatusReports->first()->status }} - {{ $report->created_at->format('d M Y') }}
        </h5>
        <p class="flex text-sm text-gray-500 mb-2">Kategori: {{ $report->kategori }}</p>
        <p class="flex text-sm text-gray-500 mb-2">Lokasi: {{ $report->lokasi }}</p>        
        <a href="{{ route('admin.reports.processed.show', ['id' => $report->id]) }}" class="btn block w-full bg-[#B3282D] text-white py-2 px-4 rounded-lg shadow-md text-center">Lihat dan Update Status</a>
    </div>
    @endforeach
</div>
@endsection
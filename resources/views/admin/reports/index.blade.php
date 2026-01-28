@extends('layouts.admin')
@section('title', 'Laporan Masuk')
@section('content')
<div class="container mx-auto p-4">
    @include('components.session-message')
    <div class="flex items-center mb-2">
        <a href="{{ route('dashboard') }}" class="text-lg font-semibold flex items-center w-fit">
        <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>
    <h5 class="font-bold text-gray-800 text-2xl mb-4">Laporan Masuk</h5>
    @if (!session('login_via_superapp'))
    <div class="flex border-b mb-4">
        <a href="{{ route('admin.reports.index', ['status' => 'menunggu']) }}"
        class="px-4 py-2 text-sm font-semibold
        {{ $status === 'menunggu' ? 'border-b-2 border-[#B3282D] text-[#B3282D]' : 'text-gray-500' }}">
            Menunggu
        </a>

        <a href="{{ route('admin.reports.index', ['status' => 'diproses']) }}"
        class="px-4 py-2 text-sm font-semibold
        {{ $status === 'diproses' ? 'border-b-2 border-[#B3282D] text-[#B3282D]' : 'text-gray-500' }}">
            Diproses
        </a>
    </div>
    <div class="bg-white p-4 rounded-lg shadow-md overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">No</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Pelapor</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Tanggal</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Status</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reports as $report)
                    @php
                        $statusData = $report->detailStatusReports->first();
                        $index = $loop->iteration + ($reports->currentPage() - 1) * $reports->perPage();
                    @endphp
                    <tr>
                        <td class="border px-4 py-2 text-sm">{{ $index }}</td>
                        <td class="border px-4 py-2 text-sm">
                            {{ ucwords(strtolower($report->user->name)) }}
                        </td>
                        <td class="border px-4 py-2 text-sm">
                            {{ $report->created_at->format('d M Y') }}
                        </td>
                        <td class="border px-4 py-2 text-sm">
                            @if ($statusData?->status === 'Menunggu')
                                <span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded-full text-xs">Menunggu</span>
                            @elseif ($statusData?->status === 'Diproses')
                                <span class="px-2 py-1 bg-blue-200 text-blue-800 rounded-full text-xs">Diproses</span>
                            @endif
                        </td>
                        <td class="border px-4 py-2 text-sm">
                            @if ($statusData?->status === 'Menunggu')
                                <a href="{{ route('admin.reports.waiting.show', $report->id) }}"
                                class="text-blue-600 hover:underline">
                                    Lihat dan Update Status
                                </a>
                            @elseif ($statusData?->status === 'Diproses')
                                <a href="{{ route('admin.reports.processed.show', $report->id) }}"
                                class="text-blue-600 hover:underline">
                                    Lihat dan Update Status
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500">
                            Tidak ada laporan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $reports->links() }}
        </div>
    </div>
    @endif
    @if (session('login_via_superapp'))
    <div class="bg-white p-4 rounded-lg shadow-md text-center mb-4">
        <h5 class="flex text-lg font-bold text-gray-800 mb-1">
            Laporan Belum Divalidasi
        </h5>
        <p class="flex text-sm text-gray-500 mb-6">Total: {{ $laporanMenungguCount }}</p>
        <a href="{{ route('admin.reports.waiting.index') }}" class="btn block w-full bg-[#B3282D] text-white py-2 px-4 rounded-lg shadow-md">Lihat</a>
    </div>
    <div class="bg-white p-4 rounded-lg shadow-md text-center mb-4">
        <h5 class="flex text-lg font-bold text-gray-800 mb-1">
            Laporan Sedang Diproses
        </h5>
        <p class="flex text-sm text-gray-500 mb-6">Total: {{ $laporanDiprosesCount }}</p>
        <a href="{{ route('admin.reports.processed.index') }}" class="btn block w-full bg-[#B3282D] text-white py-2 px-4 rounded-lg shadow-md">Lihat</a>
    </div>
    @endif
</div>
@endsection
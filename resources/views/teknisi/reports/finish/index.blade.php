@extends('layouts.teknisi')
@section('title', 'Riwayat Tugas')
@section('content')
<div class="container mx-auto p-4">
    @include('components.session-message')
    <div class="flex items-center mb-2">
        <a href="{{ route('dashboard') }}" class="text-lg font-semibold flex items-center w-fit">
            <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <h5 class="font-semibold text-gray-800 text-2xl mb-4">Riwayat Tugas</h5>

    <div class="flex border-b mb-4">
        <a href="{{ route('teknisi.reports.finish.index', ['status' => 'selesai']) }}"
           class="px-4 py-2 text-sm font-semibold {{ $status === 'selesai' ? 'border-b-2 border-[#B3282D] text-[#B3282D]' : 'text-gray-500' }}">
            Selesai
        </a>
        <a href="{{ route('teknisi.reports.finish.index', ['status' => 'ditolak']) }}"
           class="px-4 py-2 text-sm font-semibold {{ $status === 'ditolak' ? 'border-b-2 border-[#B3282D] text-[#B3282D]' : 'text-gray-500' }}">
            Ditolak
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
                        <td class="border px-4 py-2 text-sm">{{ ucwords(strtolower($report->user->name)) }}</td>
                        <td class="border px-4 py-2 text-sm">{{ $report->created_at->format('d M Y') }}</td>
                        <td class="border px-4 py-2 text-sm">
                            @if ($statusData?->status === 'Selesai')
                                <span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-xs">Selesai</span>
                            @elseif ($statusData?->status === 'Ditolak')
                                <span class="px-2 py-1 bg-red-200 text-red-800 rounded-full text-xs">Ditolak</span>
                            @endif
                        </td>
                        <td class="border px-4 py-2 text-sm">
                            @if ($statusData?->status === 'Selesai')
                                <a href="{{ route('teknisi.reports.finish.done.show', $report->id) }}" class="text-blue-600 hover:underline">Lihat Detail</a>
                            @elseif ($statusData?->status === 'Ditolak')
                                <a href="{{ route('teknisi.reports.finish.declined.show', $report->id) }}" class="text-blue-600 hover:underline">Lihat Detail</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500">Tidak ada riwayat tugas</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $reports->links() }}
        </div>
    </div>
</div>
@endsection

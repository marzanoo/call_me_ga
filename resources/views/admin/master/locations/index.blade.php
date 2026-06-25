@extends('layouts.admin')
@section('title', 'Master Lokasi')
@section('content')
<div class="container mx-auto p-4">
    @include('components.session-message')

    <div class="flex items-center justify-between gap-3 mb-4">
        <div>
            <h5 class="font-bold text-gray-800 text-2xl">Master Lokasi</h5>
            <p class="text-sm text-gray-500 mt-1">Kelola pilihan lokasi daerah dan gedung/lantai untuk form pelaporan.</p>
        </div>
        <a href="{{ route('admin.master.index') }}" class="text-sm font-semibold text-[#B3282D] hover:underline">Kembali</a>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-md border border-gray-100 mb-5">
        <h6 class="font-semibold text-gray-800 mb-3">Tambah Lokasi</h6>
        <form action="{{ route('admin.master.locations.store') }}" method="POST" class="grid gap-3 md:grid-cols-[1fr_1fr_auto] md:items-end">
            @csrf
            <div>
                <label for="area" class="block text-sm font-medium text-gray-700 mb-1">Lokasi Daerah</label>
                <input type="text" id="area" name="area" value="{{ old('area') }}" placeholder="Contoh: Jatake" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-700" required>
            </div>
            <div>
                <label for="detail" class="block text-sm font-medium text-gray-700 mb-1">Gedung atau Lantai</label>
                <input type="text" id="detail" name="detail" value="{{ old('detail') }}" placeholder="Contoh: Gedung MDTC" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-700" required>
            </div>
            <button type="submit" class="bg-[#B3282D] hover:bg-red-800 text-white px-4 py-2 rounded-lg font-semibold">
                Tambah
            </button>
        </form>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-md border border-gray-100 overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">No</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Lokasi Daerah</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Gedung/Lantai</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Status</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($locations as $location)
                    <tr>
                        <td class="border px-4 py-3 text-sm align-top">{{ $loop->iteration + ($locations->currentPage() - 1) * $locations->perPage() }}</td>
                        <td class="border px-4 py-3 align-top">
                            <form id="update-location-{{ $location->id }}" action="{{ route('admin.master.locations.update', $location->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="text" name="area" value="{{ old('area', $location->area) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
                            </form>
                        </td>
                        <td class="border px-4 py-3 align-top">
                            <input form="update-location-{{ $location->id }}" type="text" name="detail" value="{{ old('detail', $location->detail) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
                        </td>
                        <td class="border px-4 py-3 align-top">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                <input form="update-location-{{ $location->id }}" type="checkbox" name="is_active" value="1" @checked(old('is_active', $location->is_active))>
                                Aktif
                            </label>
                        </td>
                        <td class="border px-4 py-3 align-top">
                            <div class="flex flex-wrap gap-2">
                                <button form="update-location-{{ $location->id }}" type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm">
                                    Simpan
                                </button>
                                <form action="{{ route('admin.master.locations.destroy', $location->id) }}" method="POST" onsubmit="return confirm('Hapus master lokasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-gray-500">Belum ada master lokasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $locations->links() }}
        </div>
    </div>
</div>
@endsection

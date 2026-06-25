@extends('layouts.admin')
@section('title', 'Master Kategori')
@section('content')
<div class="container mx-auto p-4">
    @include('components.session-message')

    <div class="flex items-center justify-between gap-3 mb-4">
        <div>
            <h5 class="font-bold text-gray-800 text-2xl">Master Kategori</h5>
            <p class="text-sm text-gray-500 mt-1">Kelola pilihan kategori untuk form pelaporan.</p>
        </div>
        <a href="{{ route('admin.master.index') }}" class="text-sm font-semibold text-[#B3282D] hover:underline">Kembali</a>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-md border border-gray-100 mb-5">
        <h6 class="font-semibold text-gray-800 mb-3">Tambah Kategori</h6>
        <form action="{{ route('admin.master.categories.store') }}" method="POST" class="grid gap-3 md:grid-cols-[1fr_auto] md:items-end">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Lampu" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-700" required>
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
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Nama Kategori</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Status</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td class="border px-4 py-3 text-sm align-top">{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}</td>
                        <td class="border px-4 py-3 align-top">
                            <form id="update-category-{{ $category->id }}" action="{{ route('admin.master.categories.update', $category->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ old('name', $category->name) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
                            </form>
                        </td>
                        <td class="border px-4 py-3 align-top">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                <input form="update-category-{{ $category->id }}" type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active))>
                                Aktif
                            </label>
                        </td>
                        <td class="border px-4 py-3 align-top">
                            <div class="flex flex-wrap gap-2">
                                <button form="update-category-{{ $category->id }}" type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm">
                                    Simpan
                                </button>
                                <form action="{{ route('admin.master.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Hapus master kategori ini?')">
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
                        <td colspan="4" class="text-center py-5 text-gray-500">Belum ada master kategori.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection

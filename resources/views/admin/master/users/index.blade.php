@extends('layouts.admin')
@section('title', 'Master User')
@section('content')
<div class="container mx-auto p-4">
    @include('components.session-message')

    <div class="flex items-center justify-between gap-3 mb-4">
        <div>
            <h5 class="font-bold text-gray-800 text-2xl">Master User</h5>
            <p class="text-sm text-gray-500 mt-1">Kelola akun GA admin, teknisi, dan karyawan.</p>
        </div>
        <a href="{{ route('admin.master.index') }}" class="text-sm font-semibold text-[#B3282D] hover:underline">Kembali</a>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-md border border-gray-100 mb-5">
        <h6 class="font-semibold text-gray-800 mb-3">Tambah User</h6>
        <form action="{{ route('admin.master.users.store') }}" method="POST" class="grid gap-3 md:grid-cols-2">
            @csrf
            <div>
                <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                <input type="text" id="nik" name="nik" value="{{ old('nik') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
            </div>
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
            </div>
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
            </div>
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select id="role" name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                    <option value="">Pilih role</option>
                    @foreach($roleOptions as $roleValue => $roleLabel)
                        <option value="{{ $roleValue }}" @selected((string) old('role') === (string) $roleValue)>{{ $roleLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
            </div>
            <div class="md:col-span-2 flex justify-end">
                <button type="submit" class="bg-[#B3282D] hover:bg-red-800 text-white px-4 py-2 rounded-lg font-semibold">
                    Tambah
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-md border border-gray-100 overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">No</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Identitas</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Login</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Role</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Status Email</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td class="border px-4 py-3 text-sm align-top">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                        <td class="border px-4 py-3 align-top min-w-[220px]">
                            <form id="update-user-{{ $user->id }}" action="{{ route('admin.master.users.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <label class="block text-xs text-gray-500 mb-1">NIK</label>
                                <input type="text" name="nik" value="{{ old('nik', $user->nik) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm mb-2" required>
                                <label class="block text-xs text-gray-500 mb-1">Nama</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
                            </form>
                        </td>
                        <td class="border px-4 py-3 align-top min-w-[220px]">
                            <label class="block text-xs text-gray-500 mb-1">Username</label>
                            <input form="update-user-{{ $user->id }}" type="text" name="username" value="{{ old('username', $user->username) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm mb-2" required>
                            <label class="block text-xs text-gray-500 mb-1">Email</label>
                            <input form="update-user-{{ $user->id }}" type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm mb-2" required>
                            <label class="block text-xs text-gray-500 mb-1">Password Baru</label>
                            <input form="update-user-{{ $user->id }}" type="password" name="password" placeholder="Kosongkan jika tidak diganti" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </td>
                        <td class="border px-4 py-3 align-top">
                            <select form="update-user-{{ $user->id }}" name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
                                @foreach($roleOptions as $roleValue => $roleLabel)
                                    <option value="{{ $roleValue }}" @selected((int) $user->role === (int) $roleValue)>{{ $roleLabel }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="border px-4 py-3 align-top">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                <input form="update-user-{{ $user->id }}" type="checkbox" name="email_verified" value="1" @checked($user->email_verified_at)>
                                Verified
                            </label>
                        </td>
                        <td class="border px-4 py-3 align-top">
                            <div class="flex flex-wrap gap-2">
                                <button form="update-user-{{ $user->id }}" type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm">
                                    Simpan
                                </button>
                                <form action="{{ route('admin.master.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm disabled:opacity-50" @disabled($user->id === auth()->id() || $user->reports_count > 0 || $user->assigned_reports_count > 0)>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-gray-500">Belum ada user.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection

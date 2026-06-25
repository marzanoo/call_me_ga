@extends('layouts.admin')
@section('title', 'Edit Master User')
@section('content')
<div class="container mx-auto p-4">
    @include('components.session-message')

    <div class="flex items-center justify-between gap-3 mb-4">
        <div>
            <h5 class="font-bold text-gray-800 text-2xl">Edit Master User</h5>
            <p class="text-sm text-gray-500 mt-1">Ubah data akun {{ $user->name }}.</p>
        </div>
        <a href="{{ route('admin.master.users.index') }}" class="text-sm font-semibold text-[#B3282D] hover:underline">Kembali</a>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-md border border-gray-100">
        <form action="{{ route('admin.master.users.update', $user->id) }}" method="POST" class="grid gap-3 md:grid-cols-2">
            @csrf
            @method('PUT')

            <div>
                <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                <input type="text" id="nik" name="nik" value="{{ old('nik', $user->nik) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
            </div>

            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select id="role" name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                    @foreach($roleOptions as $roleValue => $roleLabel)
                        <option value="{{ $roleValue }}" @selected((string) old('role', $user->role) === (string) $roleValue)>{{ $roleLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input type="password" id="password" name="password" placeholder="Kosongkan jika tidak diganti" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>

            <div class="md:col-span-2">
                <input type="hidden" name="email_verified" value="0">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="email_verified" value="1" @checked(old('email_verified', $user->email_verified_at ? '1' : null))>
                    Email verified
                </label>
            </div>

            <div class="md:col-span-2 flex justify-end gap-2">
                <a href="{{ route('admin.master.users.index') }}" class="border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg font-semibold">
                    Batal
                </a>
                <button type="submit" class="bg-[#B3282D] hover:bg-red-800 text-white px-4 py-2 rounded-lg font-semibold">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterUserController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();

        $users = User::withCount(['reports', 'assignedReports'])
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(15);

        $roleOptions = $this->roleOptions();

        return view('admin.master.users.index', compact('users', 'roleOptions'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $data = $this->validatedData($request);
        $data['email_verified_at'] = now();
        unset($data['email_verified']);

        User::create($data);

        return redirect()->route('admin.master.users.index')->with('success', 'Master user berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeAdmin();

        $data = $this->validatedData($request, $user);
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $data['email_verified_at'] = $request->boolean('email_verified') ? ($user->email_verified_at ?? now()) : null;
        unset($data['email_verified']);

        $user->update($data);

        return redirect()->route('admin.master.users.index')->with('success', 'Master user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->authorizeAdmin();

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.master.users.index')->with('error', 'User yang sedang login tidak bisa dihapus.');
        }

        if ($user->reports()->exists() || $user->assignedReports()->exists()) {
            return redirect()->route('admin.master.users.index')->with('error', 'User yang sudah memiliki laporan atau tugas tidak bisa dihapus.');
        }

        $user->delete();

        return redirect()->route('admin.master.users.index')->with('success', 'Master user berhasil dihapus.');
    }

    private function validatedData(Request $request, ?User $user = null): array
    {
        $userId = $user?->id;
        $technicianRole = config('callmega.roles.technician');

        return $request->validate([
            'nik' => ['required', 'string', 'max:20', Rule::unique('users', 'nik')->ignore($userId)],
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($userId)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'role' => [
                'required',
                Rule::in(array_keys($this->roleOptions())),
                function ($attribute, $value, $fail) use ($technicianRole, $userId) {
                    if ((int) $value !== $technicianRole) {
                        return;
                    }

                    $technicianExists = User::where('role', $technicianRole)
                        ->when($userId, fn ($query) => $query->where('id', '!=', $userId))
                        ->exists();

                    if ($technicianExists) {
                        $fail('Akun teknisi hanya boleh satu.');
                    }
                },
            ],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:6'],
            'email_verified' => ['nullable', 'boolean'],
        ], [
            'nik.required' => 'NIK harus diisi.',
            'nik.unique' => 'NIK sudah digunakan.',
            'name.required' => 'Nama harus diisi.',
            'username.required' => 'Username harus diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email harus diisi.',
            'email.unique' => 'Email sudah digunakan.',
            'role.required' => 'Role harus dipilih.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);
    }

    private function roleOptions(): array
    {
        return [
            config('callmega.roles.admin') => 'GA Admin',
            config('callmega.roles.technician') => 'Teknisi',
            config('callmega.roles.employee') => 'Karyawan',
        ];
    }

    private function authorizeAdmin(): void
    {
        abort_if(auth()->user()?->role !== config('callmega.roles.admin'), 403);
    }
}

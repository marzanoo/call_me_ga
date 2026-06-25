<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterLocation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterLocationController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();

        $locations = MasterLocation::orderBy('area')
            ->orderBy('detail')
            ->paginate(15);

        return view('admin.master.locations.index', compact('locations'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $data = $this->validatedData($request);
        MasterLocation::create($data);

        return redirect()->route('admin.master.locations.index')->with('success', 'Master lokasi berhasil ditambahkan.');
    }

    public function update(Request $request, MasterLocation $location)
    {
        $this->authorizeAdmin();

        $data = $this->validatedData($request, $location->id);
        $location->update($data);

        return redirect()->route('admin.master.locations.index')->with('success', 'Master lokasi berhasil diperbarui.');
    }

    public function destroy(MasterLocation $location)
    {
        $this->authorizeAdmin();

        $location->delete();

        return redirect()->route('admin.master.locations.index')->with('success', 'Master lokasi berhasil dihapus.');
    }

    private function validatedData(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'area' => ['required', 'string', 'max:255'],
            'detail' => [
                'required',
                'string',
                'max:255',
                Rule::unique('master_locations', 'detail')
                    ->where(fn ($query) => $query->where('area', $request->area))
                    ->ignore($ignoreId),
            ],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'area.required' => 'Lokasi daerah harus diisi.',
            'detail.required' => 'Gedung atau lantai harus diisi.',
            'detail.unique' => 'Kombinasi lokasi daerah dan gedung/lantai sudah ada.',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function authorizeAdmin(): void
    {
        abort_if(auth()->user()?->role !== config('callmega.roles.admin'), 403);
    }
}

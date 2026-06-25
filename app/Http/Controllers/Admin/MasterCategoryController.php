<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterCategoryController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();

        $categories = MasterCategory::orderBy('name')->paginate(15);

        return view('admin.master.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        MasterCategory::create($this->validatedData($request));

        return redirect()->route('admin.master.categories.index')->with('success', 'Master kategori berhasil ditambahkan.');
    }

    public function update(Request $request, MasterCategory $category)
    {
        $this->authorizeAdmin();

        $category->update($this->validatedData($request, $category->id));

        return redirect()->route('admin.master.categories.index')->with('success', 'Master kategori berhasil diperbarui.');
    }

    public function destroy(MasterCategory $category)
    {
        $this->authorizeAdmin();

        $category->delete();

        return redirect()->route('admin.master.categories.index')->with('success', 'Master kategori berhasil dihapus.');
    }

    private function validatedData(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('master_categories', 'name')->ignore($ignoreId)],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama kategori harus diisi.',
            'name.unique' => 'Nama kategori sudah ada.',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function authorizeAdmin(): void
    {
        abort_if(auth()->user()?->role !== config('callmega.roles.admin'), 403);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterCategory;
use App\Models\MasterLocation;
use App\Models\User;

class MasterController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();

        $locationCount = MasterLocation::count();
        $categoryCount = MasterCategory::count();
        $userCount = User::count();

        return view('admin.master.index', compact('locationCount', 'categoryCount', 'userCount'));
    }

    private function authorizeAdmin(): void
    {
        abort_if(auth()->user()?->role !== config('callmega.roles.admin'), 403);
    }
}

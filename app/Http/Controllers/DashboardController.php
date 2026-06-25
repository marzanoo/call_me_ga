<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->role === 1) {
            $laporanMenungguCount = $this->latestStatusCount('Menunggu');
            $laporanDiprosesCount = $this->latestStatusCount('Diproses');
            $laporanSelesaiCount = $this->latestStatusCount('Selesai');
            $laporanDitolakCount = $this->latestStatusCount('Ditolak');

            return view('admin.index', compact('user', 'laporanMenungguCount', 'laporanDiprosesCount', 'laporanSelesaiCount', 'laporanDitolakCount'));
        } elseif ($user->role === 2) {
            $laporanMenungguCount = 0;
            $laporanDiprosesCount = $this->latestStatusCount('Diproses', $user->id);
            $laporanSelesaiCount = $this->latestStatusCount('Selesai', $user->id);
            $laporanDitolakCount = $this->latestStatusCount('Ditolak', $user->id);

            return view('teknisi.index', compact('user', 'laporanMenungguCount', 'laporanDiprosesCount', 'laporanSelesaiCount', 'laporanDitolakCount'));
        } elseif ($user->role === 3) {
            $lastReportStatus = Report::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->with([
                    'detailStatusReports' => function ($q) {
                        $q->orderBy('created_at', 'desc')->limit(1);
                    }
                ])
                ->first();
            // dd($lastReportStatus);
            $laporanDiprosesCount = $this->latestStatusCount('Menunggu', null, $user->id) + $this->latestStatusCount('Diproses', null, $user->id);
            $laporanDitolakCount = $this->latestStatusCount('Ditolak', null, $user->id);
            $laporanSelesaiCount = $this->latestStatusCount('Selesai', null, $user->id);
            return view('karyawan.index', compact('user', 'lastReportStatus', 'laporanDiprosesCount', 'laporanDitolakCount', 'laporanSelesaiCount'));
        }
        return redirect()->route('login.show')->with('error', 'Unauthorized access');
    }

    public function publicDashboard()
    {
        $laporanMenungguCount = $this->latestStatusCount('Menunggu');
        $laporanDiprosesCount = $this->latestStatusCount('Diproses');
        $laporanSelesaiCount = $this->latestStatusCount('Selesai');
        $laporanDitolakCount = $this->latestStatusCount('Ditolak');
        $totalLaporanCount = Report::count();

        $latestReports = Report::with([
            'user:id,name',
            'assignee:id,name,role',
            'detailStatusReports' => function ($q) {
                $q->select('id', 'report_id', 'status', 'keterangan', 'created_at')
                    ->orderBy('created_at', 'desc');
            },
        ])
            ->latest()
            ->limit(8)
            ->get();

        $locationSummaries = Report::query()
            ->selectRaw('COALESCE(lokasi_area, lokasi) as label, COUNT(*) as total')
            ->groupByRaw('COALESCE(lokasi_area, lokasi)')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('public-dashboard', compact(
            'laporanMenungguCount',
            'laporanDiprosesCount',
            'laporanSelesaiCount',
            'laporanDitolakCount',
            'totalLaporanCount',
            'latestReports',
            'locationSummaries'
        ));
    }

    private function latestStatusCount(string $status, ?int $assignedTo = null, ?int $userId = null): int
    {
        return Report::when($assignedTo, function ($q) use ($assignedTo) {
            $q->where('assigned_to', $assignedTo);
        })
            ->when($userId, function ($q) use ($userId) {
                $q->where('user_id', $userId);
        })
            ->whereIn('id', function ($q) use ($status) {
                $q->select('report_id')
                    ->from('detail_status_reports')
                    ->where('status', $status)
                    ->whereIn('id', function ($sub) {
                        $sub->selectRaw('MAX(id)')
                            ->from('detail_status_reports')
                            ->groupBy('report_id');
                    });
            })->count();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

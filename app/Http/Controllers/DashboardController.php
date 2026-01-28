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
            $laporanMenungguCount = Report::whereIn('id', function ($q) {
                $q->select('report_id')
                    ->from('detail_status_reports')
                    ->where('status', 'Menunggu')
                    ->whereIn('id', function ($sub) {
                        $sub->selectRaw('MAX(id)')
                            ->from('detail_status_reports')
                            ->groupBy('report_id');
                    });
            })->count();

            $laporanDiprosesCount = Report::whereIn('id', function ($q) {
                $q->select('report_id')
                    ->from('detail_status_reports')
                    ->where('status', 'Diproses')
                    ->whereIn('id', function ($sub) {
                        $sub->selectRaw('MAX(id)')
                            ->from('detail_status_reports')
                            ->groupBy('report_id');
                    });
            })->count();

            $laporanSelesaiCount = Report::whereIn('id', function ($q) {
                $q->select('report_id')
                    ->from('detail_status_reports')
                    ->where('status', 'Selesai')
                    ->whereIn('id', function ($sub) {
                        $sub->selectRaw('MAX(id)')
                            ->from('detail_status_reports')
                            ->groupBy('report_id');
                    });
            })->count();

            $laporanDitolakCount = Report::whereIn('id', function ($q) {
                $q->select('report_id')
                    ->from('detail_status_reports')
                    ->where('status', 'Ditolak')
                    ->whereIn('id', function ($sub) {
                        $sub->selectRaw('MAX(id)')
                            ->from('detail_status_reports')
                            ->groupBy('report_id');
                    });
            })->count();

            return view('admin.index', compact('user', 'laporanMenungguCount', 'laporanDiprosesCount', 'laporanSelesaiCount', 'laporanDitolakCount'));
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
            $laporanDiprosesCount = Report::where('user_id', $user->id)
                ->whereHas('detailStatusReports', function ($query) {
                    $query->where('status', '!=', 'Selesai')->where('status', '!=', 'Ditolak');
                })
                ->count();
            $laporanDitolakCount = Report::where('user_id', $user->id)
                ->whereHas('detailStatusReports', function ($query) {
                    $query->where('status', 'Ditolak');
                })
                ->count();
            $laporanSelesaiCount = Report::where('user_id', $user->id)
                ->whereHas('detailStatusReports', function ($query) {
                    $query->where('status', 'Selesai');
                })
                ->count();
            return view('karyawan.index', compact('user', 'lastReportStatus', 'laporanDiprosesCount', 'laporanDitolakCount', 'laporanSelesaiCount'));
        }
        return redirect()->route('login.show')->with('error', 'Unauthorized access');
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

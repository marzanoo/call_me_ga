<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\DetailFotoReportSelesai;
use App\Models\Report;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class AdminReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->ensureTechnicianRouteAccess();

        $isTechnician = auth()->user()?->role === config('callmega.roles.technician');
        $status = $request->get('status', $isTechnician ? 'diproses' : 'menunggu'); // default

        $statusMap = [
            'menunggu' => 'Menunggu',
            'diproses' => 'Diproses',
        ];

        $currentStatus = $statusMap[$status] ?? 'Menunggu';

        $reports = Report::with([
            'detailFotoReports:image_path,report_id',
            'detailStatusReports' => function ($q) {
                $q->select('id', 'report_id', 'status', 'keterangan', 'created_at')
                    ->orderBy('created_at', 'desc');
            },
            'user:id,name'
        ])
            ->when($isTechnician, function ($q) {
                $q->where('assigned_to', auth()->id());
            })
            ->whereHas('detailStatusReports', function ($q) use ($currentStatus) {
                $q->where('status', $currentStatus)
                    ->whereRaw('detail_status_reports.id = (
              SELECT id FROM detail_status_reports
              WHERE report_id = reports.id
              ORDER BY created_at DESC
              LIMIT 1
          )');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $laporanMenungguCount = $this->latestStatusCount('Menunggu', $isTechnician ? auth()->id() : null);
        $laporanDiprosesCount = $this->latestStatusCount('Diproses', $isTechnician ? auth()->id() : null);

        if ($isTechnician) {
            return view('teknisi.reports.processed.index', compact('reports'));
        }

        return view('admin.reports.index', compact('reports', 'status', 'laporanMenungguCount', 'laporanDiprosesCount'));
    }

    public function finishIndex(Request $request)
    {
        $this->ensureTechnicianRouteAccess();

        $isTechnician = auth()->user()?->role === config('callmega.roles.technician');
        $status = $request->get('status', 'selesai'); // default

        $statusMap = [
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
        ];

        $currentStatus = $statusMap[$status] ?? 'Selesai';

        $reports = Report::with([
            'detailFotoReports:image_path,report_id',
            'detailStatusReports' => function ($q) {
                $q->select('id', 'report_id', 'status', 'keterangan', 'created_at')
                    ->orderBy('created_at', 'desc');
            },
            'user:id,name'
        ])
            ->when($isTechnician, function ($q) {
                $q->where('assigned_to', auth()->id());
            })
            ->whereHas('detailStatusReports', function ($q) use ($currentStatus) {
                $q->where('status', $currentStatus)
                    ->whereRaw('detail_status_reports.id = (
              SELECT id FROM detail_status_reports
              WHERE report_id = reports.id
              ORDER BY created_at DESC
              LIMIT 1
          )');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $laporanSelesaiCount = $this->latestStatusCount('Selesai', $isTechnician ? auth()->id() : null);
        $laporanDitolakCount = $this->latestStatusCount('Ditolak', $isTechnician ? auth()->id() : null);
        if ($isTechnician) {
            return view('teknisi.reports.finish.index', compact('reports', 'status', 'laporanSelesaiCount', 'laporanDitolakCount'));
        }

        return view('admin.reports.finish.index', compact('reports', 'status', 'laporanSelesaiCount', 'laporanDitolakCount'));
    }

    public function finishDoneIndex()
    {
        $this->ensureTechnicianRouteAccess();

        $isTechnician = auth()->user()?->role === config('callmega.roles.technician');
        $reports = Report::with([
            'detailFotoReports:image_path,report_id',
            'detailStatusReports' => function ($q) {
                $q->select('id', 'report_id', 'status', 'keterangan', 'created_at')
                    ->orderBy('created_at', 'desc');
            },
            'user:id,name'
        ])
            ->whereHas('detailStatusReports', function ($q) {
                $q->where('status', 'Selesai')
                    ->whereRaw('detail_status_reports.id = (
              SELECT id FROM detail_status_reports
              WHERE report_id = reports.id
              ORDER BY created_at DESC
              LIMIT 1
          )');
            })
            ->when($isTechnician, function ($q) {
                $q->where('assigned_to', auth()->id());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        if ($isTechnician) {
            return view('teknisi.reports.finish.done.index', compact('reports'));
        }

        return view('admin.reports.finish.done.index', compact('reports'));
    }

    public function finishDoneShow($id)
    {
        $this->ensureTechnicianRouteAccess();

        $report = Report::with([
            'detailFotoReports:image_path,report_id',
            'detailFotoReportSelesais:image_path,report_id',
            'detailStatusReports' => function ($q) {
                $q->select('id', 'report_id', 'created_by', 'status', 'keterangan')
                    ->orderBy('created_at', 'desc')
                    ->limit(1);
            },
            'user:id,name',
            'assignee:id,name,role',
        ])
            ->where('id', $id)
            ->firstOrFail();

        $this->authorizeReportAccess($report);

        if (auth()->user()?->role === config('callmega.roles.technician')) {
            return view('teknisi.reports.finish.done.display', compact('report'));
        }

        return view('admin.reports.finish.done.display', compact('report'));
    }

    public function finishDeclinedIndex()
    {
        $this->ensureTechnicianRouteAccess();

        $isTechnician = auth()->user()?->role === config('callmega.roles.technician');
        $reports = Report::with([
            'detailFotoReports:image_path,report_id',
            'detailStatusReports' => function ($q) {
                $q->select('id', 'report_id', 'status', 'keterangan', 'created_at')
                    ->orderBy('created_at', 'desc');
            },
            'user:id,name'
        ])
            ->whereHas('detailStatusReports', function ($q) {
                $q->where('status', 'Ditolak')
                    ->whereRaw('detail_status_reports.id = (
              SELECT id FROM detail_status_reports
              WHERE report_id = reports.id
              ORDER BY created_at DESC
              LIMIT 1
          )');
            })
            ->when($isTechnician, function ($q) {
                $q->where('assigned_to', auth()->id());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        if ($isTechnician) {
            return view('teknisi.reports.finish.declined.index', compact('reports'));
        }

        return view('admin.reports.finish.declined.index', compact('reports'));
    }

    public function finishDeclinedShow($id)
    {
        $this->ensureTechnicianRouteAccess();

        $report = Report::with([
            'detailFotoReports:image_path,report_id',
            'detailStatusReports' => function ($q) {
                $q->select('id', 'report_id', 'status', 'keterangan', 'feedback')
                    ->orderBy('created_at', 'desc')
                    ->limit(1);
            },
            'user:id,name'
        ])
            ->where('id', $id)
            ->firstOrFail();

        $this->authorizeReportAccess($report);

        if (auth()->user()?->role === config('callmega.roles.technician')) {
            return view('teknisi.reports.finish.declined.display', compact('report'));
        }

        return view('admin.reports.finish.declined.display', compact('report'));
    }

    public function processedIndex()
    {
        $this->ensureTechnicianRouteAccess();

        $isTechnician = auth()->user()?->role === config('callmega.roles.technician');
        $reports = Report::with([
            'detailFotoReports:image_path,report_id',
            'detailStatusReports' => function ($q) {
                $q->select('id', 'report_id', 'created_by', 'status', 'keterangan', 'created_at')
                    ->orderBy('created_at', 'desc');
            },
            'user:id,name',
            'assignee:id,name,role',
        ])
            ->when($isTechnician, function ($q) {
                $q->where('assigned_to', auth()->id());
            })
            ->whereHas('detailStatusReports', function ($q) {
                $q->where('status', 'Diproses')
                    ->whereRaw('detail_status_reports.id = (
              SELECT id FROM detail_status_reports
              WHERE report_id = reports.id
              ORDER BY created_at DESC
              LIMIT 1
          )');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        if ($isTechnician) {
            return view('teknisi.reports.processed.index', compact('reports'));
        }

        return view('admin.reports.processed.index', compact('reports'));
    }

    public function processedShow($id)
    {
        $this->ensureTechnicianRouteAccess();

        $report = Report::with([
            'detailFotoReports:image_path,report_id',
            'detailStatusReports' => function ($q) {
                $q->select('id', 'report_id', 'created_by', 'status', 'keterangan', 'created_at')
                    ->orderBy('created_at', 'desc')
                    ->limit(1);
            },
            'user:id,name',
            'assignee:id,name,role',
        ])
            ->where('id', $id)
            ->firstOrFail();

        $this->authorizeReportAccess($report);

        if (auth()->user()?->role === config('callmega.roles.technician')) {
            return view('teknisi.reports.processed.display', compact('report'));
        }

        return view('admin.reports.processed.display', compact('report'));
    }

    public function processedUpdateStatus(Request $request, $id)
    {
        $this->ensureTechnicianRouteAccess();

        $request->validate(
            [
                'status' => 'required|in:Diproses,Selesai',
                'keterangan' => 'required|string|max:1000',
                'buktiFoto' => 'required_if:status,Selesai|array',
                'buktiFoto.*' => 'image|mimes:jpeg,png,jpg,gif|max:5012', //maks 5 mb
            ],
            [
                'keterangan.required' => 'Keterangan status harus diisi.',
                'buktiFoto.required_if' => 'Bukti foto wajib diunggah saat status diselesaikan.',
                'buktiFoto.*.image' => 'Bukti foto harus berupa gambar.',
                'buktiFoto.*.max' => 'Bukti foto tidak boleh lebih dari 10MB.',
            ]
        );

        DB::beginTransaction();
        try {
            $report = Report::findOrFail($id);
            $this->authorizeReportAccess($report);

            $report->detailStatusReports()->create([
                'created_by' => auth()->id(),
                'status' => $request->status,
                'keterangan' => $request->keterangan,
            ]);

            // Simpan foto bukti jika statusnya Selesai
            if ($request->status === 'Selesai' && $request->hasFile('buktiFoto')) {
                foreach ($request->file('buktiFoto') as $foto) {
                    // Simpan foto bukti ke storage
                    $imagePath = $this->compressAndStoreImage($foto, $report->id);

                    Log::info('Saving completed report photo:', ['path' => $imagePath]);

                    DetailFotoReportSelesai::create([
                        'report_id' => $report->id,
                        'image_path' => $imagePath,
                    ]);
                }
            }

            DB::commit();
            Log::info('Report status updated successfully:', ['report_id' => $report->id, 'new_status' => $request->status]);
            $redirectRoute = auth()->user()?->role === config('callmega.roles.technician')
                ? 'teknisi.reports.processed.index'
                : 'admin.reports.processed.index';

            return redirect()->route($redirectRoute)->with('success', 'Status laporan berhasil diperbarui.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating report status:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui status laporan.');
        }
    }

    private function compressAndStoreImage($imageFile, $reportId)
    {
        try {
            // Generate a unique filename
            $fileName = time() . '_' . uniqid() . '.jpg';
            $directory = 'reports/' . $reportId;
            $fullPath = storage_path('app/public/' . $directory);

            Log::info('Compressing image:', [
                'filename' => $fileName,
                'directory' => $directory,
                'fullPath' => $fullPath
            ]);

            // Create directory if not exists
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0755, true);
                Log::info('Directory created:', ['path' => $fullPath]);
            }

            // Create ImageManager
            $manager = new ImageManager(new Driver());

            // Read image
            $image = $manager->read($imageFile);
            Log::info('Image loaded successfully');

            // Resize the image to a width of 1920 and constrain aspect ratio (auto height)
            $image->scale(width: 1920);
            Log::info('Image resized');

            // Compress with standard quality until file size is under 2MB
            $quality = 90;
            $targetSize = 2 * 1024 * 1024; // 2MB
            $filePath = $directory . '/' . $fileName;
            $fullFilePath = $fullPath . '/' . $fileName;

            $attempts = 0;
            do {
                $attempts++;

                // Encoding the image to the desired quality
                $encoded = $image->toJpeg($quality);
                file_put_contents($fullFilePath, $encoded);

                // Check the file size
                $fileSize = filesize($fullFilePath);

                Log::info("Compression attempt {$attempts}:", [
                    'quality' => $quality,
                    'fileSize' => $fileSize,
                    'targetSize' => $targetSize
                ]);

                // If file size is still larger than target, reduce quality
                if ($fileSize > $targetSize) {
                    $quality -= 5; // Decrease quality by 5%
                }

                // Break if quality is too low or max attempts
                if ($quality < 20 || $attempts > 15) {
                    Log::warning('Compression stopped:', [
                        'quality' => $quality,
                        'attempts' => $attempts,
                        'finalSize' => $fileSize
                    ]);
                    break;
                }
            } while ($fileSize > $targetSize);

            Log::info('Image compression complete:', [
                'path' => $filePath,
                'finalQuality' => $quality,
                'finalSize' => filesize($fullFilePath)
            ]);

            return $filePath;
        } catch (Exception $e) {
            Log::error('Error in compressAndStoreImage:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }


    //Waiting Reports
    public function waitingIndex()
    {
        abort_if(auth()->user()?->role === config('callmega.roles.technician'), 403);

        $reports = Report::with([
            'detailFotoReports:image_path,report_id',
            'detailStatusReports' => function ($q) {
                $q->select('id', 'report_id', 'status', 'keterangan', 'created_at')
                    ->orderBy('created_at', 'desc');
            },
            'user:id,name'
        ])
            ->whereHas('detailStatusReports', function ($q) {
                $q->where('status', 'Menunggu')
                    ->whereRaw('detail_status_reports.id = (
              SELECT id FROM detail_status_reports
              WHERE report_id = reports.id
              ORDER BY created_at DESC
              LIMIT 1
          )');
            })
            ->orderBy('created_at', 'desc')
            ->get();
        // dd($reports);
        return view('admin.reports.waiting.index', compact('reports'));
    }

    public function waitingShow($id)
    {
        abort_if(auth()->user()?->role === config('callmega.roles.technician'), 403);

        $report = Report::with([
            'detailFotoReports:image_path,report_id',
            'detailStatusReports' => function ($q) {
                $q->select('id', 'report_id', 'status', 'keterangan')
                    ->orderBy('created_at', 'desc')
                    ->limit(1);
            },
            'user:id,name'
        ])
            ->where('id', $id)
            ->firstOrFail();

        $technician = User::where('role', config('callmega.roles.technician'))->first();

        return view('admin.reports.waiting.display', compact('report', 'technician'));
    }

    public function waitingUpdateStatus(Request $request, $id)
    {
        abort_if(auth()->user()?->role === config('callmega.roles.technician'), 403);

        $request->validate([
            'status' => 'required|in:Menunggu,Diproses,Selesai,Ditolak',
            'assignee_type' => 'required_if:status,Diproses|in:ga,technician',
            'keterangan' => 'required_if:status,Diproses|string|max:1000',
            'feedback' => 'required_if:status,Ditolak|string|max:255',
        ], [
            'assignee_type.required_if' => 'Pilih tujuan penugasan laporan.',
            'keterangan.required_if' => 'Keterangan verifikasi harus diisi.',
            'feedback.required_if' => 'Alasan penolakan harus diisi.',
        ]);

        $report = Report::findOrFail($id);
        $assignedTo = $report->assigned_to;

        if ($request->status === 'Diproses') {
            if ($request->assignee_type === 'technician') {
                $technician = User::where('role', config('callmega.roles.technician'))->first();

                if (!$technician) {
                    return redirect()->back()->withInput()->with('error', 'Akun teknisi belum tersedia. Jalankan seeder teknisi terlebih dahulu.');
                }

                $assignedTo = $technician->id;
            } else {
                $assignedTo = auth()->id();
            }

            $report->update(['assigned_to' => $assignedTo]);
        }

        // Mapping keterangan berdasarkan status
        $keteranganMap = [
            'Menunggu' => 'Laporan telah diterima dan menunggu proses verifikasi.',
            'Diproses' => 'Laporan telah diverifikasi dan sedang dilakukan tindakan.',
            'Selesai'  => 'Laporan telah selesai ditangani.',
            'Ditolak'  => 'Laporan ditolak dan dibatalkan.',
        ];

        $report->detailStatusReports()->create([
            'created_by' => auth()->id(),
            'status'     => $request->status,
            'keterangan' => $request->keterangan ?? $keteranganMap[$request->status],
            'feedback'   => $request->feedback
        ]);

        return redirect()
            ->route('admin.reports.index')
            ->with('success', 'Status laporan berhasil diperbarui.');
    }

    private function latestStatusCount(string $status, ?int $assignedTo = null): int
    {
        return Report::when($assignedTo, function ($q) use ($assignedTo) {
            $q->where('assigned_to', $assignedTo);
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

    private function authorizeReportAccess(Report $report): void
    {
        if (auth()->user()?->role === config('callmega.roles.technician') && $report->assigned_to !== auth()->id()) {
            abort(403);
        }
    }

    private function ensureTechnicianRouteAccess(): void
    {
        if (request()->routeIs('teknisi.*') && auth()->user()?->role !== config('callmega.roles.technician')) {
            abort(403);
        }
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

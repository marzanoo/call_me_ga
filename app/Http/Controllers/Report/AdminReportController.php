<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\DetailFotoReportSelesai;
use App\Models\Report;
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
        $status = $request->get('status', 'menunggu'); // default

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

        return view('admin.reports.index', compact('reports', 'status', 'laporanMenungguCount', 'laporanDiprosesCount'));
    }

    public function finishIndex(Request $request)
    {
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
        return view('admin.reports.finish.index', compact('reports', 'status', 'laporanSelesaiCount', 'laporanDitolakCount'));
    }

    public function finishDoneIndex()
    {
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
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.reports.finish.done.index', compact('reports'));
    }

    public function finishDoneShow($id)
    {
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
            ->first();
        return view('admin.reports.finish.done.display', compact('report'));
    }

    public function finishDeclinedIndex()
    {
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
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.reports.finish.declined.index', compact('reports'));
    }

    public function finishDeclinedShow($id)
    {
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
            ->first();
        return view('admin.reports.finish.declined.display', compact('report'));
    }

    public function processedIndex()
    {
        $reports = Report::with([
            'detailFotoReports:image_path,report_id',
            'detailStatusReports' => function ($q) {
                $q->select('id', 'report_id', 'status', 'keterangan', 'created_at')
                    ->orderBy('created_at', 'desc');
            },
            'user:id,name'
        ])
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

        return view('admin.reports.processed.index', compact('reports'));
    }

    public function processedShow($id)
    {
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
            ->first();
        return view('admin.reports.processed.display', compact('report'));
    }

    public function processedUpdateStatus(Request $request, $id)
    {
        $request->validate(
            [
                'status' => 'required|in:Menunggu,Diproses,Selesai,Ditolak',
                'buktiFoto' => 'required|array',
                'buktiFoto.*' => 'image|mimes:jpeg,png,jpg,gif|max:5012', //maks 5 mb
            ],
            [
                'buktiFoto.*.image' => 'Bukti foto harus berupa gambar.',
                'buktiFoto.*.max' => 'Bukti foto tidak boleh lebih dari 10MB.',
            ]
        );

        DB::beginTransaction();
        try {
            $report = Report::findOrFail($id);

            $report->detailStatusReports()->create([
                'status' => $request->status,
                'keterangan' => 'Laporan telah selesai ditangani. Terima kasih atas laporan Anda.',
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
            return redirect()->route('admin.reports.processed.index')->with('success', 'Status laporan berhasil diperbarui.');
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
            ->first();
        return view('admin.reports.waiting.display', compact('report'));
    }

    public function waitingUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Menunggu,Diproses,Selesai,Ditolak',
        ]);

        $report = Report::findOrFail($id);

        // Mapping keterangan berdasarkan status
        $keteranganMap = [
            'Menunggu' => 'Laporan telah diterima dan menunggu proses verifikasi.',
            'Diproses' => 'Laporan telah diverifikasi dan sedang dilakukan tindakan.',
            'Selesai'  => 'Laporan telah selesai ditangani.',
            'Ditolak'  => 'Laporan ditolak dan dibatalkan.',
        ];

        $report->detailStatusReports()->create([
            'status'     => $request->status,
            'keterangan' => $request->keterangan ?? $keteranganMap[$request->status],
        ]);

        return redirect()
            ->route('admin.reports.index')
            ->with('success', 'Status laporan berhasil diperbarui.');
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

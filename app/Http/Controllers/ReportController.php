<?php

namespace App\Http\Controllers;

use App\Models\DetailFotoReport;
use App\Models\DetailStatusReport;
use App\Models\Report;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('karyawan.reports.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug: Log semua data yang masuk
        Log::info('Request Data:', $request->all());
        Log::info('Files:', $request->allFiles());

        $request->validate(
            [
                'user_id' => 'required|exists:users,id',
                'lokasi' => 'required|string|max:255',
                'kategori' => 'required|string|max:255',
                'permasalahan' => 'required|string',
                'foto' => 'nullable|array',
                'foto.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240', // Max 10MB sebelum compress
            ],
            [
                'user_id.required' => 'User ID harus diisi.',
                'user_id.exists' => 'User tidak ditemukan.',
                'lokasi.required' => 'Lokasi harus diisi.',
                'lokasi.max' => 'Lokasi tidak boleh lebih dari 255 karakter.',
                'kategori.required' => 'Kategori harus diisi.',
                'kategori.max' => 'Kategori tidak boleh lebih dari 255 karakter.',
                'permasalahan.required' => 'Permasalahan harus diisi.',
                'foto.array' => 'Format foto tidak valid.',
                'foto.*.image' => 'File harus berupa gambar.',
                'foto.*.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
                'foto.*.max' => 'Ukuran gambar maksimal 10MB.',
            ]
        );

        DB::beginTransaction();
        try {
            // Create report
            $report = Report::create([
                'user_id' => $request->user_id,
                'tanggal' => now()->toDateString(),
                'lokasi' => $request->lokasi,
                'kategori' => $request->kategori,
                'permasalahan' => $request->permasalahan,
            ]);

            Log::info('Report created:', ['id' => $report->id]);

            // Status for report
            $status = DetailStatusReport::create([
                'report_id' => $report->id,
                'status' => 'Pending',
                'keterangan' => 'Laporan telah diterima dan menunggu proses verifikasi.',
            ]);

            Log::info('DetailStatusReport created:', ['id' => $status->id, 'status' => $status->status]);

            // Handle foto upload if exists
            if ($request->hasFile('foto')) {
                Log::info('Processing images...');

                foreach ($request->file('foto') as $index => $foto) {
                    try {
                        // Compress and store the image
                        $imagePath = $this->compressAndStoreImage($foto, $report->id);

                        Log::info("Image {$index} saved:", ['path' => $imagePath]);

                        DetailFotoReport::create([
                            'report_id' => $report->id,
                            'image_path' => $imagePath,
                        ]);
                    } catch (Exception $e) {
                        Log::error("Error processing image {$index}:", ['error' => $e->getMessage()]);
                        throw $e;
                    }
                }
            }

            DB::commit();
            Log::info('Report stored successfully');

            return redirect()->route('dashboard')->with('success', 'Laporan berhasil dikirim.');
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error storing report:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Hapus file yang sudah ter-upload jika ada error
            if (isset($report)) {
                $images = DetailFotoReport::where('report_id', $report->id)->get();
                foreach ($images as $img) {
                    Storage::disk('public')->delete($img->image_path);
                }
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Compress and store the image
     */
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

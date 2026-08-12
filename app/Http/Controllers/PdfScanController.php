<?php

namespace App\Http\Controllers;

use App\Exports\PdfScanExport;
use App\Http\Requests\ScanRequest;
use App\Models\Aircraft;
use App\Models\ActivityLog;
use App\Models\Seat;
use App\Services\PdfParserService;
use App\Services\VerificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class PdfScanController extends Controller
{
    protected PdfParserService $pdfParser;

    protected VerificationService $verificationService;

    public function __construct(PdfParserService $pdfParser, VerificationService $verificationService)
    {
        $this->pdfParser = $pdfParser;
        $this->verificationService = $verificationService;
    }

    public function index()
    {
        if (session()->has('pdf_scan_result')) {
            $data = session('pdf_scan_result');

            return view('admin.pdf-scan-review', $data);
        }

        return view('admin.pdf-scan');
    }

    public function clearScan()
    {
        session()->forget('pdf_scan_result');

        return redirect()->route('admin.pdf-scan');
    }

    public function scan(ScanRequest $request)
    {

        $file = $request->file('file');

        // Try to store the file and handle storage error
        try {
            $path = $file->store('temp_scans');
            if (! $path) {
                throw new \Exception('Gagal menyimpan file ke temporary storage.');
            }
        } catch (\Exception $e) {
            Log::error('[PDF Scan] File storage failed', ['error' => $e->getMessage()]);

            return redirect()->back()->with('error', 'Gagal menyimpan file yang diunggah. Silakan periksa ruang penyimpanan server Anda.');
        }

        $fullPath = storage_path('app/private/'.$path);
        Log::info('[PDF Scan] Starting scan', ['file' => $file->getClientOriginalName(), 'size' => $file->getSize()]);

        try {
            // Verify file readability/validity before processing
            if (! file_exists($fullPath) || ! is_readable($fullPath) || filesize($fullPath) === 0) {
                throw new \Exception('File tidak dapat dibaca atau berukuran 0 bytes.');
            }

            // Quick validation for allowed extensions and corrupted files
            $extension = strtolower($file->getClientOriginalExtension());
            if (! in_array($extension, ['pdf', 'jpeg', 'jpg', 'png'])) {
                throw new \Exception('Format file tidak didukung. Gunakan PDF, JPG, atau PNG.');
            }

            if (in_array($extension, ['jpeg', 'jpg', 'png'])) {
                $imgTest = @imagecreatefromstring(file_get_contents($fullPath));
                if ($imgTest === false) {
                    throw new \Exception('File gambar rusak atau corrupt.');
                }
                imagedestroy($imgTest);
            } elseif ($extension === 'pdf') {
                // Read first few bytes of PDF to check signature
                $handle = @fopen($fullPath, 'r');
                if ($handle) {
                    $header = fread($handle, 4);
                    fclose($handle);
                    if ($header !== '%PDF') {
                        throw new \Exception('File PDF tidak valid atau header corrupt.');
                    }
                } else {
                    throw new \Exception('Gagal membuka file PDF.');
                }
            }

            // processFile now returns the parsed data array directly (from AI)
            $parsed = $this->pdfParser->processFile($fullPath);

            Storage::delete($path);

            // Ensure each seat item has a 'registration' key
            $registration = $parsed['registration'] ?? 'PENDING';
            $seats = array_map(function ($seat) use ($registration) {
                $seat['registration'] = $seat['registration'] ?? $registration;

                return $seat;
            }, $parsed['seats'] ?? []);

            // ===== SMART VERIFICATION: Per-Aircraft Layout Validation =====
            // Pass through VerificationService for row validation based on aircraft layout
            $verificationResult = $this->verificationService->verify([
                'registration' => $registration,
                'aircraft_type' => $parsed['aircraft_type'] ?? 'Unknown',
                'seats' => $seats,
            ]);

            $registration = $verificationResult['registration'] ?? 'PENDING';
            $seats = $verificationResult['seats'] ?? [];

            // Ensure each seat has the registration key (required by view)
            $seats = array_map(function ($seat) use ($registration) {
                $seat['registration'] = $seat['registration'] ?? $registration;

                return $seat;
            }, $seats);

            Log::info('[PDF Scan] Scan complete with per-aircraft validation', [
                'registration' => $registration,
                'aircraft_type' => $verificationResult['aircraft_type'],
                'seats_count' => count($seats),
                'flagged_count' => $verificationResult['summary']['flagged'] ?? 0,
            ]);

            // Detect active provider for display
            $activeProvider = 'Unknown AI';
            if (! empty(env('FLAZ_API_KEY'))) {
                $activeProvider = 'Flaz.id ('.env('FLAZ_MODEL', 'claude-sonnet-4-6').')';
            } elseif (! empty(env('SNIFOX_API_KEY'))) {
                $activeProvider = 'Snifox ('.env('SNIFOX_MODEL', 'claude-sonnet-4-6').')';
            } elseif (! empty(env('GEMINI_API_KEY'))) {
                $activeProvider = 'Google Gemini';
            } elseif (! empty(env('ANTHROPIC_API_KEY'))) {
                $activeProvider = 'Anthropic Claude';
            } elseif (! empty(env('OPENAI_API_KEY'))) {
                $activeProvider = 'OpenAI GPT-4o';
            } elseif (! empty(env('OPENROUTER_API_KEY'))) {
                $activeProvider = 'OpenRouter';
            }

            // Check if refinement was applied
            $refinementApplied = $parsed['refinement_applied'] ?? false;
            $refinementModel = $parsed['refinement_model'] ?? '';
            $refinementCorrections = $parsed['refinement_corrections'] ?? 0;

            if ($refinementApplied) {
                $activeProvider .= " → {$refinementModel}";
            }

            $rawText = "Data diekstrak menggunakan AI ({$activeProvider})\n";
            if ($refinementApplied) {
                $rawText .= "🔄 Multi-Stage OCR: Stage 2 refinement aktif ({$refinementModel})\n";
                $rawText .= "   Koreksi dari refinement: {$refinementCorrections} field\n";
            }
            $rawText .= "Registration: {$registration}\n";
            $rawText .= 'Aircraft Type: '.($verificationResult['aircraft_type'] ?? 'Unknown')."\n";
            $rawText .= 'Total seats terdeteksi: '.count($seats)."\n";

            // Get aircraft layout info
            $aircraft = Aircraft::where('registration', $registration)->first();
            if ($aircraft) {
                $rawText .= 'Layout: '.$aircraft->layout."\n";
                $classRowsConfig = config('aircraft_class_rows');
                if (isset($classRowsConfig[$aircraft->layout])) {
                    $layoutInfo = $classRowsConfig[$aircraft->layout];
                    $rawText .= "\n📋 Expected Layout Structure:\n";
                    foreach ($layoutInfo as $class => $rows) {
                        if (is_array($rows) && ! empty($rows)) {
                            $rowStr = is_array($rows) && count($rows) > 1
                                ? 'rows '.min($rows).'-'.max($rows)
                                : 'row '.current($rows);
                            $rowCount = count($rows);
                            $rawText .= "  • {$class}: {$rowStr} ({$rowCount} rows)\n";
                        }
                    }
                }
            }

            if (! empty($verificationResult['summary']['flagged'])) {
                $rawText .= "\n⚠️  ".$verificationResult['summary']['flagged']." seats flagged for review (possible row/layout mismatches).\n";
            }

            if (empty($seats)) {
                $rawText .= "\n⚠ AI tidak mendeteksi data seats.\n";
                $rawText .= "Kemungkinan penyebab:\n";
                $rawText .= "- Kualitas gambar/scan terlalu rendah\n";
                $rawText .= "- Format dokumen tidak dikenali\n";
                $rawText .= "- API sedang bermasalah\n";
                $rawText .= "\nSilakan cek storage/logs/laravel.log untuk detail.";
            }

            $result = [
                'rawText' => $rawText,
                'registration' => $registration,
                'aircraftType' => $verificationResult['aircraft_type'] ?? 'Unknown',
                'extractedData' => $seats,
                'scanImages' => $parsed['scan_images'] ?? [],
                'verificationSummary' => $verificationResult['summary'] ?? [],
            ];

            // Simpan ke session agar tidak hilang saat navigasi
            session(['pdf_scan_result' => $result]);

            return view('admin.pdf-scan-review', $result);
        } catch (ConnectionException $e) {
            Storage::delete($path);
            Log::error('[PDF Scan] Connection timeout', ['error' => $e->getMessage()]);

            return redirect()->back()->with('error', '⏱ Koneksi timeout — server AI tidak merespon dalam waktu yang ditentukan. Coba lagi dalam beberapa menit atau gunakan file dengan ukuran lebih kecil.');
        } catch (\Exception $e) {
            Storage::delete($path);
            Log::error('[PDF Scan] Scan failed', ['error' => $e->getMessage()]);

            $msg = $e->getMessage();
            // Detect specific error types for better user messaging
            if (str_contains($msg, 'API Error (HTTP 401)') || str_contains($msg, 'API Error (HTTP 403)')) {
                $userMsg = 'API Key tidak valid atau tidak memiliki akses. Periksa konfigurasi API key di file .env.';
            } elseif (str_contains($msg, 'API Error (HTTP 429)')) {
                $userMsg = 'Rate limit tercapai — terlalu banyak request ke AI. Tunggu 1-2 menit lalu coba lagi.';
            } elseif (str_contains($msg, 'API Error (HTTP 5')) {
                $userMsg = 'Server AI sedang bermasalah (error 5xx). Coba lagi dalam beberapa menit.';
            } elseif (str_contains($msg, 'Ghostscript') || str_contains($msg, 'Gagal memproses PDF')) {
                $userMsg = 'Gagal mengkonversi PDF ke gambar. Pastikan file PDF tidak corrupt, Ghostscript terinstall, dan path sudah benar di .env (GHOSTSCRIPT_PATH).';
            } elseif (str_contains($msg, 'empty content') || str_contains($msg, 'JSON')) {
                $userMsg = 'AI mengembalikan response tidak valid. Coba ulangi scan — kualitas gambar atau format dokumen mungkin perlu diperbaiki.';
            } elseif (str_contains($msg, 'API Key') || str_contains($msg, 'Belum ada')) {
                $userMsg = $msg; // Already user-friendly from PdfParserService
            } elseif (str_contains($msg, 'tidak dapat dibaca') || str_contains($msg, 'corrupt') || str_contains($msg, 'rusak')) {
                $userMsg = 'File yang diunggah rusak atau corrupt. Pastikan file PDF/Gambar Anda valid dan tidak rusak.';
            } else {
                $userMsg = 'Gagal memproses file: '.$msg;
            }

            return redirect()->back()->with('error', $userMsg);
        }
    }

    public function exportExcel(Request $request)
    {
        $data = $request->input('data', []);
        $exportData = [];

        // Check if we should include verification columns
        $includeVerification = $request->input('include_verification', false);

        foreach ($data as $item) {
            if ($includeVerification) {
                // Include confidence and notes if verification data is available
                $confidence = isset($item['confidence']) ? round($item['confidence'] * 100) : 'N/A';
                $notes = '';

                if (! empty($item['was_corrected'])) {
                    $notes = ($item['correction_type'] ?? 'corrected');
                    if (! empty($item['suggestion'])) {
                        $notes .= ' - '.$item['suggestion'];
                    }
                }
                if (! empty($item['issue_detected'])) {
                    $notes .= ' | Issue: '.$item['issue_detected'];
                }

                $exportData[] = [
                    $item['registration'] ?? 'PENDING',
                    $item['seat_id'] ?? 'UNKNOWN',
                    $item['expiry_date'] ?? '-',
                    $confidence.'%',
                    $notes ?: 'OK',
                ];
            } else {
                // Standard export (without verification)
                $exportData[] = [
                    $item['registration'] ?? 'PENDING',
                    $item['seat_id'] ?? 'UNKNOWN',
                    $item['expiry_date'] ?? '-',
                ];
            }
        }

        // Build dynamic filename from registration + aircraft type
        $registration = $request->input('master_registration', 'scan');
        $aircraftType = $request->input('aircraft_type', '');

        // Sanitize for filename: PK-GIA_B777_scan.xlsx
        $regPart = preg_replace('/[^A-Za-z0-9\-]/', '', $registration) ?: 'scan';
        $typePart = preg_replace('/[^A-Za-z0-9\-]/', '', $aircraftType);
        $filenameParts = [$regPart];
        if (! empty($typePart)) {
            $filenameParts[] = $typePart;
        }
        $filenameParts[] = 'scan';
        $filename = implode('_', $filenameParts).'.xlsx';

        return Excel::download(
            new PdfScanExport($exportData, $includeVerification),
            $filename
        );
    }

    public function saveToDb(Request $request)
    {
        $registration = strtoupper(trim($request->input('master_registration', '')));
        if (empty($registration)) {
            return redirect()->back()->with('error', 'Registrasi pesawat tidak boleh kosong.');
        }

        $aircraft = Aircraft::where('registration', $registration)
            ->orWhere('registration', str_replace('-', '', $registration))
            ->first();

        if (!$aircraft) {
            return redirect()->back()->with('error', "Pesawat dengan registrasi [{$registration}] tidak ditemukan di database. Silakan daftarkan pesawat terlebih dahulu di Fleet Manager.");
        }

        $seatsData = $request->input('data', []);
        if (empty($seatsData)) {
            return redirect()->back()->with('error', 'Tidak ada data kursi yang disimpan.');
        }

        $updatedSeatsCount = 0;
        $seatIds = [];
        $affectedPns = [];

        // Parse class_rows config for the aircraft layout to dynamic class type determination
        $classRows = [];
        if ($aircraft->layout) {
            $classRows = config("aircraft_class_rows.{$aircraft->layout}", []);
        }

        try {
            DB::transaction(function () use ($seatsData, $aircraft, &$updatedSeatsCount, &$seatIds, &$affectedPns, $classRows) {
                foreach ($seatsData as $item) {
                    $seatId = strtoupper(trim($item['seat_id'] ?? ''));
                    $expiryDateStr = trim($item['expiry_date'] ?? '');

                    if (empty($seatId) || empty($expiryDateStr)) {
                        continue;
                    }

                    // Parse expiry date
                    $expiryDate = null;
                    try {
                        $dateValue = strtoupper(str_replace(['/', '.', ' '], '-', $expiryDateStr));
                        if (preg_match('/^([A-Z]{3})-(\d{2,4})$/', $dateValue, $matches)) {
                            $year = $matches[2];
                            if (strlen($year) == 2) {
                                $year = '20'.$year;
                            }
                            $expiryDate = Carbon::parse('01-'.$matches[1]."-$year");
                        } else {
                            $expiryDate = Carbon::parse($dateValue);
                        }
                    } catch (\Exception $e) {
                        Log::warning("[PDF Scan saveToDb] Failed parsing date: {$expiryDateStr}");
                        continue;
                    }

                    if (!$expiryDate) {
                        continue;
                    }

                    // Map class type, row, col
                    $classType = 'economy';
                    $rowNum = null;
                    $colNum = $seatId;
                    $seatIdLower = strtolower($seatId);

                    // Attendant
                    if (str_contains($seatIdLower, 'att/') || str_starts_with($seatIdLower, 'd')) {
                        $classType = 'attendant';
                        $colNum = $seatIdLower;
                    }
                    // Cockpit
                    elseif (in_array($seatIdLower, ['captain', 'pilot', 'copilot', 'observer1', 'observer2'])) {
                        $classType = 'cockpit';
                        $colNum = $seatIdLower;
                    }
                    // Spare
                    elseif (preg_match('/^(pax|adult|inf|infant|spare)-?(\\d+)$/i', $seatId, $m)) {
                        $rawType = strtolower($m[1]);
                        $isInfant = in_array($rawType, ['inf', 'infant']);
                        $classType = $isInfant ? 'spare-inf' : 'spare-pax';
                        $colNum = $seatIdLower;
                    }
                    // Regular seat
                    else {
                        $cleanSeatId = preg_replace('/[^A-Z0-9]/', '', $seatId);
                        if (preg_match('/^(\d+)([A-Z]+)$/', $cleanSeatId, $matches)) {
                            $rowNum = (int) $matches[1];
                            $colNum = $matches[2];
                        }

                        if ($rowNum && !empty($classRows)) {
                            foreach ($classRows as $class => $rows) {
                                if (in_array($rowNum, $rows)) {
                                    $classType = $class;
                                    break;
                                }
                            }
                        }
                    }

                    Seat::updateOrCreate(
                        [
                            'registration' => $aircraft->registration,
                            'seat_id' => $seatId,
                        ],
                        [
                            'row' => $rowNum,
                            'col' => $colNum,
                            'class_type' => $classType,
                            'expiry_date' => $expiryDate->toDateString(),
                        ]
                    );

                    $updatedSeatsCount++;
                    $seatIds[] = $seatId;

                    if (str_starts_with($seatIdLower, 'inf-') || str_contains($seatIdLower, 'infant')) {
                        $affectedPns[] = $aircraft->pn_infant;
                    } elseif (in_array($seatIdLower, ['captain', 'fo', 'pilot', 'copilot', 'obs-1', 'obs-2', 'observer1', 'observer2']) || str_starts_with($seatIdLower, 'att/')) {
                        $affectedPns[] = $aircraft->pn_crew;
                    } else {
                        $affectedPns[] = $aircraft->pn_adult;
                    }
                }

                if ($updatedSeatsCount > 0) {
                    $uniquePns = array_values(array_unique(array_filter($affectedPns)));

                    // Log the activity
                    ActivityLog::create([
                        'user_id' => Auth::id(),
                        'registration' => $aircraft->registration,
                        'action' => 'update',
                        'details' => [
                            'seat_count' => $updatedSeatsCount,
                            'expiry_date' => 'Multiple (PDF Scan Direct Apply)',
                            'pns' => $uniquePns,
                            'seats' => array_slice($seatIds, 0, 50),
                            'source' => 'PDF Scan Direct Apply',
                        ],
                    ]);
                }
            });

            if ($updatedSeatsCount > 0) {
                // Clear cache
                Cache::flush();
                session()->forget('pdf_scan_result');

                return redirect()->route('aircraft.show', $aircraft->registration)
                    ->with('success', "Berhasil menyimpan {$updatedSeatsCount} data tanggal garansi rompi langsung ke database!");
            }

            return redirect()->back()->with('error', 'Tidak ada data kursi valid yang berhasil disimpan.');

        } catch (\Exception $e) {
            Log::error('[PDF Scan saveToDb] Failed saving to database', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat menyimpan ke database: ' . $e->getMessage());
        }
    }
}

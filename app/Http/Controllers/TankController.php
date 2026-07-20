<?php

namespace App\Http\Controllers;

use App\Models\Tank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TankController extends Controller
{
    public function index()
    {
        $tanks = Tank::orderBy('code')->orderBy('main_hole')->get();
        return view('tanks.index', compact('tanks'));
    }

    public function create()
    {
        if (!Auth::user()->isSpv()) {
            abort(403, 'Hanya Supervisor yang dapat menambah tangki baru.');
        }
        return view('tanks.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isSpv()) {
            abort(403, 'Hanya Supervisor yang dapat menambah tangki baru.');
        }

        $request->validate([
            'code'              => 'required|string|max:50',
            'main_hole'         => 'required|string|max:50',
            'capacity'          => 'nullable|numeric|min:0',
            'is_active'         => 'required|boolean',
            'calibration_file'  => 'nullable|file|mimes:xlsx,xls|max:10240', // max 10MB
        ]);

        $tank = Tank::create([
            'code'      => $request->code,
            'main_hole' => $request->main_hole,
            'capacity'  => $request->capacity,
            'is_active' => $request->is_active,
        ]);

        if ($request->hasFile('calibration_file')) {
            try {
                $this->importCalibrationData($tank, $request->file('calibration_file'));
            } catch (\Exception $e) {
                return redirect()->route('tanks.edit', $tank->id)
                    ->with('warning', 'Tangki berhasil dibuat, namun file kalibrasi gagal diproses: ' . $e->getMessage());
            }
        }

        return redirect()->route('tanks.index')
            ->with('success', 'Tangki baru berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if (!Auth::user()->isSpv()) {
            abort(403, 'Hanya Supervisor yang dapat mengubah data tangki.');
        }

        $tank = Tank::findOrFail($id);
        return view('tanks.edit', compact('tank'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->isSpv()) {
            abort(403, 'Hanya Supervisor yang dapat mengubah data tangki.');
        }

        $tank = Tank::findOrFail($id);

        $request->validate([
            'code'              => 'required|string|max:50',
            'main_hole'         => 'required|string|max:50',
            'capacity'          => 'nullable|numeric|min:0',
            'is_active'         => 'required|boolean',
            'calibration_file'  => 'nullable|file|mimes:xlsx,xls|max:10240',
        ]);

        $tank->update([
            'code'      => $request->code,
            'main_hole' => $request->main_hole,
            'capacity'  => $request->capacity,
            'is_active' => $request->is_active,
        ]);

        if ($request->hasFile('calibration_file')) {
            try {
                DB::transaction(function () use ($tank, $request) {
                    $tank->calibrations()->delete();
                    $this->importCalibrationData($tank, $request->file('calibration_file'));
                });
            } catch (\Exception $e) {
                return redirect()->route('tanks.edit', $tank->id)
                    ->with('error', 'Gagal memproses file kalibrasi: ' . $e->getMessage());
            }
        }

        return redirect()->route('tanks.index')
            ->with('success', 'Data tangki berhasil diperbarui.');
    }

    private function importCalibrationData(Tank $tank, $file)
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);
        
        if (count($rows) < 2) {
            throw new \Exception('File Excel kosong atau tidak memiliki baris data.');
        }

        // Normalize header text while preserving the original Excel column letter.
        // Excel headers can contain uppercase text or multiple spaces, for example
        // "DIPP (CM)" and "VOLUME   (L)".
        $headerRow = [];
        foreach ($rows[1] as $columnLetter => $headerName) {
            $headerRow[$columnLetter] = strtolower(preg_replace('/\s+/', ' ', trim((string) $headerName)));
        }
        
        // Find indices
        $dippCmKey = null;
        $dippMmKey = null;
        $volumeKey = null;

        foreach ($headerRow as $colLetter => $headerName) {
            if ($headerName === 'dipp (cm)' || $headerName === 'dipp(cm)' || str_contains($headerName, 'dipp (cm)')) {
                $dippCmKey = $colLetter;
            }
            if ($headerName === 'dipp (mm)' || $headerName === 'dipp(mm)' || str_contains($headerName, 'dipp (mm)')) {
                $dippMmKey = $colLetter;
            }
            if ($headerName === 'volume (l)' || $headerName === 'volume(l)' || str_contains($headerName, 'volume (l)') || $headerName === 'volume(liter)') {
                $volumeKey = $colLetter;
            }
        }

        // If headers not found by exact string, try general matches
        if (!$dippCmKey && !$dippMmKey) {
            foreach ($headerRow as $colLetter => $headerName) {
                if (str_contains($headerName, 'dipp') && str_contains($headerName, 'cm')) {
                    $dippCmKey = $colLetter;
                    break;
                }
            }
        }
        if (!$dippMmKey) {
            foreach ($headerRow as $colLetter => $headerName) {
                if (str_contains($headerName, 'dipp') && str_contains($headerName, 'mm')) {
                    $dippMmKey = $colLetter;
                    break;
                }
            }
        }
        if (!$volumeKey) {
            foreach ($headerRow as $colLetter => $headerName) {
                if (str_contains($headerName, 'volume') && (str_contains($headerName, '(l)') || str_contains($headerName, ' l'))) {
                    $volumeKey = $colLetter;
                    break;
                }
            }
        }

        if ((!$dippCmKey && !$dippMmKey) || !$volumeKey) {
            throw new \Exception('Kolom header "DIPP (CM)" atau "DIPP (MM)" dan "VOLUME (L)" tidak ditemukan pada baris pertama Excel.');
        }

        // Start reading data from row 2
        $calibrations = [];
        $now = now();

        for ($i = 2; $i <= count($rows); $i++) {
            $row = $rows[$i];
            
            $rawVol = isset($row[$volumeKey]) ? trim($row[$volumeKey]) : null;
            if ($rawVol === null || $rawVol === '') continue; // Skip empty rows

            // Clean volume (replace comma with dot if string float representation)
            $vol = floatval(str_replace(',', '.', str_replace('.', '', $rawVol))); // Handles formats like 10.000 or 10,000 or 10.2

            // Use DIPP (CM) as the source of truth when both columns exist.
            // The form submits sounding in CM, while some Excel templates use
            // a different scale in their DIPP (MM) column.
            if ($dippCmKey && isset($row[$dippCmKey]) && trim((string) $row[$dippCmKey]) !== '') {
                $cmVal = floatval(str_replace(',', '.', trim($row[$dippCmKey])));
                $mmVal = intval(round($cmVal * 10));
            } elseif ($dippMmKey && isset($row[$dippMmKey]) && trim((string) $row[$dippMmKey]) !== '') {
                $mmVal = intval(trim($row[$dippMmKey]));
                $cmVal = $mmVal / 10.0;
            } else {
                continue; // Skip if no sounding value
            }

            $calibrations[] = [
                'tank_id'       => $tank->id,
                'sounding_cm'   => $cmVal,
                'sounding_mm'   => $mmVal,
                'volume_liters' => $vol,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];

            // Bulk insert every 500 records to save memory/prevent timeouts
            if (count($calibrations) >= 500) {
                \App\Models\TankCalibration::insert($calibrations);
                $calibrations = [];
            }
        }

        if (count($calibrations) > 0) {
            \App\Models\TankCalibration::insert($calibrations);
        }
    }

    public function destroy($id)
    {
        if (!Auth::user()->isSpv()) {
            abort(403, 'Hanya Supervisor yang dapat menghapus tangki.');
        }

        $tank = Tank::findOrFail($id);
        
        // Prevent deletion if items exist to preserve database integrity
        if ($tank->items()->exists()) {
            return redirect()->route('tanks.index')
                ->with('error', 'Tangki tidak dapat dihapus karena sudah memiliki catatan laporan kegiatan.');
        }

        $tank->delete();

        return redirect()->route('tanks.index')
            ->with('success', 'Tangki berhasil dihapus.');
    }

    public function getVolume(Request $request, $tank_id)
    {
        $sounding = $request->query('sounding');
        
        if ($sounding === null || $sounding === '') {
            return response()->json(['volume' => null]);
        }

        // Parse sounding from CM, convert to MM for precise database lookup
        $soundingCm = floatval($sounding);
        $soundingMm = intval(round($soundingCm * 10));

        // 1. Try to find exact sounding match
        $calibration = \App\Models\TankCalibration::where('tank_id', $tank_id)
            ->where('sounding_mm', $soundingMm)
            ->first();

        if ($calibration) {
            return response()->json(['volume' => floatval($calibration->volume_liters)]);
        }

        // 2. Linear Interpolation between closest lower and upper soundings
        $lower = \App\Models\TankCalibration::where('tank_id', $tank_id)
            ->where('sounding_mm', '<', $soundingMm)
            ->orderBy('sounding_mm', 'desc')
            ->first();

        $upper = \App\Models\TankCalibration::where('tank_id', $tank_id)
            ->where('sounding_mm', '>', $soundingMm)
            ->orderBy('sounding_mm', 'asc')
            ->first();

        if ($lower && $upper) {
            // Linear interpolation formula: y = y1 + ((x - x1) * (y2 - y1) / (x2 - x1))
            $x = $soundingCm;
            $x1 = floatval($lower->sounding_cm);
            $x2 = floatval($upper->sounding_cm);
            $y1 = floatval($lower->volume_liters);
            $y2 = floatval($upper->volume_liters);

            if ($x2 - $x1 != 0) {
                $volume = $y1 + (($x - $x1) * ($y2 - $y1) / ($x2 - $x1));
                return response()->json(['volume' => round($volume, 2)]);
            }
        }

        // 3. Fallback: return the closest one if only one side is found
        if ($lower) {
            return response()->json(['volume' => floatval($lower->volume_liters)]);
        }
        if ($upper) {
            return response()->json(['volume' => floatval($upper->volume_liters)]);
        }

        return response()->json(['volume' => null]);
    }
}

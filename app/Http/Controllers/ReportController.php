<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\DailyReportItem;
use App\Models\Site;
use App\Models\Tank;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get all active sites for dropdown (ALL roles)
        $sites = Site::where('is_active', true)->orderBy('name')->get();
        
        // Get filter parameters
        $siteId = $request->get('site_id');
        $search = $request->get('search');
        $status = $request->get('status');
        $sortOrder = $request->get('sort', 'desc'); // desc = terbaru, asc = terlama
        $perPage = $request->get('per_page', 15); // default 15
        
        // Validate per_page
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 15;
        }
        
        $query = DailyReport::with(['fuelman', 'gl', 'spv', 'site']);

        // Fuelman only sees their own reports
        if ($user->isFuelman()) {
            $query->where('fuelman_id', $user->id);
        }
        
        // Filter by site if selected (ALL roles)
        if ($siteId) {
            $query->where('site_id', $siteId);
        }
        
        // Filter by status if selected
        if ($status) {
            $query->where('status', $status);
        }
        
        // Search filter - search in date, fuelman name, gl name, spv name
        if ($search) {
            $query->where(function($q) use ($search) {
                // Cast date to string for LIKE comparison in PostgreSQL
                $q->whereRaw("to_char(date, 'YYYY-MM-DD') LIKE ?", ["%{$search}%"])
                  ->orWhereHas('fuelman', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('gl', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('spv', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Sort order
        $query->orderBy('date', $sortOrder);

        $reports = $query->paginate($perPage);

        return view('reports.index', compact('reports', 'sites', 'siteId', 'search', 'status', 'sortOrder', 'perPage'));
    }

    public function create()
    {
        if (!Auth::user()->isFuelman()) {
            abort(403, 'Hanya Fuelman yang dapat membuat laporan baru.');
        }

        // Load ALL active tanks ordered by site first, then by code alphabetically
        $tanks = Tank::with('site')
            ->where('tanks.is_active', true)
            ->join('sites', 'tanks.site_id', '=', 'sites.id')
            ->orderBy('sites.code')
            ->orderBy('tanks.code')
            ->orderBy('tanks.main_hole')
            ->select('tanks.*')
            ->get();
        
        $defaultDate = now()->format('Y-m-d');
        $sites = \App\Models\Site::where('is_active', true)->orderBy('code')->get();
        
        // Create empty report object for consistency with edit view
        $report = new DailyReport();
        $report->collaborator_id = null;

        return view('reports.create', compact('tanks', 'defaultDate', 'sites', 'report'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isFuelman()) {
            abort(403, 'Hanya Fuelman yang dapat membuat laporan baru.');
        }

        \Log::info('Store report attempt', [
            'user_id' => Auth::id(),
            'site_id' => $request->site_id,
            'date' => $request->date,
            'items_count' => count($request->items ?? []),
            'has_files' => $request->hasFile('items.*.photos.*'),
            'memory_usage' => memory_get_usage(true) / 1024 / 1024 . ' MB'
        ]);

        $request->validate([
            'date' => [
                'required',
                'date',
                Rule::unique('daily_reports')->where(function ($query) use ($request) {
                    return $query->where('site_id', $request->site_id);
                }),
            ],
            'site_id' => 'required|exists:sites,id',
            'items' => 'nullable|array',
            'items.*.tank_id' => 'nullable|exists:tanks,id',
            'items.*.sounding_pagi' => 'nullable|numeric',
            'items.*.liter_pagi' => 'nullable|string',
            'items.*.jam_pagi' => 'nullable',
            'items.*.petugas_pagi' => 'nullable|string',
            'items.*.sounding_sore' => 'nullable|numeric',
            'items.*.liter_sore' => 'nullable|string',
            'items.*.jam_sore' => 'nullable',
            'items.*.petugas_sore' => 'nullable|string',
            'items.*.fm_pagi' => 'nullable|numeric',
            'items.*.fm_sore' => 'nullable|numeric',
            'items.*.keterangan' => 'nullable|string',
            'items.*.photos' => 'nullable|array',
            'items.*.photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:1024',
            'kapasitas' => 'nullable|array',
            'kapasitas.*.soh' => 'nullable|numeric|min:0',
            'kapasitas.*.rata' => 'nullable|numeric|min:0',
            
            // Transfers B validation
            'transfers' => 'nullable|array',
            'transfers.*.dari_tangki' => 'nullable|string',
            'transfers.*.ke_tangki' => 'nullable|string',
            'transfers.*.spm_awal' => 'nullable|numeric',
            'transfers.*.spm_akhir' => 'nullable|numeric',
            'transfers.*.spm_hasil' => 'nullable|numeric',
            'transfers.*.spm_liter' => 'nullable|string',
            'transfers.*.ft_awal' => 'nullable|numeric',
            'transfers.*.ft_akhir' => 'nullable|numeric',
            'transfers.*.ft_hasil' => 'nullable|numeric',
            'transfers.*.ft_liter' => 'nullable|string',
            'transfers.*.fm_awal' => 'nullable|numeric',
            'transfers.*.fm_akhir' => 'nullable|numeric',
            'transfers.*.fm_jumlah' => 'nullable|numeric',
            'transfers.*.jam_mulai' => 'nullable',
            'transfers.*.jam_selesai' => 'nullable',
            'transfers.*.lama_transfer' => 'nullable|string',
            'transfers.*.photos' => 'nullable|array',
            'transfers.*.photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:1024',

            // Flowmeters C validation
            'flowmeters' => 'nullable|array',
            'flowmeters.*.unit' => 'nullable|string',
            'flowmeters.*.jenis_flowmeter' => 'nullable|string',
            'flowmeters.*.nomor_seri' => 'nullable|string',
            'flowmeters.*.awal_pagi' => 'nullable|numeric',
            'flowmeters.*.akhir_sore' => 'nullable|numeric',
            'flowmeters.*.jumlah_pakai' => 'nullable|numeric',
        ], [
            'date.unique' => 'Laporan untuk site ini pada tanggal tersebut sudah ada.',
            'site_id.required' => 'Site harus dipilih.',
            'site_id.exists' => 'Site yang dipilih tidak valid.',
            'items.*.photos.*.image' => 'File harus berupa gambar.',
            'items.*.photos.*.mimes' => 'Format gambar harus: JPG, JPEG, PNG, atau WEBP.',
            'items.*.photos.*.max' => 'Ukuran gambar maksimal 1MB per file.',
            'transfers.*.photos.*.image' => 'File harus berupa gambar.',
            'transfers.*.photos.*.mimes' => 'Format gambar harus: JPG, JPEG, PNG, atau WEBP.',
            'transfers.*.photos.*.max' => 'Ukuran gambar maksimal 1MB per file.',
        ]);

        // Custom validation for photo count based on main_hole type
        if ($request->items) {
            foreach ($request->items as $index => $item) {
                if (!empty($item['tank_id']) && !empty($item['photos'])) {
                    $tank = \App\Models\Tank::find($item['tank_id']);
                    if ($tank) {
                        // Only DEPAN/BELAKANG variants get 4 photos, everything else gets 8
                        $maxPhotos = 8; // default
                        if (str_contains($tank->main_hole, 'DEPAN') || str_contains($tank->main_hole, 'BELAKANG')) {
                            $maxPhotos = 4;
                        }
                        if (count($item['photos']) > $maxPhotos) {
                            return back()->withInput()->withErrors([
                                "items.{$index}.photos" => "Tangki dengan main hole {$tank->main_hole} maksimal {$maxPhotos} foto."
                            ]);
                        }
                    }
                }
            }
        }

        // Custom validation for transfer photos (max 6)
        if ($request->transfers) {
            foreach ($request->transfers as $index => $transfer) {
                if (!empty($transfer['photos']) && count($transfer['photos']) > 6) {
                    return back()->withInput()->withErrors([
                        "transfers.{$index}.photos" => "Transfer solar maksimal 6 foto."
                    ]);
                }
            }
        }

        DB::beginTransaction();
        try {
            $kapasitas = $request->kapasitas ?? [];
            $report = DailyReport::create([
                'date'       => $request->date,
                'site_id'    => $request->site_id,
                'status'     => 'draft',
                'fuelman_id' => Auth::id(),
                'soh_spm1'   => $kapasitas['SPM1']['soh'] ?? null,
                'soh_spm2'   => $kapasitas['SPM2']['soh'] ?? null,
                'soh_spm3'   => $kapasitas['SPM3']['soh'] ?? null,
                'soh_ft05'   => $kapasitas['FT05']['soh'] ?? null,
                'rata_spm1'  => $kapasitas['SPM1']['rata'] ?? null,
                'rata_spm2'  => $kapasitas['SPM2']['rata'] ?? null,
                'rata_spm3'  => $kapasitas['SPM3']['rata'] ?? null,
                'rata_ft05'  => $kapasitas['FT05']['rata'] ?? null,
            ]);

            \Log::info('Report created', ['report_id' => $report->id]);

            $this->saveItems($report, $request->items);
            \Log::info('Items saved', ['report_id' => $report->id]);
            
            $this->saveTransfers($report, $request->transfers ?? []);
            \Log::info('Transfers saved', ['report_id' => $report->id]);
            
            $this->saveFlowmeters($report, $request->flowmeters ?? []);
            \Log::info('Flowmeters saved', ['report_id' => $report->id]);

            DB::commit();
            \Log::info('Report stored successfully', ['report_id' => $report->id]);

            return redirect()->route('reports.show', $report->id)
                ->with('success', 'Laporan harian berhasil dibuat sebagai Draft.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::error('Validation error in store', [
                'errors' => $e->errors(),
                'request' => $request->all(),
            ]);
            return back()->withInput()->withErrors($e->errors());
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Failed to store report', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'memory' => memory_get_usage(true) / 1024 / 1024 . ' MB',
                'request_data' => [
                    'date' => $request->date ?? 'not set',
                    'site_id' => $request->site_id ?? 'not set',
                    'has_items' => !empty($request->items),
                    'has_transfers' => !empty($request->transfers),
                    'has_files' => $request->hasFile('items.*.photos.*') || $request->hasFile('transfers.*.photos.*'),
                ]
            ]);
            
            // Return detailed error for debugging
            if (config('app.debug')) {
                return back()->withInput()->with('error', 'Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            }
            
            return back()->withInput()->with('error', 'Gagal membuat laporan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $report = DailyReport::with(['items.tank', 'transfers', 'flowmeters', 'attachments', 'fuelman', 'gl', 'spv', 'site', 'collaborator'])->findOrFail($id);
        $user = Auth::user();

        // Authorization: Fuelman can only view their own reports OR reports where they are collaborator
        if ($user->isFuelman()) {
            if ($report->fuelman_id !== $user->id && $report->collaborator_id !== $user->id) {
                abort(403, 'Anda tidak memiliki akses ke laporan ini.');
            }
        }

        // Debug: Log attachment URLs
        if ($report->attachments->isNotEmpty()) {
            foreach ($report->attachments as $attachment) {
                \Log::info('Attachment URL generated', [
                    'id' => $attachment->id,
                    'path' => $attachment->path,
                    'public_url' => $attachment->getPublicUrl(),
                    'disk' => config('filesystems.report_attachment_disk'),
                    's3_url_config' => config('filesystems.disks.s3.url'),
                ]);
            }
        }

        return view('reports.show', compact('report'));
    }

    public function edit($id)
    {
        $report = DailyReport::with(['items.tank', 'transfers', 'flowmeters', 'attachments', 'collaborator'])->findOrFail($id);
        $user = Auth::user();

        // Authorization: Only fuelman (creator OR collaborator) can edit
        if (!$user->isFuelman()) {
            abort(403, 'Hanya Fuelman yang dapat mengubah laporan.');
        }
        
        if ($report->fuelman_id !== $user->id && $report->collaborator_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah laporan ini.');
        }

        if (!in_array($report->status, ['draft', 'rejected'])) {
            return redirect()->route('reports.show', $report->id)
                ->with('error', 'Hanya laporan dengan status Draft atau Direvisi yang dapat diubah.');
        }

        // Load items WITHOUT keying by tank_id, so multiple rows for same tank are preserved
        $items = $report->items; // Keep as collection, don't use keyBy
        $transfers = $report->transfers;
        $flowmeters = $report->flowmeters;
        
        // Load ALL active tanks ordered by site first, then alphabetically
        $tanks = Tank::with('site')
            ->where('tanks.is_active', true)
            ->join('sites', 'tanks.site_id', '=', 'sites.id')
            ->orderBy('sites.code')
            ->orderBy('tanks.code')
            ->orderBy('tanks.main_hole')
            ->select('tanks.*')
            ->get();
        $sites = \App\Models\Site::where('is_active', true)->orderBy('code')->get();

        return view('reports.edit', compact('report', 'tanks', 'items', 'transfers', 'flowmeters', 'sites'));
    }

    public function update(Request $request, $id)
    {
        $report = DailyReport::findOrFail($id);
        $user = Auth::user();

        // Authorization: Only fuelman (creator OR collaborator) can update
        if (!$user->isFuelman()) {
            abort(403, 'Hanya Fuelman yang dapat mengubah laporan.');
        }
        
        if ($report->fuelman_id !== $user->id && $report->collaborator_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah laporan ini.');
        }

        if (!in_array($report->status, ['draft', 'rejected'])) {
            return redirect()->route('reports.show', $report->id)
                ->with('error', 'Hanya laporan dengan status Draft atau Direvisi yang dapat diubah.');
        }

        $request->validate([
            'date' => [
                'required',
                'date',
                Rule::unique('daily_reports')->where(function ($query) use ($request) {
                    return $query->where('site_id', $request->site_id);
                })->ignore($id),
            ],
            'site_id' => 'required|exists:sites,id',
            'items' => 'nullable|array',
            'items.*.tank_id' => 'nullable|exists:tanks,id',
            'items.*.sounding_pagi' => 'nullable|numeric',
            'items.*.liter_pagi' => 'nullable|string',
            'items.*.jam_pagi' => 'nullable',
            'items.*.petugas_pagi' => 'nullable|string',
            'items.*.sounding_sore' => 'nullable|numeric',
            'items.*.liter_sore' => 'nullable|string',
            'items.*.jam_sore' => 'nullable',
            'items.*.petugas_sore' => 'nullable|string',
            'items.*.fm_pagi' => 'nullable|numeric',
            'items.*.fm_sore' => 'nullable|numeric',
            'items.*.keterangan' => 'nullable|string',
            'items.*.photos' => 'nullable|array',
            'items.*.photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:1024',
            'delete_attachment_ids' => 'nullable|array',
            'delete_attachment_ids.*' => 'integer',
            'kapasitas' => 'nullable|array',
            'kapasitas.*.soh' => 'nullable|numeric|min:0',
            'kapasitas.*.rata' => 'nullable|numeric|min:0',

            // Transfers B validation
            'transfers' => 'nullable|array',
            'transfers.*.dari_tangki' => 'nullable|string',
            'transfers.*.ke_tangki' => 'nullable|string',
            'transfers.*.spm_awal' => 'nullable|numeric',
            'transfers.*.spm_akhir' => 'nullable|numeric',
            'transfers.*.spm_hasil' => 'nullable|numeric',
            'transfers.*.spm_liter' => 'nullable|string',
            'transfers.*.ft_awal' => 'nullable|numeric',
            'transfers.*.ft_akhir' => 'nullable|numeric',
            'transfers.*.ft_hasil' => 'nullable|numeric',
            'transfers.*.ft_liter' => 'nullable|string',
            'transfers.*.fm_awal' => 'nullable|numeric',
            'transfers.*.fm_akhir' => 'nullable|numeric',
            'transfers.*.fm_jumlah' => 'nullable|numeric',
            'transfers.*.jam_mulai' => 'nullable',
            'transfers.*.jam_selesai' => 'nullable',
            'transfers.*.lama_transfer' => 'nullable|string',
            'transfers.*.photos' => 'nullable|array',
            'transfers.*.photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:1024',

            // Flowmeters C validation
            'flowmeters' => 'nullable|array',
            'flowmeters.*.unit' => 'nullable|string',
            'flowmeters.*.jenis_flowmeter' => 'nullable|string',
            'flowmeters.*.nomor_seri' => 'nullable|string',
            'flowmeters.*.awal_pagi' => 'nullable|numeric',
            'flowmeters.*.akhir_sore' => 'nullable|numeric',
            'flowmeters.*.jumlah_pakai' => 'nullable|numeric',
        ]);

        // Custom validation for photo count based on main_hole type
        if ($request->items) {
            foreach ($request->items as $index => $item) {
                if (!empty($item['tank_id']) && !empty($item['photos'])) {
                    $tank = \App\Models\Tank::find($item['tank_id']);
                    if ($tank) {
                        // Only DEPAN/BELAKANG variants get 4 photos, everything else gets 8
                        $maxPhotos = 8; // default
                        if (str_contains($tank->main_hole, 'DEPAN') || str_contains($tank->main_hole, 'BELAKANG')) {
                            $maxPhotos = 4;
                        }
                        if (count($item['photos']) > $maxPhotos) {
                            return back()->withInput()->withErrors([
                                "items.{$index}.photos" => "Tangki dengan main hole {$tank->main_hole} maksimal {$maxPhotos} foto."
                            ]);
                        }
                    }
                }
            }
        }

        // Custom validation for transfer photos (max 6)
        if ($request->transfers) {
            foreach ($request->transfers as $index => $transfer) {
                if (!empty($transfer['photos']) && count($transfer['photos']) > 6) {
                    return back()->withInput()->withErrors([
                        "transfers.{$index}.photos" => "Transfer solar maksimal 6 foto."
                    ]);
                }
            }
        }

        DB::beginTransaction();
        try {
            $kapasitas = $request->kapasitas ?? [];
            $report->update([
                'date'      => $request->date,
                'site_id'   => $request->site_id,
                'status'    => 'draft',
                'soh_spm1'  => $kapasitas['SPM1']['soh'] ?? $report->soh_spm1,
                'soh_spm2'  => $kapasitas['SPM2']['soh'] ?? $report->soh_spm2,
                'soh_spm3'  => $kapasitas['SPM3']['soh'] ?? $report->soh_spm3,
                'soh_ft05'  => $kapasitas['FT05']['soh'] ?? $report->soh_ft05,
                'rata_spm1' => $kapasitas['SPM1']['rata'] ?? $report->rata_spm1,
                'rata_spm2' => $kapasitas['SPM2']['rata'] ?? $report->rata_spm2,
                'rata_spm3' => $kapasitas['SPM3']['rata'] ?? $report->rata_spm3,
                'rata_ft05' => $kapasitas['FT05']['rata'] ?? $report->rata_ft05,
            ]);

            $this->deleteAttachments($report, $request->input('delete_attachment_ids', []));

            $this->migrateLegacyItemAttachmentKeys($report);

            // Clear existing items and save new ones
            $report->items()->delete();
            $report->transfers()->delete();
            $report->flowmeters()->delete();

            $this->saveItems($report, $request->items);
            $this->saveTransfers($report, $request->transfers ?? []);
            $this->saveFlowmeters($report, $request->flowmeters ?? []);

            DB::commit();

            return redirect()->route('reports.show', $report->id)
                ->with('success', 'Laporan harian berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui laporan: ' . $e->getMessage());
        }
    }

    public function submit($id)
    {
        $report = DailyReport::findOrFail($id);
        $user = Auth::user();

        if (!$user->isFuelman() || $report->fuelman_id !== $user->id) {
            abort(403, 'Hanya pembuat laporan yang dapat mengirim laporan.');
        }

        if (!in_array($report->status, ['draft', 'rejected'])) {
            return redirect()->route('reports.show', $report->id)
                ->with('error', 'Laporan sudah dikirim atau disetujui.');
        }

        // Reset approval saat submit ulang
        $report->update([
            'status' => 'submitted',
            'gl_id' => null,
            'spv_id' => null,
            'gl_feedback' => null,
            'spv_feedback' => null,
        ]);

        return redirect()->route('reports.show', $report->id)
            ->with('success', 'Laporan berhasil dikirim ke Group Leader.');
    }

    public function verify(Request $request, $id)
    {
        $report = DailyReport::findOrFail($id);
        $user = Auth::user();

        if (!$user->isGl()) {
            abort(403, 'Hanya Group Leader yang dapat memverifikasi laporan.');
        }

        if ($report->status !== 'submitted') {
            return redirect()->route('reports.show', $report->id)
                ->with('error', 'Laporan ini tidak dalam antrean verifikasi.');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'feedback' => 'required_if:action,reject|nullable|string',
        ]);

        if ($request->action === 'approve') {
            $report->update([
                'status' => 'verified',
                'gl_id' => $user->id,
                'gl_feedback' => null,
            ]);
            return redirect()->route('reports.show', $report->id)
                ->with('success', 'Laporan berhasil diverifikasi dan diteruskan ke Supervisor.');
        } else {
            // GL Reject: tidak ada approval sama sekali
            $report->update([
                'status' => 'rejected',
                'gl_id' => null,
                'spv_id' => null,
                'gl_feedback' => $request->feedback,
                'spv_feedback' => null,
            ]);
            return redirect()->route('reports.show', $report->id)
                ->with('warning', 'Laporan telah ditolak dan dikembalikan ke Fuelman.');
        }
    }

    public function approve(Request $request, $id)
    {
        $report = DailyReport::findOrFail($id);
        $user = Auth::user();

        if (!$user->isSpv()) {
            abort(403, 'Hanya Supervisor yang dapat menyetujui laporan.');
        }

        if ($report->status !== 'verified') {
            return redirect()->route('reports.show', $report->id)
                ->with('error', 'Laporan ini belum diverifikasi oleh Group Leader.');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'feedback' => 'required_if:action,reject|nullable|string',
        ]);

        if ($request->action === 'approve') {
            $report->update([
                'status' => 'approved',
                'spv_id' => $user->id,
                'spv_feedback' => null,
            ]);
            return redirect()->route('reports.show', $report->id)
                ->with('success', 'Laporan berhasil disetujui (Approved).');
        } else {
            // SPV Reject: GL tetap tercatat, SPV tidak
            $report->update([
                'status' => 'rejected',
                // 'gl_id' tetap ada (sudah diverifikasi sebelumnya)
                'spv_id' => null,
                // 'gl_feedback' tetap (tidak diubah)
                'spv_feedback' => $request->feedback,
            ]);
            return redirect()->route('reports.show', $report->id)
                ->with('warning', 'Laporan telah ditolak dan dikembalikan ke Fuelman.');
        }
    }

    public function destroy($id)
    {
        $report = DailyReport::findOrFail($id);
        $user = Auth::user();

        // Authorization checks
        if ($user->isFuelman()) {
            if ($report->fuelman_id !== $user->id) {
                abort(403, 'Anda tidak memiliki hak akses untuk menghapus laporan ini.');
            }
            if (!in_array($report->status, ['draft', 'rejected'])) {
                return redirect()->route('reports.show', $report->id)
                    ->with('error', 'Laporan yang sudah diajukan atau disetujui tidak dapat dihapus.');
            }
        } elseif (!$user->isSpv()) {
            // Only Fuelman (owner) and Supervisor can delete reports
            abort(403, 'Hanya Fuelman pembuat laporan atau Supervisor yang dapat menghapus laporan.');
        }

        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'Laporan harian berhasil dihapus.');
    }

    /**
     * Helper to save report items and calculate automated values.
     */
    private function saveItems(DailyReport $report, array $itemsData)
    {
        \Log::info('=== SAVING ITEMS START ===');
        \Log::info('Total items received', ['count' => count($itemsData)]);
        
        // Log raw items data with more detail
        foreach ($itemsData as $index => $data) {
            \Log::info("Raw item {$index}", [
                'has_tank_id' => isset($data['tank_id']),
                'tank_id' => $data['tank_id'] ?? 'MISSING',
                'tank_id_empty' => empty($data['tank_id']),
                'sounding_pagi' => $data['sounding_pagi'] ?? 'missing',
                'sounding_sore' => $data['sounding_sore'] ?? 'missing',
                'all_keys' => array_keys($data),
            ]);
        }
        
        // Group items by tank_id to detect 3-row groups
        $itemsByTank = [];
        foreach ($itemsData as $index => $data) {
            if (empty($data['tank_id'])) {
                \Log::warning("!!! SKIPPING item at index {$index}: tank_id is empty or missing !!!", [
                    'tank_id_value' => $data['tank_id'] ?? 'NOT SET',
                    'has_key' => array_key_exists('tank_id', $data),
                ]);
                continue;
            }
            $tankId = $data['tank_id'];
            if (!isset($itemsByTank[$tankId])) {
                $itemsByTank[$tankId] = [];
            }
            $itemsByTank[$tankId][] = ['index' => $index, 'data' => $data];
            \Log::info("Added item {$index} to tank group {$tankId}");
        }
        
        \Log::info('Items grouped by tank', ['groups' => array_keys($itemsByTank), 'group_counts' => array_map('count', $itemsByTank)]);
        
        // Process each tank group
        foreach ($itemsByTank as $tankId => $items) {
            $tank = Tank::find($tankId);
            
            \Log::info("Processing tank {$tankId}", [
                'tank_code' => $tank?->code,
                'main_hole' => $tank?->main_hole,
                'row_count' => count($items),
            ]);

            // Check if user submitted all 3 rows for DEPAN+BELAKANG tank
            if ($tank && $tank->main_hole === '(DEPAN + BELAKANG) / 2' && count($items) === 3) {
                \Log::info("Tank {$tank->code} has 3 rows (DEPAN, BELAKANG, average), saving all with photos separately");
                $this->saveAvgMainHoleTankItems($report, $tank, $items);
                continue;
            }
            
            // Check if user is creating NEW report with this tank type (only 1 row submitted - legacy)
            if ($tank && $tank->main_hole === '(DEPAN + BELAKANG) / 2' && count($items) === 1) {
                \Log::info("Tank {$tank->code} is DEPAN+BELAKANG type, will create 3 rows (legacy mode)");
                // Legacy mode: create 3 rows from single data (photos only for DEPAN)
                $this->saveAvgMainHoleTankItemsLegacy($report, $tank, $items[0]['data']);
                continue;
            }

            // Normal single or multiple separate rows
            foreach ($items as $item) {
                $this->saveSingleItem($report, $tank, $item['data']);
            }
        }
    }
    
    private function saveAvgMainHoleTankItems(DailyReport $report, Tank $tank, array $groupedItems)
    {
        \Log::info("Creating 3 rows for DEPAN+BELAKANG tank", ['tank_code' => $tank->code, 'item_count' => count($groupedItems)]);
        
        // Expect exactly 3 items: DEPAN, BELAKANG, average
        // They come in order from frontend
        $depanData = $groupedItems[0] ?? null;
        $belakangData = $groupedItems[1] ?? null;
        $avgData = $groupedItems[2] ?? null;
        
        if (!$depanData || !$belakangData || !$avgData) {
            \Log::warning("Incomplete 3-row group data", ['count' => count($groupedItems)]);
            // Fallback: save whatever we have
            foreach ($groupedItems as $item) {
                $this->saveSingleItem($report, $tank, $item['data']);
            }
            return;
        }
        
        // Row 1: DEPAN
        $depanItem = $this->createItemFromData($tank, array_merge($depanData['data'], ['main_hole_variant' => 'DEPAN']));
        $report->items()->save($depanItem);
        \Log::info("Saved DEPAN row", ['item_id' => $depanItem->id]);
        
        // Save DEPAN photos
        if (!empty($depanData['data']['photos'])) {
            $context = trim(implode(' — ', array_filter([
                'Tangki ' . $tank->code,
                'DEPAN',
                $depanData['data']['keterangan'] ?? null,
            ])));
            $this->saveAttachmentPhotos($report, 'A', $depanData['data']['attachment_key'] ?? "item-{$tank->id}-depan", $context, $depanData['data']['photos']);
        }
        
        // Row 2: BELAKANG
        $belakangItem = $this->createItemFromData($tank, array_merge($belakangData['data'], ['main_hole_variant' => 'BELAKANG']));
        $report->items()->save($belakangItem);
        \Log::info("Saved BELAKANG row", ['item_id' => $belakangItem->id]);
        
        // Save BELAKANG photos
        if (!empty($belakangData['data']['photos'])) {
            $context = trim(implode(' — ', array_filter([
                'Tangki ' . $tank->code,
                'BELAKANG',
                $belakangData['data']['keterangan'] ?? null,
            ])));
            $this->saveAttachmentPhotos($report, 'A', $belakangData['data']['attachment_key'] ?? "item-{$tank->id}-belakang", $context, $belakangData['data']['photos']);
        }
        
        // Row 3: (DEPAN + BELAKANG) / 2
        $avgItem = $this->createItemFromData($tank, array_merge($avgData['data'], ['main_hole_variant' => '(DEPAN + BELAKANG) / 2']));
        $report->items()->save($avgItem);
        \Log::info("Saved (DEPAN + BELAKANG) / 2 row", ['item_id' => $avgItem->id]);
        
        // Save average photos
        if (!empty($avgData['data']['photos'])) {
            $context = trim(implode(' — ', array_filter([
                'Tangki ' . $tank->code,
                '(DEPAN + BELAKANG) / 2',
                $avgData['data']['keterangan'] ?? null,
            ])));
            $this->saveAttachmentPhotos($report, 'A', $avgData['data']['attachment_key'] ?? "item-{$tank->id}-avg", $context, $avgData['data']['photos']);
        }
    }
    
    // Legacy function for backward compatibility (when only 1 row submitted for DEPAN+BELAKANG tank)
    private function saveAvgMainHoleTankItemsLegacy(DailyReport $report, Tank $tank, array $data)
    {
        \Log::info("Creating 3 rows for DEPAN+BELAKANG tank (legacy mode)", ['tank_code' => $tank->code]);
        
        // Row 1: DEPAN (use input data as-is)
        $depanData = array_merge($data, ['main_hole_variant' => 'DEPAN']);
        $depanItem = $this->createItemFromData($tank, $depanData);
        $report->items()->save($depanItem);
        \Log::info("Saved DEPAN row", ['item_id' => $depanItem->id]);
        
        // Row 2: BELAKANG (keep petugas, clear other fields)
        $belakangData = [
            'tank_id' => $tank->id,
            'main_hole_variant' => 'BELAKANG',
            'sounding_pagi' => null,
            'liter_pagi' => null,
            'jam_pagi' => null,
            'petugas_pagi' => $data['petugas_pagi'] ?? null,
            'sounding_sore' => null,
            'liter_sore' => null,
            'jam_sore' => null,
            'petugas_sore' => $data['petugas_sore'] ?? null,
            'fm_pagi' => null,
            'fm_sore' => null,
            'keterangan' => null,
        ];
        $belakangItem = $this->createItemFromData($tank, $belakangData);
        $report->items()->save($belakangItem);
        \Log::info("Saved BELAKANG row", ['item_id' => $belakangItem->id]);
        
        // Row 3: (DEPAN + BELAKANG) / 2 (keep petugas, clear other fields)
        $avgData = [
            'tank_id' => $tank->id,
            'main_hole_variant' => '(DEPAN + BELAKANG) / 2',
            'sounding_pagi' => null,
            'liter_pagi' => null,
            'jam_pagi' => null,
            'petugas_pagi' => $data['petugas_pagi'] ?? null,
            'sounding_sore' => null,
            'liter_sore' => null,
            'jam_sore' => null,
            'petugas_sore' => $data['petugas_sore'] ?? null,
            'fm_pagi' => null,
            'fm_sore' => null,
            'keterangan' => null,
        ];
        $avgItem = $this->createItemFromData($tank, $avgData);
        $report->items()->save($avgItem);
        \Log::info("Saved (DEPAN + BELAKANG) / 2 row", ['item_id' => $avgItem->id]);
        
        // Save photos for DEPAN row only (legacy behavior)
        if (!empty($data['photos'])) {
            $context = trim(implode(' — ', array_filter([
                'Tangki ' . $tank->code,
                'DEPAN',
                $data['keterangan'] ?? null,
            ])));
            $this->saveAttachmentPhotos($report, 'A', $data['attachment_key'] ?? "item-{$tank->id}", $context, $data['photos']);
        }
    }
    
    private function saveSingleItem(DailyReport $report, ?Tank $tank, array $data)
    {
        $item = $this->createItemFromData($tank, $data);
        $report->items()->save($item);

        $context = trim(implode(' — ', array_filter([
            'Tangki ' . ($tank?->code ?? '-'),
            $tank?->main_hole,
            $data['keterangan'] ?? null,
        ])));
        $this->saveAttachmentPhotos($report, 'A', $data['attachment_key'] ?? "item-{$tank->id}", $context, $data['photos'] ?? []);
    }
    
    private function createItemFromData(?Tank $tank, array $data): DailyReportItem
    {
        // Get sounding values
        $soundingPagi = isset($data['sounding_pagi']) && $data['sounding_pagi'] !== '' ? (double)$data['sounding_pagi'] : null;
        $soundingSore = isset($data['sounding_sore']) && $data['sounding_sore'] !== '' ? (double)$data['sounding_sore'] : null;

        // Calculate liter from sounding using calibration data
        $literPagi = null;
        $literSore = null;
        
        if ($tank) {
            if ($soundingPagi !== null) {
                $literPagi = $tank->soundingToLiter($soundingPagi);
            }
            if ($soundingSore !== null) {
                $literSore = $tank->soundingToLiter($soundingSore);
            }
        }

        // Calculate Flow Meter Usage: fm_sore - fm_pagi (hanya jika KEDUA terisi)
        $fmPagi = isset($data['fm_pagi']) && $data['fm_pagi'] !== '' ? (double)$data['fm_pagi'] : null;
        $fmSore = isset($data['fm_sore']) && $data['fm_sore'] !== '' ? (double)$data['fm_sore'] : null;
        $fmPakai = null;
        if ($fmPagi !== null && $fmSore !== null) {
            $fmPakai = $fmSore - $fmPagi;
        }

        return new DailyReportItem([
            'tank_id' => $tank?->id,
            'main_hole_variant' => $data['main_hole_variant'] ?? null,
            'sounding_pagi' => $soundingPagi,
            'liter_pagi' => $literPagi,
            'jam_pagi' => $data['jam_pagi'] ?: null,
            'petugas_pagi' => $data['petugas_pagi'] ?: null,
            
            'sounding_sore' => $soundingSore,
            'liter_sore' => $literSore,
            'jam_sore' => $data['jam_sore'] ?: null,
            'petugas_sore' => $data['petugas_sore'] ?: null,
            
            'fm_pagi' => $fmPagi,
            'fm_sore' => $fmSore,
            'fm_pakai' => $fmPakai,
            'keterangan' => $data['keterangan'] ?: null,
        ]);
    }

    private function saveTransfers(DailyReport $report, array $transfersData)
    {
        foreach ($transfersData as $data) {
            // Skip empty rows
            if (empty($data['dari_tangki']) && empty($data['ke_tangki'])) {
                continue;
            }

            $spmAwal = isset($data['spm_awal']) && $data['spm_awal'] !== '' ? (double)$data['spm_awal'] : null;
            $spmAkhir = isset($data['spm_akhir']) && $data['spm_akhir'] !== '' ? (double)$data['spm_akhir'] : null;
            $spmHasil = null;
            if ($spmAwal !== null && $spmAkhir !== null) {
                $spmHasil = $spmAwal - $spmAkhir;
            }

            $ftAwal = isset($data['ft_awal']) && $data['ft_awal'] !== '' ? (double)$data['ft_awal'] : null;
            $ftAkhir = isset($data['ft_akhir']) && $data['ft_akhir'] !== '' ? (double)$data['ft_akhir'] : null;
            $ftHasil = null;
            if ($ftAwal !== null && $ftAkhir !== null) {
                $ftHasil = $ftAkhir - $ftAwal;
            }

            $fmAwal = isset($data['fm_awal']) && $data['fm_awal'] !== '' ? (double)$data['fm_awal'] : null;
            $fmAkhir = isset($data['fm_akhir']) && $data['fm_akhir'] !== '' ? (double)$data['fm_akhir'] : null;
            $fmJumlah = null;
            if ($fmAwal !== null && $fmAkhir !== null) {
                $fmJumlah = $fmAkhir - $fmAwal;
            }

            // Calculate liter values based on sounding hasil and tank calibration
            $spmLiter = null;
            $ftLiter = null;

            // Find SPM tank (dari_tangki)
            if ($spmHasil !== null && !empty($data['dari_tangki'])) {
                $spmTank = Tank::where('code', $data['dari_tangki'])->first();
                if ($spmTank) {
                    $spmLiter = $spmTank->soundingToLiter(abs($spmHasil));
                }
            }

            // Find FT tank (ke_tangki)
            if ($ftHasil !== null && !empty($data['ke_tangki'])) {
                $ftTank = Tank::where('code', $data['ke_tangki'])->first();
                if ($ftTank) {
                    $ftLiter = $ftTank->soundingToLiter(abs($ftHasil));
                }
            }

            $transfer = $report->transfers()->create([
                'dari_tangki'   => $data['dari_tangki'] ?: null,
                'ke_tangki'     => $data['ke_tangki'] ?: null,
                'spm_awal'      => $spmAwal,
                'spm_akhir'     => $spmAkhir,
                'spm_hasil'     => $spmHasil,
                'spm_liter'     => $spmLiter,
                'ft_awal'       => $ftAwal,
                'ft_akhir'      => $ftAkhir,
                'ft_hasil'      => $ftHasil,
                'ft_liter'      => $ftLiter,
                'fm_awal'       => $fmAwal,
                'fm_akhir'      => $fmAkhir,
                'fm_jumlah'     => $fmJumlah,
                'jam_mulai'     => $data['jam_mulai'] ?: null,
                'jam_selesai'   => $data['jam_selesai'] ?: null,
                'lama_transfer' => $data['lama_transfer'] ?: null,
            ]);

            $context = trim(implode(' — ', array_filter([
                'Transfer ' . ($data['dari_tangki'] ?: '-') . ' ke ' . ($data['ke_tangki'] ?: '-'),
                $data['lama_transfer'] ?? null,
            ])));
            $attachmentKey = "transfer-{$transfer->id}";
            $legacyAttachmentKey = $data['attachment_key'] ?? $attachmentKey;

            // Transfer dibuat ulang saat update. Pindahkan foto lama ke ID transfer
            // yang baru agar tetap tampil pada pembukaan form berikutnya.
            if ($legacyAttachmentKey !== $attachmentKey) {
                $report->attachments()
                    ->where('section', 'B')
                    ->where('attachment_key', $legacyAttachmentKey)
                    ->update(['attachment_key' => $attachmentKey]);
            }

            $this->saveAttachmentPhotos($report, 'B', $attachmentKey, $context, $data['photos'] ?? []);
        }
    }

    /** Save photos for an attachment set (no limit - validated at request level). */
    private function saveAttachmentPhotos(DailyReport $report, string $section, string $attachmentKey, string $context, array $files): void
    {
        $photos = array_values(array_filter($files, fn ($file) => $file instanceof UploadedFile));
        if ($photos === []) {
            return;
        }

        // No limit here - already validated at request level based on tank type
        foreach ($photos as $photo) {
            try {
                $disk = $this->attachmentDisk();
                \Log::info("Attempting to upload photo", [
                    'disk' => $disk,
                    'filename' => $photo->getClientOriginalName(),
                    'size' => $photo->getSize(),
                    'section' => $section,
                    'report_id' => $report->id,
                ]);
                
                $path = $photo->store("report-attachments/{$report->id}/section-{$section}", $disk);
                
                \Log::info("Photo uploaded successfully", [
                    'path' => $path,
                    'disk' => $disk,
                    'report_id' => $report->id,
                ]);
                
                $report->attachments()->create([
                    'section' => $section,
                    'attachment_key' => $attachmentKey,
                    'context' => $context,
                    'path' => $path,
                    'original_name' => $photo->getClientOriginalName(),
                ]);
            } catch (\Throwable $e) {
                \Log::error("Failed to upload photo", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'disk' => $disk ?? 'unknown',
                    'report_id' => $report->id,
                    'section' => $section,
                ]);
                // Re-throw to abort transaction
                throw new \RuntimeException("Gagal upload foto: " . $e->getMessage(), 0, $e);
            }
        }
    }

    /** Delete only attachments that belong to the report currently being edited. */
    private function deleteAttachments(DailyReport $report, array $attachmentIds): void
    {
        if ($attachmentIds === []) {
            return;
        }

        $attachments = $report->attachments()
            ->whereIn('id', array_unique($attachmentIds))
            ->get();

        foreach ($attachments as $attachment) {
            Storage::disk($this->attachmentDisk())->delete($attachment->path);
            $attachment->delete();
        }
    }

    /** Convert the legacy per-item attachment key to the stable per-tank key. */
    private function migrateLegacyItemAttachmentKeys(DailyReport $report): void
    {
        foreach ($report->items as $item) {
            $report->attachments()
                ->where('section', 'A')
                ->where('attachment_key', "item-{$item->id}")
                ->update(['attachment_key' => "item-{$item->tank_id}"]);
        }
    }

    /** Local storage is private; use Laravel's public disk for browser-visible attachments. */
    private function attachmentDisk(): string
    {
        $disk = config('filesystems.report_attachment_disk', 'public');

        return $disk === 'local' ? 'public' : $disk;
    }

    private function saveFlowmeters(DailyReport $report, array $flowmetersData)
    {
        foreach ($flowmetersData as $data) {
            // Skip empty rows
            if (empty($data['unit'])) {
                continue;
            }

            $awal = isset($data['awal_pagi']) && $data['awal_pagi'] !== '' ? (double)$data['awal_pagi'] : null;
            $akhir = isset($data['akhir_sore']) && $data['akhir_sore'] !== '' ? (double)$data['akhir_sore'] : null;
            $jumlah = null;
            if ($awal !== null && $akhir !== null) {
                $jumlah = round($akhir - $awal);
            }

            $report->flowmeters()->create([
                'unit'            => $data['unit'] ?: null,
                'jenis_flowmeter' => $data['jenis_flowmeter'] ?: null,
                'nomor_seri'      => $data['nomor_seri'] ?: null,
                'awal_pagi'       => $awal,
                'akhir_sore'      => $akhir,
                'jumlah_pakai'    => $jumlah,
            ]);
        }
    }
}

    /**
     * Show list of reports where user is a collaborator
     */
    public function collaborations(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isFuelman()) {
            abort(403, 'Hanya Fuelman yang dapat mengakses laporan kolaborasi.');
        }
        
        // Get filter parameters
        $search = $request->get('search');
        $status = $request->get('status');
        $sortOrder = $request->get('sort', 'desc');
        $perPage = $request->get('per_page', 15);
        
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 15;
        }
        
        $query = DailyReport::with(['fuelman', 'gl', 'spv', 'site'])
            ->where('collaborator_id', $user->id);
        
        // Filter by status
        if ($status) {
            $query->where('status', $status);
        }
        
        // Search in date or fuelman name
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereRaw("to_char(date, 'YYYY-MM-DD') LIKE ?", ["%{$search}%"])
                  ->orWhereHas('fuelman', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $query->orderBy('date', $sortOrder);
        
        $reports = $query->paginate($perPage);
        
        return view('reports.collaborations', compact('reports', 'search', 'status', 'sortOrder', 'perPage'));
    }
    
    /**
     * Add a collaborator to a report
     */
    public function addCollaborator(Request $request, $id)
    {
        $report = DailyReport::findOrFail($id);
        $user = Auth::user();
        
        // Only report creator can add collaborator
        if (!$user->isFuelman() || $report->fuelman_id !== $user->id) {
            abort(403, 'Hanya pembuat laporan yang dapat menambah kolaborator.');
        }
        
        // Validate collaborator_id
        $request->validate([
            'collaborator_id' => 'required|exists:users,id',
        ]);
        
        $collaboratorId = $request->collaborator_id;
        
        // Cannot add self as collaborator
        if ($collaboratorId == $user->id) {
            return back()->with('error', 'Anda tidak dapat menambahkan diri sendiri sebagai kolaborator.');
        }
        
        // Check if collaborator is fuelman
        $collaborator = \App\Models\User::find($collaboratorId);
        if (!$collaborator->isFuelman()) {
            return back()->with('error', 'Kolaborator harus memiliki role Fuelman.');
        }
        
        // Check if already has collaborator (max 1)
        if ($report->collaborator_id) {
            return back()->with('error', 'Laporan ini sudah memiliki kolaborator. Hapus kolaborator yang ada terlebih dahulu.');
        }
        
        // Add collaborator and save their name for history
        $report->update([
            'collaborator_id' => $collaboratorId,
            'collaborator_name' => $collaborator->name,
        ]);
        
        return back()->with('success', "Berhasil menambahkan {$collaborator->name} sebagai kolaborator.");
    }
    
    /**
     * Remove collaborator from a report
     */
    public function removeCollaborator($id)
    {
        $report = DailyReport::findOrFail($id);
        $user = Auth::user();
        
        // Only report creator can remove collaborator
        if (!$user->isFuelman() || $report->fuelman_id !== $user->id) {
            abort(403, 'Hanya pembuat laporan yang dapat menghapus kolaborator.');
        }
        
        if (!$report->collaborator_id) {
            return back()->with('error', 'Laporan ini tidak memiliki kolaborator.');
        }
        
        // Remove collaborator_id BUT keep collaborator_name for history
        $report->update(['collaborator_id' => null]);
        // NOTE: collaborator_name is NOT set to null, so dropdown will still appear
        
        return back()->with('success', 'Kolaborator berhasil dihapus.');
    }

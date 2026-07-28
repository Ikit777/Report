<?php

namespace App\Http\Controllers;

use App\Models\Flowmeter;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FlowmeterController extends Controller
{
    public function index()
    {
        if (!Auth::user()->isSpv() && !Auth::user()->isAdmin()) {
            abort(403, 'Hanya Supervisor dan Admin yang dapat mengelola flowmeter.');
        }

        $flowmeters = Flowmeter::with('site')
            ->join('sites', 'flowmeters.site_id', '=', 'sites.id')
            ->orderBy('sites.code')
            ->orderBy('flowmeters.unit')
            ->select('flowmeters.*')
            ->get();

        return view('flowmeters.index', compact('flowmeters'));
    }

    public function create()
    {
        if (!Auth::user()->isSpv() && !Auth::user()->isAdmin()) {
            abort(403, 'Hanya Supervisor dan Admin yang dapat menambah flowmeter.');
        }

        $sites = Site::where('is_active', true)->orderBy('code')->get();
        return view('flowmeters.create', compact('sites'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isSpv() && !Auth::user()->isAdmin()) {
            abort(403, 'Hanya Supervisor dan Admin yang dapat menambah flowmeter.');
        }

        $request->validate([
            'site_id' => 'required|exists:sites,id',
            'unit' => 'required|string|max:50',
            'jenis' => 'required|string|max:100',
            'nomor_seri' => 'nullable|string|max:100',
            'is_active' => 'required|boolean',
        ]);

        Flowmeter::create([
            'site_id' => $request->site_id,
            'unit' => $request->unit,
            'jenis' => $request->jenis,
            'nomor_seri' => $request->nomor_seri,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('flowmeters.index')
            ->with('success', 'Flowmeter berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if (!Auth::user()->isSpv() && !Auth::user()->isAdmin()) {
            abort(403, 'Hanya Supervisor dan Admin yang dapat mengubah flowmeter.');
        }

        $flowmeter = Flowmeter::findOrFail($id);
        $sites = Site::where('is_active', true)->orderBy('code')->get();
        return view('flowmeters.edit', compact('flowmeter', 'sites'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->isSpv() && !Auth::user()->isAdmin()) {
            abort(403, 'Hanya Supervisor dan Admin yang dapat mengubah flowmeter.');
        }

        $flowmeter = Flowmeter::findOrFail($id);

        $request->validate([
            'site_id' => 'required|exists:sites,id',
            'unit' => 'required|string|max:50',
            'jenis' => 'required|string|max:100',
            'nomor_seri' => 'nullable|string|max:100',
            'is_active' => 'required|boolean',
        ]);

        $flowmeter->update([
            'site_id' => $request->site_id,
            'unit' => $request->unit,
            'jenis' => $request->jenis,
            'nomor_seri' => $request->nomor_seri,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('flowmeters.index')
            ->with('success', 'Flowmeter berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (!Auth::user()->isSpv() && !Auth::user()->isAdmin()) {
            abort(403, 'Hanya Supervisor dan Admin yang dapat menghapus flowmeter.');
        }

        $flowmeter = Flowmeter::findOrFail($id);
        $flowmeter->delete();

        return redirect()->route('flowmeters.index')
            ->with('success', 'Flowmeter berhasil dihapus.');
    }

    /**
     * API endpoint untuk mendapatkan data flowmeter berdasarkan site
     */
    public function getBySite(Request $request, $siteId)
    {
        $flowmeters = Flowmeter::where('site_id', $siteId)
            ->where('is_active', true)
            ->orderBy('unit')
            ->get();

        return response()->json($flowmeters);
    }
}

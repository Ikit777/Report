<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiteController extends Controller
{
    public function index()
    {
        if (!Auth::user()->isSpv()) {
            abort(403, 'Hanya Supervisor yang dapat mengelola site.');
        }

        $sites = Site::orderBy('code')->paginate(15);

        return view('sites.index', compact('sites'));
    }

    public function create()
    {
        if (!Auth::user()->isSpv()) {
            abort(403, 'Hanya Supervisor yang dapat mengelola site.');
        }

        return view('sites.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isSpv()) {
            abort(403, 'Hanya Supervisor yang dapat mengelola site.');
        }

        $request->validate([
            'code' => 'required|string|max:10|unique:sites,code',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        Site::create([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('sites.index')
            ->with('success', 'Site berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if (!Auth::user()->isSpv()) {
            abort(403, 'Hanya Supervisor yang dapat mengelola site.');
        }

        $site = Site::findOrFail($id);

        return view('sites.edit', compact('site'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->isSpv()) {
            abort(403, 'Hanya Supervisor yang dapat mengelola site.');
        }

        $site = Site::findOrFail($id);

        $request->validate([
            'code' => 'required|string|max:10|unique:sites,code,' . $id,
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $site->update([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('sites.index')
            ->with('success', 'Site berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (!Auth::user()->isSpv()) {
            abort(403, 'Hanya Supervisor yang dapat mengelola site.');
        }

        $site = Site::findOrFail($id);
        
        // Check if site has reports
        if ($site->reports()->count() > 0) {
            return redirect()->route('sites.index')
                ->with('error', 'Site tidak dapat dihapus karena masih memiliki laporan.');
        }

        $site->delete();

        return redirect()->route('sites.index')
            ->with('success', 'Site berhasil dihapus.');
    }
}

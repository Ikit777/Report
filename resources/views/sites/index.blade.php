@extends('layouts.app')

@section('title', 'Kelola Site')

@section('content')
<div class="content-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1 style="margin: 0;">Kelola Site / Lokasi</h1>
    <a href="{{ route('sites.create') }}" class="btn btn-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Tambah Site
    </a>
</div>

<div class="card-table-container">
    <table class="table-list">
        <thead>
            <tr>
                <th style="width: 15%;">Kode</th>
                <th>Nama Site</th>
                <th style="width: 15%; text-align: center;">Status</th>
                <th style="width: 20%; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sites as $site)
            <tr>
                <td style="font-weight: 600; font-family: monospace;">{{ $site->code }}</td>
                <td>{{ $site->name }}</td>
                <td style="text-align: center;">
                    @if($site->is_active)
                        <span class="badge-success">Aktif</span>
                    @else
                        <span class="badge-danger">Nonaktif</span>
                    @endif
                </td>
                <td style="text-align: center;">
                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                        <a href="{{ route('sites.edit', $site->id) }}" class="btn btn-sm btn-primary" title="Edit">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </a>
                        <form action="{{ route('sites.destroy', $site->id) }}" method="POST" style="display: inline;" onsubmit="return confirmDelete(event, this)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                    Tidak ada data site.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($sites->hasPages())
<div style="margin-top: 1.5rem;">
    {{ $sites->links() }}
</div>
@endif
@endsection

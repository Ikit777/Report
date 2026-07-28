@extends('layouts.app')

@section('title', 'Kelola Flowmeter')

@section('content')
<div class="content-header">
    <div>
        <h1 class="page-title">Kelola Flowmeter</h1>
        <p class="page-subtitle">Manajemen data flowmeter untuk setiap site.</p>
    </div>
    <a href="{{ route('flowmeters.create') }}" class="btn btn-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Tambah Flowmeter
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom: 1.5rem;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
        {{ session('error') }}
    </div>
@endif

<div class="card-table-container">
    <h2 class="card-title">Daftar Flowmeter</h2>
    <div class="table-responsive">
        <table class="table-list">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Site</th>
                    <th>Unit</th>
                    <th>Jenis Flowmeter</th>
                    <th>Nomor Seri</th>
                    <th>Status</th>
                    <th style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($flowmeters as $index => $flowmeter)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $flowmeter->site->code }}</strong>
                            <br>
                            <span style="color: var(--text-muted); font-size: 0.85rem;">{{ $flowmeter->site->name }}</span>
                        </td>
                        <td><strong>{{ $flowmeter->unit }}</strong></td>
                        <td>{{ $flowmeter->jenis }}</td>
                        <td>{{ $flowmeter->nomor_seri ?? '-' }}</td>
                        <td>
                            @if($flowmeter->is_active)
                                <span class="badge badge-approved" style="background-color: var(--success-light); color: #065f46;">Aktif</span>
                            @else
                                <span class="badge badge-draft">Non-Aktif</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem; align-items: center;">
                                <a href="{{ route('flowmeters.edit', $flowmeter->id) }}" class="btn-icon btn-icon-edit" title="Ubah">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('flowmeters.destroy', $flowmeter->id) }}" method="POST" onsubmit="return confirmDelete(event, this);" style="margin: 0; display: inline-flex;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon btn-icon-delete" title="Hapus">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                        <td colspan="7" style="text-align: center; color: var(--text-muted);">
                            Belum ada data flowmeter. Silakan tambah flowmeter baru.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function confirmDelete(event, form) {
    event.preventDefault();
    if (confirm('Apakah Anda yakin ingin menghapus flowmeter ini?')) {
        form.submit();
    }
    return false;
}
</script>
@endsection

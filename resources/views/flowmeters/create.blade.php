@extends('layouts.app')

@section('title', 'Tambah Flowmeter')

@section('content')
<div class="content-header">
    <div>
        <h1 class="page-title">Tambah Flowmeter Baru</h1>
        <p class="page-subtitle">Tambahkan data flowmeter untuk site tertentu.</p>
    </div>
    <a href="{{ route('flowmeters.index') }}" class="btn btn-secondary">Kembali</a>
</div>

<div class="card-table-container" style="max-width: 700px;">
    <h2 class="card-title">Form Data Flowmeter</h2>

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 1.5rem; flex-direction: column; align-items: flex-start; gap: 0.25rem;">
            @foreach ($errors->all() as $error)
                <span>• {{ $error }}</span>
            @endforeach
        </div>
    @endif

    <form action="{{ route('flowmeters.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="site_id">Site <span style="color: var(--danger);">*</span></label>
            <select name="site_id" id="site_id" class="form-control" required>
                <option value="">-- Pilih Site --</option>
                @foreach($sites as $site)
                    <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>
                        {{ $site->code }} - {{ $site->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="unit">Unit / Nama Flowmeter <span style="color: var(--danger);">*</span></label>
            <input type="text" name="unit" id="unit" class="form-control" value="{{ old('unit') }}" placeholder="Contoh: FM-001, Unit A" required>
            <small style="color: var(--text-muted); font-size: 0.8rem;">Nama atau kode unit flowmeter</small>
        </div>

        <div class="form-group">
            <label for="jenis">Jenis Flowmeter <span style="color: var(--danger);">*</span></label>
            <input type="text" name="jenis" id="jenis" class="form-control" value="{{ old('jenis') }}" placeholder="Contoh: Digital, Analog, Turbine" required>
            <small style="color: var(--text-muted); font-size: 0.8rem;">Tipe atau jenis flowmeter</small>
        </div>

        <div class="form-group">
            <label for="nomor_seri">Nomor Seri</label>
            <input type="text" name="nomor_seri" id="nomor_seri" class="form-control" value="{{ old('nomor_seri') }}" placeholder="Contoh: SN-123456789">
            <small style="color: var(--text-muted); font-size: 0.8rem;">Serial number flowmeter (opsional)</small>
        </div>

        <div class="form-group">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="cursor: pointer;">
                <label for="is_active" style="margin: 0; cursor: pointer; user-select: none;">Flowmeter Aktif</label>
            </div>
            <small style="color: var(--text-muted); font-size: 0.8rem; margin-left: 1.5rem;">Centang jika flowmeter ini sedang aktif digunakan</small>
        </div>

        <div style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Simpan Flowmeter</button>
            <a href="{{ route('flowmeters.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection

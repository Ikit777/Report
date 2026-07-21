@extends('layouts.app')

@section('title', 'Tambah Site Baru')

@section('content')
<div class="content-header">
    <div>
        <h1 class="page-title">Tambah Site Baru</h1>
        <p class="page-subtitle">Tambahkan lokasi site untuk laporan harian</p>
    </div>
    <a href="{{ route('sites.index') }}" class="btn btn-secondary">Kembali</a>
</div>

<div class="card-table-container">
    <h2 class="card-title">Form Site Baru</h2>
    
    <form action="{{ route('sites.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="code">Kode Site <span style="color: #dc2626;">*</span></label>
            <input type="text" name="code" id="code" class="form-control" value="{{ old('code') }}" placeholder="Contoh: SPT1" required>
            @error('code')
                <span style="color: #dc2626; font-size: 0.875rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="name">Nama Site <span style="color: #dc2626;">*</span></label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Sungai Puting" required>
            @error('name')
                <span style="color: #dc2626; font-size: 0.875rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="is_active">Status</label>
            <select name="is_active" id="is_active" class="form-control">
                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
        </div>

        <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">Simpan Site</button>
            <a href="{{ route('sites.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection

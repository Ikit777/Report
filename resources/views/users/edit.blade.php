@extends('layouts.app')

@section('title', 'Ubah Pengguna')

@section('content')
<div class="content-header">
    <div>
        <h1 class="page-title">Ubah Data Pengguna</h1>
        <p class="page-subtitle">Perbarui informasi dan hak akses pengguna.</p>
    </div>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
</div>

<div class="card-table-container" style="max-width: 600px;">
    <h2 class="card-title">Form Edit Pengguna: <span style="color: var(--primary);">{{ $user->name }}</span></h2>

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 1.5rem; flex-direction: column; align-items: flex-start; gap: 0.25rem;">
            @foreach ($errors->all() as $error)
                <span>• {{ $error }}</span>
            @endforeach
        </div>
    @endif

    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="form-group">
            <label for="employee_id">ID Pegawai</label>
            <input type="text" name="employee_id" id="employee_id" class="form-control" value="{{ old('employee_id', $user->employee_id) }}" placeholder="Masukkan ID atau Nomor Pegawai (contoh: EMP123)">
        </div>

        <div class="form-group">
            <label for="email">Alamat Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="form-group">
            <label for="role">Role / Hak Akses</label>
            <select name="role" id="role" class="form-control" required>
                <option value="fuelman" {{ (old('role', $user->role) === 'fuelman') ? 'selected' : '' }}>Fuelman</option>
                <option value="group_leader" {{ (old('role', $user->role) === 'group_leader') ? 'selected' : '' }}>Group Leader</option>
                <option value="supervisor" {{ (old('role', $user->role) === 'supervisor') ? 'selected' : '' }}>Supervisor</option>
            </select>
        </div>

        <hr style="border-color: var(--border-color); margin: 1.5rem 0;">

        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">
            Biarkan kosong jika tidak ingin mengubah password.
        </p>

        <div class="form-group">
            <label for="password">Password Baru (Opsional)</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 6 karakter">
        </div>

        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi password baru">
        </div>

        <div style="margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection

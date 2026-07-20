@extends('layouts.app')

@section('title', 'Tambah Pengguna')

@section('content')
<div class="content-header">
    <div>
        <h1 class="page-title">Tambah Pengguna Baru</h1>
        <p class="page-subtitle">Daftarkan akun pengguna baru ke dalam sistem.</p>
    </div>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
</div>

<div class="card-table-container" style="max-width: 600px;">
    <h2 class="card-title">Form Data Pengguna</h2>

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 1.5rem; flex-direction: column; align-items: flex-start; gap: 0.25rem;">
            @foreach ($errors->all() as $error)
                <span>• {{ $error }}</span>
            @endforeach
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
        </div>

        <div class="form-group">
            <label for="employee_id">ID Pegawai</label>
            <input type="text" name="employee_id" id="employee_id" class="form-control" value="{{ old('employee_id') }}" placeholder="Masukkan ID atau Nomor Pegawai (contoh: EMP123)">
        </div>

        <div class="form-group">
            <label for="email">Alamat Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="contoh@domain.com" required>
        </div>

        <div class="form-group">
            <label for="role">Role / Hak Akses</label>
            <select name="role" id="role" class="form-control" required>
                <option value="" disabled selected>-- Pilih Role --</option>
                <option value="fuelman" {{ old('role') === 'fuelman' ? 'selected' : '' }}>Fuelman</option>
                <option value="group_leader" {{ old('role') === 'group_leader' ? 'selected' : '' }}>Group Leader</option>
                <option value="supervisor" {{ old('role') === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
            </select>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 6 karakter" required>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi password" required>
        </div>

        <div style="margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Tambah Pengguna</button>
        </div>
    </form>
</div>
@endsection

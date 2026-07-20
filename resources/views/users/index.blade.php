@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="content-header">
    <div>
        <h1 class="page-title">Manajemen Pengguna</h1>
        <p class="page-subtitle">Kelola akun pengguna dan hak akses di dalam sistem.</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Tambah Pengguna
    </a>
</div>

<div class="card-table-container">
    <h2 class="card-title">Daftar Pengguna Sistem</h2>
    <div class="table-responsive">
        <table class="table-list">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Nama</th>
                    <th>ID Pegawai</th>
                    <th>Email</th>
                    <th>Akses Role</th>
                    <th style="width: 200px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div class="avatar" style="width: 36px; height: 36px; font-size: 0.9rem; flex-shrink: 0;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <strong>{{ $user->name }}</strong>
                            </div>
                        </td>
                        <td>{{ $user->employee_id ?? '-' }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->isFuelman())
                                <span class="badge badge-submitted">Fuelman</span>
                            @elseif($user->isGl())
                                <span class="badge badge-verified">Group Leader</span>
                            @elseif($user->isSpv())
                                <span class="badge badge-approved">Supervisor</span>
                            @else
                                <span class="badge badge-draft">{{ $user->role }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.25rem; align-items: center;">
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-secondary" style="font-size: 0.8rem; padding: 6px 12px; margin: 0;">
                                    Ubah
                                </a>
                                @if($user->id !== Auth::id())
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirmDelete(event, this);" style="margin: 0; display: inline-flex;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" style="font-size: 0.8rem; padding: 6px 12px; margin: 0;">Hapus</button>
                                    </form>
                                @else
                                    <span style="font-size: 0.75rem; color: var(--text-muted); padding: 6px 8px;">(Anda)</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

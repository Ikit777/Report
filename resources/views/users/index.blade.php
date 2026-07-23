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
                            <div style="display: flex; gap: 0.5rem; align-items: center;">
                                <a href="{{ route('users.edit', $user->id) }}" class="btn-icon btn-icon-edit" title="Ubah">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </a>
                                @if($user->id !== Auth::id())
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirmDelete(event, this);" style="margin: 0; display: inline-flex;">
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

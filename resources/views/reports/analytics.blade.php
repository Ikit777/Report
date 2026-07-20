@extends('layouts.app')

@section('title', 'Rekap & Analisis Penggunaan')

@section('styles')
<style>
    .usage-chart {
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .usage-chart-title {
        margin: 0 0 1.25rem;
        font-size: 1.1rem;
        color: var(--text-primary);
    }

    .usage-chart-subtitle {
        margin: -0.85rem 0 1.25rem;
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .usage-chart-bars {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 1rem;
        align-items: end;
    }

    .usage-chart-item {
        min-width: 0;
    }

    .usage-chart-value {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        text-align: center;
    }

    .usage-chart-plot {
        position: relative;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        height: 160px;
        padding: 0 0.75rem;
        border-bottom: 2px solid var(--border-color);
        background: repeating-linear-gradient(
            to bottom,
            transparent 0,
            transparent 39px,
            var(--border-color) 40px
        );
    }

    .usage-chart-bar {
        width: min(72%, 68px);
        min-height: 0;
        border-radius: 8px 8px 2px 2px;
        background: linear-gradient(90deg, var(--info), var(--primary));
        transition: height 180ms ease;
    }

    .usage-chart-zero {
        width: 12px;
        height: 12px;
        margin-bottom: -7px;
        border: 3px solid var(--info);
        border-radius: 50%;
        background: var(--bg-secondary);
    }

    .usage-chart-label {
        display: block;
        margin-top: 0.75rem;
        text-align: right;
        font-weight: 600;
        color: var(--text-primary);
        text-align: center;
    }

    @media (max-width: 640px) {
        .usage-chart-bars {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div>
        <h1 class="page-title">Rekap & Analisis Penggunaan BBM</h1>
        <p class="page-subtitle">Ringkasan total pemakaian BBM berdasarkan data laporan yang disetujui (Approved).</p>
    </div>
</div>

@php
    $maxUsage = max(1, (float) $usageData->max('total_pakai'));
@endphp
<div class="card-table-container usage-chart">
    <h2 class="usage-chart-title">Grafik Pemakaian BBM per Tangki</h2>
    <p class="usage-chart-subtitle">Total pemakaian dari laporan yang telah disetujui.</p>
    @if($usageData->isNotEmpty())
        <div class="usage-chart-bars">
            @foreach($usageData as $data)
                @php
                    $usage = (float) $data->total_pakai;
                    $percentage = min(100, max(0, ($usage / $maxUsage) * 100));
                @endphp
                <div class="usage-chart-item" role="img" aria-label="Pemakaian {{ $data->tank->code }}: {{ number_format($usage, 0, ',', '.') }} liter">
                    <span class="usage-chart-value">{{ number_format($usage, 0, ',', '.') }} L</span>
                    <div class="usage-chart-plot">
                        @if($usage > 0)
                            <div class="usage-chart-bar" style="height: {{ $percentage }}%;"></div>
                        @else
                            <span class="usage-chart-zero"></span>
                        @endif
                    </div>
                    <span class="usage-chart-label">{{ $data->tank->code }}<br>{{ $data->tank->main_hole }}</span>
                </div>
            @endforeach
        </div>
    @else
        <p style="margin: 0; color: var(--text-muted);">Belum ada data pemakaian dari laporan yang disetujui.</p>
    @endif
</div>

<div class="card-table-container">
    <h2 class="card-title">Riwayat Laporan Disetujui (Approved)</h2>
    <div class="table-responsive">
        <table class="table-list">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Fuelman</th>
                    <th>GL Pemverifikasi</th>
                    <th>SPV Penyetuju</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($approvedReports as $report)
                    <tr>
                        <td><strong>{{ $report->date->format('d-m-Y') }}</strong></td>
                        <td>{{ $report->fuelman->name }}</td>
                        <td>{{ $report->gl ? $report->gl->name : '-' }}</td>
                        <td>{{ $report->spv ? $report->spv->name : '-' }}</td>
                        <td><span class="badge badge-approved">Approved</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted);">Belum ada laporan disetujui.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

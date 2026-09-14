@extends('layouts.master')

@section('title', 'Detail Alat')

@section('content')
<div class="row justify-content-lg-center">
    <div class="col-lg-9">

        <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h3 class="page-title mb-1">Detail Alat</h3>
                <ul class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('alat.index') }}">Alat</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ul>
            </div>
            <div>
                <a href="{{ route('alat.index') }}" class="btn btn-light border">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                <a href="{{ route('alat.edit', $alat->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Edit Alat
                </a>
            </div>
        </div>

        {{-- Header card: identitas alat --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                             style="width:56px;height:56px;background:#eef2ff;">
                            <i class="fas fa-toolbox fa-lg" style="color:#4f46e5;"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 fw-semibold">{{ $alat->nama_alat }}</h4>
                            <div class="text-muted small">
                                Kode: <code class="bg-light px-2 py-1 rounded">{{ $alat->kode_alat }}</code>
                            </div>
                        </div>
                    </div>

                    @php
                        $kondisiMap = [
                            'baik'  => ['bg' => '#e6f7ec', 'text' => '#15803d', 'icon' => 'fa-check-circle'],
                            'rusak' => ['bg' => '#fdeaea', 'text' => '#dc2626', 'icon' => 'fa-times-circle'],
                        ];
                        $k = $kondisiMap[$alat->kondisi] ?? ['bg' => '#fef6e7', 'text' => '#d97706', 'icon' => 'fa-exclamation-circle'];
                    @endphp
                    <span class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill fw-medium"
                          style="background:{{ $k['bg'] }}; color:{{ $k['text'] }};">
                        <i class="fas {{ $k['icon'] }}"></i>
                        {{ ucfirst($alat->kondisi) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Stat cards: total, tersedia, terpakai --}}
        @php
            $terpakai = $alat->jumlah_total - $alat->jumlah_tersedia;
            $persenTersedia = $alat->jumlah_total > 0
                ? round(($alat->jumlah_tersedia / $alat->jumlah_total) * 100)
                : 0;
        @endphp
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-1">Jumlah Total</div>
                        <div class="fs-3 fw-bold">{{ $alat->jumlah_total }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-1">Tersedia</div>
                        <div class="fs-3 fw-bold text-success">{{ $alat->jumlah_tersedia }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-1">Sedang Dipakai</div>
                        <div class="fs-3 fw-bold text-warning">{{ $terpakai }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Progress ketersediaan --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between small text-muted mb-2">
                    <span>Ketersediaan Alat</span>
                    <span>{{ $persenTersedia }}%</span>
                </div>
                <div class="progress" style="height:10px; border-radius:10px;">
                    <div class="progress-bar bg-success" role="progressbar"
                         style="width: {{ $persenTersedia }}%; border-radius:10px;"
                         aria-valuenow="{{ $persenTersedia }}" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail informasi --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h6 class="fw-semibold mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Lengkap</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="text-muted small mb-1">Kategori</div>
                        <div class="fw-medium">{{ $alat->kategori->nama_kategori ?? '-' }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="text-muted small mb-1">Lokasi</div>
                        <div class="fw-medium"><i class="fas fa-map-marker-alt text-muted me-1"></i>{{ $alat->lokasi }}</div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small mb-1">Keterangan</div>
                        <div class="fw-medium">{{ $alat->keterangan ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
@extends('layouts.master')

@section('title', 'Laporan')

@section('content')
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Laporan</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Laporan</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.index') }}" id="filterForm">
                {{-- Pertahankan tab aktif --}}
                <input type="hidden" name="tab" value="{{ $activeTab }}">

                <div class="row">
                    @if ($activeTab == 'peminjaman')
                        {{-- Filter untuk Peminjaman --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ request('start_date') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tanggal Akhir</label>
                                <input type="date" name="end_date" class="form-control"
                                    value="{{ request('end_date') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">Semua Status</option>
                                    <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                                    <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                                    <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                                </select>
                            </div>
                        </div>
                    @elseif ($activeTab == 'alat')
                        {{-- Filter untuk Alat --}}
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Kategori</label>
                                <select name="kategori" class="form-control">
                                    <option value="">Semua</option>
                                    @foreach ($kategori as $kat)
                                        <option value="{{ $kat->id }}" {{ request('kategori') == $kat->id ? 'selected' : '' }}>
                                            {{ $kat->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Kondisi</label>
                                <select name="kondisi" class="form-control">
                                    <option value="">Semua</option>
                                    <option value="baik" {{ request('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="rusak" {{ request('kondisi') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                                    <option value="perbaikan" {{ request('kondisi') == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Stok</label>
                                <select name="stok" class="form-control">
                                    <option value="">Semua</option>
                                    <option value="aman" {{ request('stok') == 'aman' ? 'selected' : '' }}>Aman (>5)</option>
                                    <option value="sedikit" {{ request('stok') == 'sedikit' ? 'selected' : '' }}>Sedikit (1-5)</option>
                                    <option value="habis" {{ request('stok') == 'habis' ? 'selected' : '' }}>Habis (0)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Lokasi</label>
                                <input type="text" name="lokasi" class="form-control" value="{{ request('lokasi') }}" placeholder="Cari lokasi...">
                            </div>
                        </div>
                    @elseif ($activeTab == 'pengembalian')
                        {{-- Filter untuk Pengembalian --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ request('start_date') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tanggal Akhir</label>
                                <input type="date" name="end_date" class="form-control"
                                    value="{{ request('end_date') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Keterlambatan</label>
                                <select name="keterlambatan" class="form-control">
                                    <option value="">Semua</option>
                                    <option value="tepat" {{ request('keterlambatan') == 'tepat' ? 'selected' : '' }}>Tepat Waktu</option>
                                    <option value="terlambat" {{ request('keterlambatan') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                                </select>
                            </div>
                        </div>
                    @endif

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-4" id="reportTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ $activeTab == 'peminjaman' ? 'active' : '' }}"
                href="{{ route('reports.index', ['tab' => 'peminjaman'] + request()->except('tab')) }}">
                <i class="fas fa-book me-1"></i> Peminjaman
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab == 'alat' ? 'active' : '' }}"
                href="{{ route('reports.index', ['tab' => 'alat'] + request()->except('tab')) }}">
                <i class="fas fa-tools me-1"></i> Alat
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab == 'pengembalian' ? 'active' : '' }}"
                href="{{ route('reports.index', ['tab' => 'pengembalian'] + request()->except('tab')) }}">
                <i class="fas fa-undo-alt me-1"></i> Pengembalian
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Peminjaman Tab -->
        @if ($activeTab == 'peminjaman')
            <div class="tab-pane active">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Laporan Peminjaman</h4>
                        <div class="card-action d-flex">
                            <a href="{{ route('reports.export', ['type' => 'peminjaman'] + request()->all()) }}"
                                class="btn btn-danger btn-sm me-2">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </a>
                            <a href="{{ route('reports.export-excel', ['type' => 'peminjaman'] + request()->all()) }}"
                                class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Peminjaman</h5>
                                        <h3>{{ $totalPeminjaman }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Aktif</h5>
                                        <h3>{{ $peminjamanAktif }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Dikembalikan</h5>
                                        <h3>{{ $peminjamanDikembalikan }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Terlambat</h5>
                                        <h3>{{ $peminjamanTerlambat }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>ID</th>
                                        <th>Peminjam</th>
                                        <th>Alat</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Tanggal Rencana</th>
                                        <th>Tanggal Kembali</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($peminjaman as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $item->user->name ?? '-' }}</td>
                                            <td>
                                                @foreach ($item->detailPeminjaman as $detail)
                                                    - {{ $detail->alat->nama_alat ?? '-' }} ({{ $detail->jumlah_pinjam }}
                                                    pcs)<br>
                                                @endforeach
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('d/m/Y') }}</td>
                                            <td>{{ $item->tanggal_kembali_realisasi ? \Carbon\Carbon::parse($item->tanggal_kembali_realisasi)->format('d/m/Y') : '-' }}</td>
                                            <td>
                                                @php
                                                    $badgeClass = match ($item->status) {
                                                        'dipinjam' => 'badge bg-info',
                                                        'dikembalikan' => 'badge bg-success',
                                                        'terlambat' => 'badge bg-danger',
                                                        default => 'badge bg-secondary',
                                                    };
                                                @endphp
                                                <span class="{{ $badgeClass }}">{{ ucfirst($item->status) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Tidak ada data peminjaman</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Alat Tab -->
        @if ($activeTab == 'alat')
            <div class="tab-pane active">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Laporan Data Alat</h4>
                        <div class="card-action d-flex">
                            <a href="{{ route('reports.export', ['type' => 'alat'] + request()->all()) }}"
                                class="btn btn-danger btn-sm me-2">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </a>
                            <a href="{{ route('reports.export-excel', ['type' => 'alat'] + request()->all()) }}"
                                class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Alat</h5>
                                        <h3>{{ $totalAlat }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Tersedia</h5>
                                        <h3>{{ $alatTersedia }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Dipinjam</h5>
                                        <h3>{{ $alatDipinjam }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Rusak</h5>
                                        <h3>{{ $alatRusak }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Nama Alat</th>
                                        <th>Kategori</th>
                                        <th>Kondisi</th>
                                        <th>Total Stok</th>
                                        <th>Tersedia</th>
                                        <th>Lokasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($alat as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->kode_alat }}</td>
                                            <td>{{ $item->nama_alat }}</td>
                                            <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                                            <td>
                                                @php
                                                    $badgeClass = match ($item->kondisi) {
                                                        'baik' => 'badge bg-success',
                                                        'rusak' => 'badge bg-danger',
                                                        'perbaikan' => 'badge bg-warning',
                                                        default => 'badge bg-secondary',
                                                    };
                                                @endphp
                                                <span class="{{ $badgeClass }}">{{ ucfirst($item->kondisi) }}</span>
                                            </td>
                                            <td>{{ $item->jumlah_total }}</td>
                                            <td>{{ $item->jumlah_tersedia }}</td>
                                            <td>{{ $item->lokasi }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Tidak ada data alat</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Pengembalian Tab -->
        @if ($activeTab == 'pengembalian')
            <div class="tab-pane active">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Laporan Pengembalian</h4>
                        <div class="card-action d-flex">
                            <a href="{{ route('reports.export', ['type' => 'pengembalian'] + request()->all()) }}"
                                class="btn btn-danger btn-sm me-2">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </a>
                            <a href="{{ route('reports.export-excel', ['type' => 'pengembalian'] + request()->all()) }}"
                                class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Pengembalian</h5>
                                        <h3>{{ $totalPengembalian }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Tepat Waktu</h5>
                                        <h3>{{ $tepatWaktu }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Terlambat</h5>
                                        <h3>{{ $terlambat }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Kondisi Baik</h5>
                                        <h3>{{ $kondisiBaik }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>ID Pinjam</th>
                                        <th>Peminjam</th>
                                        <th>Alat</th>
                                        <th>Tgl Kembali</th>
                                        <th>Status</th>
                                        <th>Denda</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pengembalian as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->peminjaman_id }}</td>
                                            <td>{{ $item->peminjaman->user->name ?? '-' }}</td>
                                            <td>
                                                @foreach ($item->peminjaman->detailPeminjaman as $detail)
                                                    - {{ $detail->alat->nama_alat ?? '-' }} ({{ $detail->jumlah_pinjam }}
                                                    pcs)<br>
                                                @endforeach
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali_realisasi)->format('d/m/Y') }}</td>
                                            <td>
                                                @if ($item->peminjaman && $item->tanggal_kembali_realisasi <= $item->peminjaman->tanggal_kembali_rencana)
                                                    <span class="badge bg-success">Tepat Waktu</span>
                                                @else
                                                    <span class="badge bg-danger">Terlambat</span>
                                                @endif
                                            </td>
                                            <td>Rp {{ number_format($item->denda, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data pengembalian</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
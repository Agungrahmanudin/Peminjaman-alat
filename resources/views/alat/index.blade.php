@extends('layouts.master')

@section('title', 'Alat')

@section('content')
    <div class="row justify-content-lg-center">
        <div class="col-lg-10">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Daftar Alat</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Alat</li>
                        </ul>
                    </div>
                    @if (auth()->user()->role !== 'peminjam')
                        <div class="col-auto">
                            <a href="{{ route('alat.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Tambah Alat
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">

                    {{-- Form Filter --}}
                    <div class="card mb-3">
                        <div class="card-body">
                            <form method="GET" action="{{ route('alat.index') }}" id="filterForm">
                                <div class="row g-3 align-items-center mb-2">

                                    <div class="col-md-3">
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control" name="search"
                                                placeholder="Cari nama/kode..." value="{{ request('search') }}">
                                            <button class="btn btn-outline-primary" type="submit">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <select class="form-select form-select-sm" name="kategori_id"
                                            onchange="this.form.submit()">
                                            <option value="">Semua Kategori</option>
                                            @foreach ($kategoriList as $kategori)
                                                <option value="{{ $kategori->id }}"
                                                    {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                                    {{ $kategori->nama_kategori }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <select class="form-select form-select-sm" name="kondisi"
                                            onchange="this.form.submit()">
                                            <option value="">Semua Kondisi</option>
                                            <option value="baik"
                                                {{ request('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                                            <option value="rusak"
                                                {{ request('kondisi') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                                            <option value="perbaikan"
                                                {{ request('kondisi') == 'perbaikan' ? 'selected' : '' }}>Perbaikan
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <select class="form-select form-select-sm" name="stok"
                                            onchange="this.form.submit()">
                                            <option value="">Semua Stok</option>
                                            <option value="tersedia" {{ request('stok') == 'tersedia' ? 'selected' : '' }}>
                                                Tersedia</option>
                                            <option value="kosong" {{ request('stok') == 'kosong' ? 'selected' : '' }}>
                                                Kosong</option>
                                            <option value="kritis" {{ request('stok') == 'kritis' ? 'selected' : '' }}>
                                                Stok Kritis</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="fas fa-filter me-1"></i> Filter
                                            </button>
                                            <a href="{{ route('alat.index') }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-redo me-1"></i> Reset
                                            </a>
                                            {{-- Hapus Semua: hanya admin & petugas --}}
                                            @if (auth()->user()->role !== 'peminjam' && $alat->count() > 0)
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteAllModal">
                                                    <i class="fas fa-trash-alt me-1"></i> Hapus Semua
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Modal Hapus Semua (hanya admin & petugas) --}}
                    @if (auth()->user()->role !== 'peminjam' && $alat->count() > 0)
                        <div class="modal fade" id="deleteAllModal" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Hapus Semua
                                            Alat</h5>
                                        <button type="button" class="btn-close btn-close-white"
                                            data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <i class="fas fa-trash-alt fa-4x text-danger mb-3"></i>
                                        <h5 class="text-danger fw-bold mb-3">PERHATIAN!</h5>
                                        <p>Anda akan menghapus <strong class="text-danger">{{ $alat->total() }}
                                                alat</strong> secara permanen.</p>
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-circle me-2"></i>
                                            <strong>Tindakan ini tidak dapat dibatalkan!</strong>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('alat.deleteAll') }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash-alt me-1"></i> Ya, Hapus Semua
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Tabel Alat -->
                    <!-- Tabel Alat -->
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Kode Alat</th>
                                    <th>Nama Alat</th>
                                    <th>Kategori</th>
                                    <th>Kondisi</th>
                                    <th>Tersedia</th>
                                    <th>Lokasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($alat as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>

                                        <td>
                                            <code>{{ $item->kode_alat }}</code>
                                        </td>

                                        <td>
                                            <strong>{{ $item->nama_alat }}</strong>

                                            @if ($item->keterangan)
                                                <br>
                                                <small class="text-muted">
                                                    {{ Str::limit($item->keterangan, 50) }}
                                                </small>
                                            @endif
                                        </td>

                                        <td>
                                            <span class="badge bg-info">
                                                {{ $item->kategori->nama_kategori ?? 'Tidak ada' }}
                                            </span>
                                        </td>

                                        <td>
                                            @if ($item->kondisi == 'baik')
                                                <span class="badge bg-success">
                                                    Baik
                                                </span>
                                            @elseif($item->kondisi == 'rusak')
                                                <span class="badge bg-danger">
                                                    Rusak
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    Perbaikan
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            @php
                                                $tersedia = $item->jumlah_tersedia ?? 0;
                                            @endphp

                                            <span
                                                class="badge {{ $tersedia > 5 ? 'bg-success' : ($tersedia > 0 ? 'bg-warning' : 'bg-danger') }}">
                                                {{ $tersedia }}
                                            </span>
                                        </td>

                                        <td>
                                            {{ $item->lokasi }}
                                        </td>

                                        {{-- ===== AKSI ===== --}}
                                        <td>
                                            <div class="d-flex gap-1">

                                                {{-- DETAIL --}}
                                                <a href="{{ route('alat.show', $item->id) }}"
                                                    class="btn btn-sm btn-outline-info" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if (auth()->user()->role === 'peminjam')
                                                    {{-- TOMBOL PINJAM --}}
                                                    <a href="{{ route('peminjaman.create', ['alat_id' => $item->id]) }}"
                                                        class="btn btn-sm btn-success" title="Pinjam Alat">
                                                        <i class="fas fa-hand-holding"></i>
                                                        Pinjam
                                                    </a>
                                                @else
                                                    {{-- EDIT --}}
                                                    <a href="{{ route('alat.edit', $item->id) }}"
                                                        class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    {{-- HAPUS --}}
                                                    <form action="{{ route('alat.destroy', $item->id) }}" method="POST"
                                                        class="d-inline">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            title="Hapus" onclick="return confirm('Hapus alat ini?')">

                                                            <i class="fas fa-trash"></i>

                                                        </button>
                                                    </form>
                                                @endif

                                            </div>
                                        </td>
                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="8" class="text-center py-4">

                                            <div class="text-muted">

                                                <i class="fas fa-tools fa-3x mb-3"></i>

                                                <p>Tidak ada alat ditemukan</p>

                                                @if (auth()->user()->role !== 'peminjam')
                                                    <a href="{{ route('alat.create') }}" class="btn btn-primary">

                                                        <i class="fas fa-plus"></i>
                                                        Tambah Alat Pertama

                                                    </a>
                                                @endif

                                            </div>

                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- PAGINATION --}}
                        @if ($alat->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-3">

                                <small class="text-muted">
                                    Menampilkan
                                    {{ $alat->firstItem() }}
                                    -
                                    {{ $alat->lastItem() }}
                                    dari
                                    {{ $alat->total() }}
                                    alat
                                </small>

                                <nav>
                                    {{ $alat->links() }}
                                </nav>

                            </div>
                        @else
                            <div class="mt-3">
                                <small class="text-muted">
                                    Menampilkan semua {{ $alat->count() }} alat
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
        }

        .badge {
            font-size: 0.8em;
            padding: 0.35em 0.65em;
            font-weight: 500;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        code {
            background-color: #f8f9fa;
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
            font-size: 0.875em;
            color: #d63384;
        }

        .d-flex.gap-1 .btn {
            border-radius: 0.375rem !important;
        }
    </style>
@endpush

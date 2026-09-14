@extends('layouts.master')

@section('title', 'Pengembalian Alat')

@section('content')
    <div class="row justify-content-lg-center">
        <div class="col-lg-10">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Pengembalian Alat</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Pengembalian</li>
                        </ul>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- ============================================================ --}}
            {{-- TABEL PEMINJAMAN AKTIF — hanya tampil untuk admin & petugas  --}}
            {{-- ============================================================ --}}
            @if (!$isPeminjam)
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Daftar Peminjaman Aktif</h5>
                        <form action="{{ route('pengembalian.index') }}" method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control form-control-sm me-2"
                                placeholder="Cari peminjam..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Kode Pinjam</th>
                                        <th>Peminjam</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Rencana Kembali</th>
                                        <th>Jumlah Alat</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($peminjamanAktif as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><strong>#{{ $item->id }}</strong></td>
                                            <td>{{ $item->user->name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y H:i') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="text-center">{{ $item->detailPeminjaman->count() }}</td>
                                            <td>
                                                @php $isLate = now()->gt(\Carbon\Carbon::parse($item->tanggal_kembali_rencana)); @endphp
                                                @if ($item->status == 'menunggu_pengembalian')
                                                    <span class="badge bg-warning text-dark">
                                                        Menunggu Pengembalian
                                                    </span>
                                                @elseif($item->status == 'dipinjam')
                                                    @php
                                                        $isLate = now()->gt(
                                                            \Carbon\Carbon::parse($item->tanggal_kembali_rencana),
                                                        );
                                                    @endphp

                                                    @if ($isLate)
                                                        <span class="badge bg-danger">Terlambat</span>
                                                    @else
                                                        <span class="badge bg-success">Dipinjam</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item->status == 'menunggu_pengembalian')
                                                    <a href="{{ route('pengembalian.create', $item->id) }}"
                                                        class="btn btn-sm btn-success">
                                                        <i class="fas fa-undo"></i> Proses Pengembalian
                                                    </a>
                                                @else
                                                    <span class="text-muted">Belum diajukan</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <i class="fas fa-box-open fa-3x text-muted mb-3 d-block"></i>
                                                <p class="text-muted mb-0">Tidak ada peminjaman aktif</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($peminjamanAktif->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $peminjamanAktif->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- ============================================================ --}}
            {{-- RIWAYAT PENGEMBALIAN                                         --}}
            {{-- Admin/Petugas : semua riwayat                                --}}
            {{-- Peminjam      : hanya riwayat milik sendiri                  --}}
            {{-- ============================================================ --}}
            {{-- ============================================================ --}}
            {{-- RIWAYAT PENGEMBALIAN                                         --}}
            {{-- ============================================================ --}}
            <div class="card shadow-sm border-0 {{ !$isPeminjam ? 'mt-4' : '' }}" style="border-radius: 15px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        @if ($isPeminjam)
                            <i class="fas fa-history text-primary me-2"></i>Riwayat Pengembalian Saya
                        @else
                            <i class="fas fa-history text-primary me-2"></i>Riwayat Pengembalian
                        @endif
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <style>
                                .bg-soft-primary {
                                    background-color: #e7f1ff;
                                    color: #0d6efd;
                                }

                                .table> :not(caption)>*>* {
                                    padding: 1rem 0.75rem;
                                }
                            </style>
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th class="ps-4 border-0">#</th>

                                    {{-- Kode Pinjam & Peminjam Hanya Tampil untuk Admin/Petugas --}}
                                    @if (!$isPeminjam)
                                        <th class="border-0">Kode Pinjam</th>
                                        <th class="border-0">Peminjam</th>
                                    @endif

                                    <th class="border-0">Barang Pinjaman</th>
                                    <th class="border-0">Tgl Pinjam</th>
                                    <th class="border-0">Tgl Kembali</th>
                                    <th class="border-0 text-center">Tagihan Denda</th>
                                    <th class="border-0 text-center">Status</th>
                                    <th class="border-0 text-center pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayatPengembalian as $index => $item)
                                    <tr class="border-bottom border-light">
                                        <td class="ps-4 text-muted">{{ $index + 1 }}</td>

                                        {{-- Kode Pinjam & Peminjam Hanya Tampil untuk Admin/Petugas --}}
                                        @if (!$isPeminjam)
                                            <td>
                                                <span
                                                    class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill border border-primary border-opacity-25 shadow-sm">
                                                    #{{ $item->peminjaman_id }}
                                                </span>
                                            </td>
                                            <td class="fw-bold text-dark">{{ $item->peminjaman->user->name ?? '-' }}</td>
                                        @endif

                                        {{-- NAMA BARANG --}}
                                        <td>
                                            <ul class="list-unstyled mb-0 small">
                                                @if ($item->peminjaman && $item->peminjaman->detailPeminjaman)
                                                    @foreach ($item->peminjaman->detailPeminjaman as $detail)
                                                        <li class="mb-1 text-wrap" style="min-width: 150px;">
                                                            <i class="fas fa-wrench text-secondary me-1"
                                                                style="font-size: 0.8em;"></i>
                                                            <span
                                                                class="fw-bold text-dark">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                                            <span class="text-muted">({{ $detail->jumlah_pinjam }}
                                                                pcs)</span>
                                                        </li>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </ul>
                                        </td>

                                        {{-- TANGGAL PINJAM --}}
                                        <td>
                                            @if ($item->peminjaman)
                                                <div class="text-dark fw-semibold">
                                                    {{ \Carbon\Carbon::parse($item->peminjaman->tanggal_pinjam)->format('d M Y') }}
                                                </div>
                                                <div class="text-muted small"><i
                                                        class="fas fa-clock me-1"></i>{{ \Carbon\Carbon::parse($item->peminjaman->tanggal_pinjam)->format('H:i') }}
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        {{-- TANGGAL KEMBALI --}}
                                        <td>
                                            <div class="text-dark fw-semibold">
                                                {{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('d M Y') }}
                                            </div>
                                            <div class="text-muted small"><i
                                                    class="fas fa-clock me-1"></i>{{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('H:i') }}
                                            </div>
                                        </td>

                                        {{-- DENDA --}}
                                        <td class="text-center">
                                            @if ($item->denda > 0)
                                                <div class="text-danger fw-bold small">Rp
                                                    {{ number_format($item->denda, 0, ',', '.') }}</div>
                                                <div class="text-muted" style="font-size: 0.7rem;">Dibayar: Rp
                                                    {{ number_format($item->denda_dibayar, 0, ',', '.') }}</div>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>

                                        {{-- STATUS DENDA --}}
                                        <td class="text-center">
                                            @if ($item->status_denda === 'lunas')
                                                <span class="badge bg-success px-3 py-2 rounded-pill shadow-sm"><i
                                                        class="fas fa-check-circle me-1"></i>Lunas</span>
                                            @elseif($item->status_denda === 'sebagian')
                                                <span
                                                    class="badge bg-warning text-dark px-3 py-2 rounded-pill shadow-sm">Sebagian</span>
                                            @elseif($item->status_denda === 'belum')
                                                <span class="badge bg-danger px-3 py-2 rounded-pill shadow-sm">Belum
                                                    Lunas</span>
                                            @else
                                                <span
                                                    class="badge bg-light text-secondary border px-3 py-2 rounded-pill shadow-sm">Tidak
                                                    Ada</span>
                                            @endif
                                        </td>

                                        {{-- AKSI --}}
                                        <td class="text-center pe-4">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('pengembalian.show', $item->id) }}"
                                                    class="btn btn-sm btn-info text-white shadow-sm"
                                                    title="Detail Riwayat">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>

                                                <form action="{{ route('pengembalian.destroy', $item->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Yakin ingin menghapus riwayat pengembalian ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger shadow-sm"
                                                        title="Hapus Riwayat">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        {{-- Jika Peminjam (7 Kolom), Jika Admin (9 Kolom) --}}
                                        <td colspan="{{ $isPeminjam ? 7 : 9 }}" class="text-center py-5">
                                            <i class="fas fa-box-open fa-3x text-muted mb-3 d-block opacity-50"></i>
                                            <h6 class="text-dark fw-bold">Tidak ada riwayat pengembalian</h6>
                                            <p class="text-muted small mb-0">
                                                @if ($isPeminjam)
                                                    Anda belum melakukan pengembalian barang apapun.
                                                @else
                                                    Belum ada data pengembalian yang tercatat di sistem.
                                                @endif
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($riwayatPengembalian->hasPages())
                        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                            <div class="text-muted small">
                                Menampilkan {{ $riwayatPengembalian->firstItem() ?? 0 }} -
                                {{ $riwayatPengembalian->lastItem() ?? 0 }} dari {{ $riwayatPengembalian->total() }} data
                            </div>
                            <div>
                                {{ $riwayatPengembalian->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection

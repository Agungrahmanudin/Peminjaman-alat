@extends('layouts.master')

@section('title', 'Detail Customer')

@section('content')
<div class="row justify-content-lg-center">
    <div class="col-lg-10">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Detail Customer</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
                        <li class="breadcrumb-item active">{{ $customer->name }}</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-primary me-2">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus customer ini?')">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center">
                        @if($customer->avatar)
                            <img src="{{ asset('storage/avatars/' . $customer->avatar) }}" 
                                 class="img-fluid rounded-circle mb-3" 
                                 style="width: 150px; height: 150px; object-fit: cover;" 
                                 alt="{{ $customer->name }}">
                        @else
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto mb-3" 
                                 style="width: 150px; height: 150px;">
                                <span class="text-white fw-bold" style="font-size: 3rem;">
                                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <h4 class="mb-1">{{ $customer->name }}</h4>
                        <p class="text-muted">{{ $customer->company ?: 'Tidak ada perusahaan' }}</p>
                        <span class="badge {{ $customer->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $customer->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th width="40%">Email</th>
                                        <td>{{ $customer->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Telepon</th>
                                        <td>{{ $customer->phone ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Perusahaan</th>
                                        <td>{{ $customer->company ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Terdaftar</th>
                                        <td>{{ $customer->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Alamat</label>
                                    <p>{{ $customer->address ?: 'Tidak ada alamat' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6 class="card-title">Total Peminjaman</h6>
                        <h3>{{ $totalPeminjaman }}</h3>
                        <small>Seluruh waktu</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6 class="card-title">Peminjaman Aktif</h6>
                        <h3>{{ $peminjamanAktif }}</h3>
                        <small>Sedang dipinjam</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6 class="card-title">Total Alat</h6>
                        <h3>{{ $totalAlatDipinjam }}</h3>
                        <small>Unit yang dipinjam</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Peminjaman -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Riwayat Peminjaman Terbaru</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tanggal Kembali</th>
                                <th>Jumlah Alat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customer->peminjaman as $pinjam)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $pinjam->tanggal_pinjam->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($pinjam->tanggal_kembali)
                                            {{ $pinjam->tanggal_kembali->format('d/m/Y H:i') }}
                                        @else
                                            <span class="text-warning">Belum dikembalikan</span>
                                        @endif
                                    </td>
                                    <td>{{ $pinjam->detailPeminjaman->count() }} alat</td>
                                    <td>
                                        @if($pinjam->status == 'dipinjam')
                                            @if(now()->gt($pinjam->tanggal_kembali_rencana))
                                                <span class="badge bg-danger">Terlambat</span>
                                            @else
                                                <span class="badge bg-warning">Dipinjam</span>
                                            @endif
                                        @else
                                            <span class="badge bg-success">Dikembalikan</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('peminjaman.show', $pinjam->id) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada riwayat peminjaman</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
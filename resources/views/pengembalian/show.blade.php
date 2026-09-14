@extends('layouts.master')

@section('title', 'Detail Pengembalian')

@section('content')
<div class="row justify-content-lg-center">
    <div class="col-lg-10">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Detail Pengembalian #{{ $pengembalian->id }}</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pengembalian.index') }}">Pengembalian</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ul>
                </div>
              
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3">Informasi Peminjaman</h5>
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">ID Peminjaman</th>
                                <td><strong>#{{ $pengembalian->peminjaman_id }}</strong></td>
                            </tr>
                            <tr>
                                <th>Peminjam</th>
                                <td>{{ $pengembalian->peminjaman->user->name }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Pinjam</th>
                                <td>{{ \Carbon\Carbon::parse($pengembalian->peminjaman->tanggal_pinjam)->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Rencana Kembali</th>
                                <td>{{ \Carbon\Carbon::parse($pengembalian->peminjaman->tanggal_kembali_rencana)->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Keperluan</th>
                                <td>{{ $pengembalian->peminjaman->keperluan }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-3">Informasi Pengembalian</h5>
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Tanggal Kembali Aktual</th>
                                <td>{{ \Carbon\Carbon::parse($pengembalian->tanggal_kembali_realisasi)->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Status Pengembalian</th>
                                <td>
                                    @php
                                        $tanggalRencana = \Carbon\Carbon::parse($pengembalian->peminjaman->tanggal_kembali_rencana);
                                        $tanggalRealisasi = \Carbon\Carbon::parse($pengembalian->tanggal_kembali_realisasi);
                                        $terlambat = $tanggalRealisasi->gt($tanggalRencana);
                                        $hariTerlambat = $terlambat ? $tanggalRencana->diffInDays($tanggalRealisasi) : 0;
                                    @endphp
                                    @if($terlambat)
                                        <span class="badge bg-danger">Terlambat {{ $hariTerlambat }} hari</span>
                                    @else
                                        <span class="badge bg-success">Tepat waktu</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Denda</th>
                                <td>
                                    @if($pengembalian->denda > 0)
                                        <span class="text-danger fw-bold">Rp {{ number_format($pengembalian->denda, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-success">Rp 0</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Jumlah Alat</th>
                                <td><span class="badge bg-info">{{ $pengembalian->peminjaman->detailPeminjaman->count() }} alat</span></td>
                            </tr>
                            <tr>
                                <th>Catatan</th>
                                <td>{{ $pengembalian->catatan ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Detail Alat yang Dikembalikan</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Kode Alat</th>
                                <th>Nama Alat</th>
                                <th width="100">Jumlah</th>
                                <th width="150">Kondisi Saat Pinjam</th>
                                <th width="150">Kondisi Saat Kembali</th>
                                <th width="150">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Decode format JSON dari database menjadi Array
                                $arrayKondisi = json_decode($pengembalian->kondisi_kembali, true) ?? [];
                            @endphp
                            
                            @foreach($pengembalian->peminjaman->detailPeminjaman as $detail)
                                @php
                                    // Ambil kondisi berdasarkan ID detail peminjaman
                                    $kondisiKembaliItem = $arrayKondisi[$detail->id] ?? null;
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><code>{{ $detail->alat->kode_alat }}</code></td>
                                    <td>{{ $detail->alat->nama_alat }}</td>
                                    <td>{{ $detail->jumlah_pinjam }}</td>
                                    <td>
                                        <span class="badge {{ $detail->kondisi_pinjam == 'baik' ? 'bg-success' : ($detail->kondisi_pinjam == 'rusak' ? 'bg-danger' : 'bg-warning') }}">
                                            {{ ucfirst($detail->kondisi_pinjam) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($kondisiKembaliItem)
                                            <span class="badge {{ $kondisiKembaliItem == 'baik' ? 'bg-success' : ($kondisiKembaliItem == 'hilang' ? 'bg-dark' : 'bg-danger') }}">
                                                {{ ucfirst($kondisiKembaliItem) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Tidak tercatat</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($kondisiKembaliItem && $detail->kondisi_pinjam == $kondisiKembaliItem)
                                            <span class="badge bg-success">Kondisi sama</span>
                                        @elseif($kondisiKembaliItem)
                                            <span class="badge bg-warning text-dark">Kondisi berubah</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge {
        font-size: 0.8em;
        padding: 0.35em 0.65em;
        font-weight: 500;
    }
    
    .table th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #495057;
    }
    
    code {
        background-color: #f8f9fa;
        padding: 2px 6px;
        border-radius: 3px;
        font-family: 'Courier New', monospace;
    }
</style>
@endpush
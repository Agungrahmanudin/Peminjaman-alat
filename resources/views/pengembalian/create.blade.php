@extends('layouts.master')

@section('title', 'Proses Pengembalian Alat')

@section('content')
<div class="row justify-content-lg-center">
    <div class="col-lg-10">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Proses Pengembalian Alat</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pengembalian.index') }}">Pengembalian</a></li>
                        <li class="breadcrumb-item active">Proses</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pengembalian.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        {{-- Error dari Session (Logika Controller) --}}
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Error dari Validasi Form (Request Validator) --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong><i class="fas fa-exclamation-triangle me-2"></i>Gagal memproses data!</strong>
                <p class="mb-1">Silakan perbaiki kesalahan berikut:</p>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Informasi Peminjaman</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="150"><strong>Peminjam</strong></td>
                                <td>: {{ $peminjaman->user->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Pinjam</strong></td>
                                <td>: {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Rencana Kembali</strong></td>
                                <td>: {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="150"><strong>Keperluan</strong></td>
                                <td>: {{ $peminjaman->keperluan }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>: <span class="badge bg-info">Dipinjam</span></td>
                            </tr>
                        </table>
                    </div>
                </div>

                @php
                    $today = \Carbon\Carbon::now();
                    $rencana = \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana);
                    $hariTerlambat = $today->gt($rencana) ? $rencana->diffInDays($today) : 0;
                @endphp

                <form action="{{ route('pengembalian.store') }}" method="POST" id="formPengembalian">
                    @csrf
                    <input type="hidden" name="peminjaman_id" value="{{ $peminjaman->id }}">

                    <h5 class="card-title mb-3">Cek Kondisi Alat</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Alat</th>
                                    <th>Jumlah</th>
                                    <th>Kondisi Pinjam</th>
                                    <th>Kondisi Kembali</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($peminjaman->detailPeminjaman as $detail)
                                <tr>
                                    <td>
                                        <strong>{{ $detail->alat->nama_alat }}</strong><br>
                                        <small class="text-muted">Kode: {{ $detail->alat->kode_alat }}</small><br>
                                        <small class="text-primary">Harga: Rp {{ number_format($detail->alat->harga_alat ?? 0, 0, ',', '.') }}</small>
                                    </td>
                                    <td class="text-center align-middle">{{ $detail->jumlah_pinjam }}</td>
                                    <td class="align-middle">
                                        <span class="badge bg-info">{{ $detail->kondisi_pinjam }}</span>
                                    </td>
                                    <td>
                                        <select class="form-select kondisi-kembali mb-2 @error('kondisi_kembali.'.$detail->id) is-invalid @enderror" 
                                                name="kondisi_kembali[{{ $detail->id }}]" 
                                                data-id="{{ $detail->id }}"
                                                required
                                                onchange="togglePersen(this); hitungDenda()">
                                            <option value="">-- Pilih Kondisi --</option>
                                            <option value="baik" {{ old('kondisi_kembali.'.$detail->id) == 'baik' ? 'selected' : '' }}>✅ Baik</option>
                                            <option value="rusak" {{ old('kondisi_kembali.'.$detail->id) == 'rusak' ? 'selected' : '' }}>⚠️ Rusak (Isi Persentase)</option>
                                            <option value="hilang" {{ old('kondisi_kembali.'.$detail->id) == 'hilang' ? 'selected' : '' }}>💔 Hilang (Ganti 100%)</option>
                                        </select>

                                        <div class="input-group input-group-sm {{ old('kondisi_kembali.'.$detail->id) && old('kondisi_kembali.'.$detail->id) != 'baik' ? '' : 'd-none' }}" id="div_persen_{{ $detail->id }}">
                                            <span class="input-group-text">Denda</span>
                                            <input type="number" class="form-control persen-denda @error('persen_denda.'.$detail->id) is-invalid @enderror" 
                                                   id="persen_{{ $detail->id }}" 
                                                   name="persen_denda[{{ $detail->id }}]" 
                                                   min="0" max="100" 
                                                   value="{{ old('persen_denda.'.$detail->id, 0) }}" 
                                                   data-harga="{{ $detail->alat->harga_alat ?? 0 }}"
                                                   data-jumlah="{{ $detail->jumlah_pinjam }}"
                                                   oninput="hitungDenda()"
                                                   placeholder="0-100">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <input type="text" class="form-control form-control-sm" 
                                               name="catatan_alat[{{ $detail->id }}]" 
                                               value="{{ old('catatan.'.$detail->id) }}"
                                               placeholder="Catatan kerusakan">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6><i class="fas fa-clock text-warning"></i> Denda Keterlambatan</h6>
                                    <div class="mb-2">
                                        <label>Hari Terlambat</label>
                                        <input type="text" class="form-control" value="{{ $hariTerlambat }} hari" readonly>
                                    </div>
                                    <div class="mb-2">
                                        <label>Denda (Rp 10.000/hari)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" id="denda_keterlambatan" 
                                                   value="{{ $hariTerlambat * 10000 }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6><i class="fas fa-calculator"></i> Total Denda</h6>
                                    <div class="mb-2">
                                        <label>Denda Kerusakan / Kehilangan</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control text-danger fw-bold" id="denda_kerusakan" 
                                                   value="0" readonly>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label>Denda Keterlambatan</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" id="denda_keterlambatan_display" 
                                                   value="{{ $hariTerlambat * 10000 }}" readonly>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="mb-2">
                                        <label><strong>TOTAL DENDA</strong></label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control form-control-lg fw-bold" 
                                                   id="total_denda" name="denda" value="{{ $hariTerlambat * 10000 }}" readonly>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label>Denda Dibayar</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control @error('denda_dibayar') is-invalid @enderror" name="denda_dibayar" 
                                                   id="denda_dibayar" value="{{ old('denda_dibayar', 0) }}" min="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Metode Pembayaran</label>
                                <select class="form-select @error('metode_pembayaran') is-invalid @enderror" name="metode_pembayaran" id="metode_pembayaran">
                                    <option value="tunai" {{ old('metode_pembayaran') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                                    <option value="transfer" {{ old('metode_pembayaran') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                    <option value="qris" {{ old('metode_pembayaran') == 'qris' ? 'selected' : '' }}>QRIS</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Pengembalian</label>
                                <input type="datetime-local" class="form-control @error('tanggal_kembali_realisasi') is-invalid @enderror" name="tanggal_kembali_realisasi" 
                                       value="{{ old('tanggal_kembali_realisasi', \Carbon\Carbon::now()->setTimezone('Asia/Jakarta')->format('Y-m-d\TH:i')) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Pengembalian</label>
                        <textarea class="form-control" name="catatan" rows="2" 
                                  placeholder="Catatan tambahan tentang pengembalian...">{{ old('catatan_pengembalian') }}</textarea>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('pengembalian.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-success" id="btnSubmit">
                            <i class="fas fa-check"></i> Proses Pengembalian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Panggil hitungDenda saat halaman diload (berguna jika ada old value)
window.addEventListener('DOMContentLoaded', (event) => {
    hitungDenda();
});

// Menampilkan atau menyembunyikan input persentase
function togglePersen(select) {
    const id = select.dataset.id;
    const divPersen = document.getElementById('div_persen_' + id);
    const inputPersen = document.getElementById('persen_' + id);
    
    if (select.value === 'rusak') {
        // Tampilkan input, user bisa isi angka bebas
        divPersen.classList.remove('d-none');
        inputPersen.readOnly = false;
        if(inputPersen.value == 0 || inputPersen.value == 100) {
            inputPersen.value = ""; // Kosongkan agar user bisa mengetik
        }
    } else if (select.value === 'hilang') {
        // Tampilkan input, kunci di angka 100%
        divPersen.classList.remove('d-none');
        inputPersen.readOnly = true;
        inputPersen.value = 100;
    } else {
        // Sembunyikan jika kondisinya "Baik" atau Kosong
        divPersen.classList.add('d-none');
        inputPersen.value = 0;
    }
}

// Menghitung total denda
function hitungDenda() {
    const persenInputs = document.querySelectorAll('.persen-denda');
    let dendaKerusakan = 0;
    
    persenInputs.forEach(input => {
        const persen = parseFloat(input.value) || 0;
        const harga = parseFloat(input.dataset.harga) || 0;
        const jumlah = parseInt(input.dataset.jumlah) || 1;
        
        // Rumus: (Persentase / 100) x Harga Alat x Jumlah Pinjam
        dendaKerusakan += (persen / 100) * harga * jumlah;
    });
    
    // Bulatkan hasil agar tidak ada koma (desimal)
    dendaKerusakan = Math.round(dendaKerusakan);
    
    const dendaKeterlambatan = parseInt(document.getElementById('denda_keterlambatan').value) || 0;
    const totalDenda = dendaKerusakan + dendaKeterlambatan;
    
    document.getElementById('denda_kerusakan').value = dendaKerusakan;
    document.getElementById('total_denda').value = totalDenda;
}

// Validasi Denda Dibayar tidak boleh lebih besar dari Total Denda
document.getElementById('denda_dibayar').addEventListener('input', function() {
    const totalDenda = parseInt(document.getElementById('total_denda').value) || 0;
    const dibayar = parseInt(this.value) || 0;
    
    if (dibayar > totalDenda) {
        this.value = totalDenda;
        alert('Denda yang dibayar tidak boleh melebihi total denda');
    }
});

// Validasi Form Submit
document.getElementById('formPengembalian').addEventListener('submit', function(e) {
    const kondisiSelects = document.querySelectorAll('.kondisi-kembali');
    const persenInputs = document.querySelectorAll('.persen-denda');
    let valid = true;
    
    // Cek dropdown kondisi
    kondisiSelects.forEach(select => {
        if (!select.value) {
            alert('Harap pilih kondisi untuk semua alat');
            valid = false;
            e.preventDefault();
            return false;
        }
    });

    // Cek input persentase jika rusak
    persenInputs.forEach(input => {
        const id = input.id.split('_')[1];
        const select = document.querySelector(`select[data-id="${id}"]`);
        
        if (select.value === 'rusak' && (input.value === "" || input.value <= 0 || input.value > 100)) {
            alert('Harap masukkan persentase kerusakan antara 1 - 100%');
            valid = false;
            e.preventDefault();
            return false;
        }
    });
    
    if (valid) {
        const btn = document.getElementById('btnSubmit');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    }
});
</script>
@endsection
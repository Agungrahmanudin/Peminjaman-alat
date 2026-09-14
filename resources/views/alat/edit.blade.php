@extends('layouts.master')

@section('title', 'Edit Alat')

@section('content')
<div class="row justify-content-lg-center">
    <div class="col-lg-10">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Edit Alat</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('alat.index') }}">Alat</a></li>
                        <li class="breadcrumb-item active">Edit Alat</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="{{ route('alat.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('alat.update', $alat->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-4">
                                <h5 class="card-title mb-3">Informasi Dasar Alat</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Kode Alat <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   name="kode_alat" 
                                                   class="form-control @error('kode_alat') is-invalid @enderror" 
                                                   value="{{ old('kode_alat', $alat->kode_alat) }}" 
                                                   required>
                                            @error('kode_alat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Alat <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   name="nama_alat" 
                                                   class="form-control @error('nama_alat') is-invalid @enderror" 
                                                   value="{{ old('nama_alat', $alat->nama_alat) }}" 
                                                   required>
                                            @error('nama_alat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                            <select name="kategori_id" class="form-select @error('kategori_id') is-invalid @enderror" required>
                                                <option value="">Pilih Kategori</option>
                                                @foreach($kategori as $kat)
                                                    <option value="{{ $kat->id }}" 
                                                        {{ old('kategori_id', $alat->kategori_id) == $kat->id ? 'selected' : '' }}>
                                                        {{ $kat->nama_kategori }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('kategori_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Kondisi <span class="text-danger">*</span></label>
                                            <select name="kondisi" class="form-select @error('kondisi') is-invalid @enderror" required>
                                                <option value="baik" {{ old('kondisi', $alat->kondisi) == 'baik' ? 'selected' : '' }}>Baik</option>
                                                <option value="rusak" {{ old('kondisi', $alat->kondisi) == 'rusak' ? 'selected' : '' }}>Rusak</option>
                                                <option value="perbaikan" {{ old('kondisi', $alat->kondisi) == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                                            </select>
                                            @error('kondisi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Harga Alat (Rp) <span class="text-danger">*</span></label>
                                            <input type="number" 
                                                   name="harga_alat" 
                                                   class="form-control @error('harga_alat') is-invalid @enderror" 
                                                   value="{{ old('harga_alat', $alat->harga_alat) }}" 
                                                   min="0" 
                                                   required>
                                            @error('harga_alat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Digunakan untuk menghitung denda kerusakan/hilang.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Keterangan</label>
                                    <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                                              rows="3">{{ old('keterangan', $alat->keterangan) }}</textarea>
                                    @error('keterangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <h5 class="card-title mb-3">Informasi Stok</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Jumlah Total <span class="text-danger">*</span></label>
                                            <input type="number" 
                                                   name="jumlah_total" 
                                                   id="jumlah_total"
                                                   class="form-control @error('jumlah_total') is-invalid @enderror" 
                                                   value="{{ old('jumlah_total', $alat->jumlah_total) }}" 
                                                   min="1" 
                                                   required>
                                            @error('jumlah_total')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Jumlah Tersedia <span class="text-danger">*</span></label>
                                            <input type="number" 
                                                   name="jumlah_tersedia" 
                                                   id="jumlah_tersedia"
                                                   class="form-control @error('jumlah_tersedia') is-invalid @enderror" 
                                                   value="{{ old('jumlah_tersedia', $alat->jumlah_tersedia) }}" 
                                                   min="0" 
                                                   required>
                                            @error('jumlah_tersedia')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Maksimal: <span id="maxTersedia">{{ $alat->jumlah_total }}</span></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h5 class="card-title mb-3">Informasi Lokasi</h5>
                                <div class="mb-3">
                                    <label class="form-label">Lokasi Penyimpanan <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="lokasi" 
                                           class="form-control @error('lokasi') is-invalid @enderror" 
                                           value="{{ old('lokasi', $alat->lokasi) }}" 
                                           required>
                                    @error('lokasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light mb-4">
                                <div class="card-body">
                                    <h6 class="card-title">Ringkasan Alat</h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td>Kode:</td>
                                            <td><strong>{{ $alat->kode_alat }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Nama:</td>
                                            <td><strong>{{ $alat->nama_alat }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Kategori:</td>
                                            <td><strong>{{ $alat->kategori->nama_kategori ?? '-' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Harga:</td>
                                            <td><strong>Rp {{ number_format($alat->harga_alat ?? 0, 0, ',', '.') }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Kondisi:</td>
                                            <td>
                                                @php
                                                    $badgeClass = match($alat->kondisi) {
                                                        'baik' => 'success',
                                                        'rusak' => 'danger',
                                                        'perbaikan' => 'warning',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $badgeClass }}">{{ ucfirst($alat->kondisi) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Total Stok:</td>
                                            <td><strong>{{ $alat->jumlah_total }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Tersedia:</td>
                                            <td><strong class="text-success">{{ $alat->jumlah_tersedia }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Lokasi:</td>
                                            <td><small>{{ $alat->lokasi }}</small></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <small>
                                    Pastikan data yang diisi sudah benar.<br>
                                    Jumlah tersedia tidak boleh melebihi jumlah total.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('alat.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jumlahTotal = document.getElementById('jumlah_total');
        const jumlahTersedia = document.getElementById('jumlah_tersedia');
        const maxTersedia = document.getElementById('maxTersedia');
        
        function updateMaxTersedia() {
            const total = parseInt(jumlahTotal.value) || 0;
            jumlahTersedia.max = total;
            if (maxTersedia) maxTersedia.textContent = total;
            
            if (parseInt(jumlahTersedia.value) > total) {
                jumlahTersedia.value = total;
                alert('Jumlah tersedia otomatis disesuaikan karena tidak boleh melebihi jumlah total.');
            }
        }
        
        jumlahTotal.addEventListener('input', updateMaxTersedia);
        jumlahTotal.addEventListener('change', updateMaxTersedia);
        
        // Validasi form
        document.querySelector('form').addEventListener('submit', function(e) {
            const total = parseInt(jumlahTotal.value);
            const tersedia = parseInt(jumlahTersedia.value);
            
            if (tersedia > total) {
                e.preventDefault();
                alert('Jumlah tersedia tidak boleh lebih dari jumlah total!');
                return false;
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    .card-title {
        font-weight: 600;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }
    
    .table-sm td {
        padding: 0.3rem;
    }
</style>
@endpush
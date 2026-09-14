@extends('layouts.master')

@section('title', 'Tambah Alat Baru')

@section('content')
<div class="row justify-content-lg-center">
    <div class="col-lg-10">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Tambah Alat Baru</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('alat.index') }}">Alat</a></li>
                        <li class="breadcrumb-item active">Tambah Alat</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="{{ route('alat.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('alat.store') }}" id="alatForm">
                    @csrf
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-8">
                            <!-- Informasi Dasar -->
                            <div class="mb-4">
                                <h5 class="card-title mb-3">Informasi Dasar Alat</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="kode_alat" class="form-label">Kode Alat <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control @error('kode_alat') is-invalid @enderror" 
                                                       id="kode_alat" name="kode_alat" 
                                                       value="{{ old('kode_alat') }}" 
                                                       placeholder="ALT-XXXX" required readonly>
                                                <button class="btn btn-outline-primary" type="button" onclick="generateKodeAlat()">
                                                    <i class="fas fa-sync-alt"></i> Generate
                                                </button>
                                            </div>
                                            @error('kode_alat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Kode unik untuk identifikasi alat (otomatis)</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nama_alat" class="form-label">Nama Alat <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('nama_alat') is-invalid @enderror" 
                                                   id="nama_alat" name="nama_alat" 
                                                   value="{{ old('nama_alat') }}" 
                                                   placeholder="Contoh: Multimeter Digital" required>
                                            @error('nama_alat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="kategori_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <select class="form-select @error('kategori_id') is-invalid @enderror" 
                                                        id="kategori_id" name="kategori_id" required>
                                                    <option value="">Pilih Kategori</option>
                                                    @forelse($kategori as $kat)
                                                        <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
                                                            {{ $kat->nama_kategori }}
                                                        </option>
                                                    @empty
                                                        <option value="" disabled>Tidak ada kategori</option>
                                                    @endforelse
                                                </select>
                                                <button type="button" class="btn btn-outline-primary" 
                                                        data-bs-toggle="modal" data-bs-target="#tambahKategoriModal">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                            @error('kategori_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="kondisi" class="form-label">Kondisi <span class="text-danger">*</span></label>
                                            <select class="form-select @error('kondisi') is-invalid @enderror" 
                                                    id="kondisi" name="kondisi" required>
                                                <option value="baik" {{ old('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                                                <option value="rusak" {{ old('kondisi') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                                                <option value="perbaikan" {{ old('kondisi') == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                                            </select>
                                            @error('kondisi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Input Harga Alat Ditambahkan Di Sini -->
                                <div class="mb-3">
                                    <label for="harga_alat" class="form-label">Harga Satuan Alat <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('harga_alat') is-invalid @enderror" 
                                               id="harga_alat" name="harga_alat" 
                                               value="{{ old('harga_alat') }}" 
                                               min="0" placeholder="Contoh: 150000" required>
                                    </div>
                                    <small class="text-muted">Digunakan sebagai dasar persentase perhitungan denda kerusakan barang.</small>
                                    @error('harga_alat')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="keterangan" class="form-label">Keterangan</label>
                                    <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                              id="keterangan" name="keterangan" 
                                              rows="3" placeholder="Deskripsi singkat tentang alat">{{ old('keterangan') }}</textarea>
                                    @error('keterangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Informasi Kuantitas -->
                            <div class="mb-4">
                                <h5 class="card-title mb-3">Informasi Kuantitas</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="jumlah_total" class="form-label">Jumlah Total <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('jumlah_total') is-invalid @enderror" 
                                                   id="jumlah_total" name="jumlah_total" 
                                                   value="{{ old('jumlah_total', 1) }}" 
                                                   min="1" required>
                                            @error('jumlah_total')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="jumlah_tersedia" class="form-label">Jumlah Tersedia <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('jumlah_tersedia') is-invalid @enderror" 
                                                   id="jumlah_tersedia" name="jumlah_tersedia" 
                                                   value="{{ old('jumlah_tersedia', 1) }}" 
                                                   min="0" required>
                                            @error('jumlah_tersedia')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-4">
                            <!-- Informasi Lokasi -->
                            <div class="mb-4">
                                <h5 class="card-title mb-3">Informasi Lokasi</h5>
                                <div class="mb-3">
                                    <label for="lokasi" class="form-label">Lokasi Penyimpanan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('lokasi') is-invalid @enderror" 
                                           id="lokasi" name="lokasi" 
                                           value="{{ old('lokasi') }}" 
                                           placeholder="Contoh: Gudang Utama, Rak A1" required>
                                    @error('lokasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Informasi Sistem -->
                            <div class="mb-4">
                                <h5 class="card-title mb-3">Informasi Sistem</h5>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <small>
                                        Kode alat akan digenerate otomatis.<br>
                                        Format: <strong>ALT-TAHUN-BULAN-ANGKA</strong>
                                    </small>
                                </div>
                            </div>

                            <!-- Preview -->
                            <div class="mb-4">
                                <h5 class="card-title mb-3">Preview Alat</h5>
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fas fa-tools fa-4x text-primary"></i>
                                        </div>
                                        <h6 id="previewNamaAlat" class="card-title">-</h6>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                Kode: <span id="previewKodeAlat">-</span><br>
                                                Kategori: <span id="previewKategori">-</span><br>
                                                Kondisi: <span id="previewKondisi" class="badge bg-success">Baik</span><br>
                                                Harga: <strong class="text-dark" id="previewHarga">-</strong>
                                            </small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('alat.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                                <div>
                                    <button type="reset" class="btn btn-outline-secondary me-2">
                                        <i class="fas fa-redo"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan Alat
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="tambahKategoriModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formTambahKategori">
                    @csrf
                    <div class="mb-3">
                        <label for="nama_kategori_baru" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_kategori_baru" name="nama_kategori" required>
                        <small class="text-muted">Contoh: Alat Listrik, Alat Mekanik, dll</small>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi_kategori" class="form-label">Deskripsi (Opsional)</label>
                        <textarea class="form-control" id="deskripsi_kategori" name="deskripsi" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="simpanKategori()">
                    <i class="fas fa-save"></i> Simpan Kategori
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Generate Kode Alat Otomatis saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        generateKodeAlat();
        updatePreview();
        
        // Set jumlah tersedia maksimal sama dengan jumlah total
        const jumlahTotal = document.getElementById('jumlah_total');
        const jumlahTersedia = document.getElementById('jumlah_tersedia');
        
        jumlahTersedia.max = jumlahTotal.value;
        
        jumlahTotal.addEventListener('input', function() {
            jumlahTersedia.max = this.value;
        });
    });

    // Generate Kode Alat Otomatis
    function generateKodeAlat() {
        const date = new Date();
        const tahun = date.getFullYear();
        const bulan = String(date.getMonth() + 1).padStart(2, '0');
        const random = Math.floor(Math.random() * 9000) + 1000;
        
        const kodeAlat = `ALT-${tahun}-${bulan}-${random}`;
        
        document.getElementById('kode_alat').value = kodeAlat;
        updatePreview();
    }

    // Update Preview Real-time
    function updatePreview() {
        // Update nama alat
        const namaAlat = document.getElementById('nama_alat').value || '-';
        document.getElementById('previewNamaAlat').textContent = namaAlat;
        
        // Update kode alat
        const kodeAlat = document.getElementById('kode_alat').value || '-';
        document.getElementById('previewKodeAlat').textContent = kodeAlat;
        
        // Update kategori
        const kategoriSelect = document.getElementById('kategori_id');
        const selectedKategori = kategoriSelect.options[kategoriSelect.selectedIndex];
        let kategoriText = '-';
        
        if (selectedKategori && selectedKategori.value !== '') {
            kategoriText = selectedKategori.textContent;
        }
        document.getElementById('previewKategori').textContent = kategoriText;
        
        // Update kondisi
        const kondisiSelect = document.getElementById('kondisi');
        const kondisi = kondisiSelect.value;
        const kondisiBadge = document.getElementById('previewKondisi');
        
        let badgeClass = 'badge ';
        switch(kondisi) {
            case 'baik':
                badgeClass += 'bg-success';
                kondisiBadge.textContent = 'Baik';
                break;
            case 'rusak':
                badgeClass += 'bg-danger';
                kondisiBadge.textContent = 'Rusak';
                break;
            case 'perbaikan':
                badgeClass += 'bg-warning';
                kondisiBadge.textContent = 'Perbaikan';
                break;
            default:
                badgeClass += 'bg-secondary';
                kondisiBadge.textContent = '-';
        }
        kondisiBadge.className = badgeClass;

        // Update harga
        const hargaAlat = document.getElementById('harga_alat').value;
        const previewHarga = document.getElementById('previewHarga');
        if (hargaAlat && !isNaN(hargaAlat)) {
            // Format angka menjadi mata uang Rupiah
            previewHarga.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(hargaAlat);
        } else {
            previewHarga.textContent = '-';
        }
    }

    // Simpan Kategori Baru via AJAX
    function simpanKategori() {
        const namaKategori = document.getElementById('nama_kategori_baru').value;
        const deskripsi = document.getElementById('deskripsi_kategori').value;
        
        if (!namaKategori.trim()) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Nama kategori tidak boleh kosong',
                confirmButtonColor: '#dc3545'
            });
            return;
        }
        
        fetch('{{ route("kategori.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                nama_kategori: namaKategori,
                deskripsi: deskripsi
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('kategori_id');
                
                // Hapus opsi "Tidak ada kategori" jika ada
                const noCategoryOption = select.querySelector('option[value=""][disabled]');
                if (noCategoryOption) {
                    noCategoryOption.remove();
                }
                
                // Tambahkan opsi baru
                const option = document.createElement('option');
                option.value = data.kategori.id;
                option.textContent = data.kategori.nama_kategori;
                option.selected = true;
                select.appendChild(option);
                
                updatePreview();
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('tambahKategoriModal'));
                modal.hide();
                
                document.getElementById('formTambahKategori').reset();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Kategori berhasil ditambahkan',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Gagal menambah kategori',
                    confirmButtonColor: '#dc3545'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat menyimpan kategori',
                confirmButtonColor: '#dc3545'
            });
        });
    }

    // Validasi form sebelum submit
    document.getElementById('alatForm').addEventListener('submit', function(e) {
        const jumlahTotal = parseInt(document.getElementById('jumlah_total').value);
        const jumlahTersedia = parseInt(document.getElementById('jumlah_tersedia').value);
        
        if (jumlahTersedia > jumlahTotal) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: 'Jumlah tersedia tidak boleh lebih dari jumlah total',
                confirmButtonColor: '#dc3545'
            });
            return false;
        }
        
        if (jumlahTersedia < 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: 'Jumlah tersedia tidak boleh negatif',
                confirmButtonColor: '#dc3545'
            });
            return false;
        }
    });

    // Real-time validation untuk jumlah tersedia
    document.getElementById('jumlah_total').addEventListener('change', function() {
        const jumlahTotal = parseInt(this.value);
        const jumlahTersedia = parseInt(document.getElementById('jumlah_tersedia').value);
        
        if (jumlahTersedia > jumlahTotal) {
            document.getElementById('jumlah_tersedia').value = jumlahTotal;
            Swal.fire({
                icon: 'warning',
                title: 'Penyesuaian Otomatis',
                text: 'Jumlah tersedia disesuaikan dengan jumlah total',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });

    // Event listeners untuk real-time preview
    document.getElementById('nama_alat').addEventListener('input', updatePreview);
    document.getElementById('kategori_id').addEventListener('change', updatePreview);
    document.getElementById('kondisi').addEventListener('change', updatePreview);
    document.getElementById('harga_alat').addEventListener('input', updatePreview);
</script>
@endpush

@push('styles')
<style>
    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .card-title {
        color: #495057;
        font-weight: 600;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }
    
    .alert-info {
        background-color: #e7f3ff;
        border-color: #b8d4ff;
        color: #004085;
    }
    
    .invalid-feedback {
        display: block;
    }
    
    .input-group .btn, .input-group .input-group-text {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    .input-group > .form-control {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    .input-group > .input-group-text:first-child {
        border-top-left-radius: 0.375rem;
        border-bottom-left-radius: 0.375rem;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
</style>
@endpush
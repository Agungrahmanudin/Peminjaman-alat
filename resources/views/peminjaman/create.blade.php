@extends('layouts.master')

@section('title', 'Buat Peminjaman')

@section('content')
    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <h5 class="alert-heading">❌ ERROR:</h5>
            <p class="mb-0">{{ session('error') }}</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <h5 class="alert-heading">❌ Validation Errors:</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <p class="mb-0">{{ session('success') }}</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-lg-center">
        <div class="col-lg-12">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Buat Peminjaman Baru</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('peminjaman.index') }}">Peminjaman</a></li>
                            <li class="breadcrumb-item active">Buat Peminjaman</li>
                        </ul>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('peminjaman.store') }}" method="POST" id="peminjamanForm">
                        @csrf

                        <div class="row">
                            {{-- ====== KOLOM KIRI ====== --}}
                            <div class="col-md-8">

                                {{-- Informasi Peminjam --}}
                                <div class="mb-4">
                                    <h5 class="card-title mb-3">Informasi Peminjam</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Peminjam</label>
                                                <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="text" class="form-control" value="{{ Auth::user()->email }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Informasi Peminjaman --}}
                                <div class="mb-4">
                                    <h5 class="card-title mb-3">Informasi Peminjaman</h5>
                                    <div class="mb-3">
                                        <label for="keperluan" class="form-label">Keperluan <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('keperluan') is-invalid @enderror"
                                                  id="keperluan" name="keperluan" rows="3"
                                                  placeholder="Deskripsi keperluan peminjaman" required>{{ old('keperluan') }}</textarea>
                                        @error('keperluan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="tanggal_kembali_rencana" class="form-label">
                                                    Tanggal Rencana Kembali <span class="text-danger">*</span>
                                                </label>
                                                <input type="date"
                                                       class="form-control @error('tanggal_kembali_rencana') is-invalid @enderror"
                                                       id="tanggal_kembali_rencana" name="tanggal_kembali_rencana"
                                                       value="{{ old('tanggal_kembali_rencana', date('Y-m-d', strtotime('+7 days'))) }}"
                                                       min="{{ date('Y-m-d') }}" required>
                                                @error('tanggal_kembali_rencana')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Tanggal Pinjam</label>
                                                <input type="text" class="form-control" value="{{ now()->format('d/m/Y H:i') }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Daftar Alat --}}
                                <div class="mb-4">
                                    <h5 class="card-title mb-3">
                                        Pilih Alat yang Akan Dipinjam
                                        <span class="badge bg-primary ms-2">{{ $alat_list->total() }} Alat Tersedia</span>
                                    </h5>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <input type="text" class="form-control" placeholder="Cari alat..."
                                                       id="searchAlatInput" onkeyup="searchAlat()">
                                                <button class="btn btn-outline-primary" type="button">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <select class="form-select" id="filterKategori" onchange="searchAlat()">
                                                <option value="">Semua Kategori</option>
                                                @foreach(App\Models\Kategori::all() as $kategori)
                                                    <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered" id="alatTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="50">
                                                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)">
                                                    </th>
                                                    <th width="100">Kode</th>
                                                    <th>Nama Alat</th>
                                                    <th width="120">Kategori</th>
                                                    <th width="80">Stok</th>
                                                    <th width="130">Jumlah Pinjam</th>
                                                    <th width="150">Kondisi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="alatTableBody">
                                                @forelse($alat_list as $alat)
                                                    <tr class="alat-row"
                                                        data-id="{{ $alat->id }}"
                                                        data-kategori="{{ $alat->kategori_id }}"
                                                        data-nama="{{ strtolower($alat->nama_alat) }}"
                                                        data-kode="{{ strtolower($alat->kode_alat) }}">
                                                        <td>
                                                            <input type="checkbox"
                                                                   class="alat-checkbox"
                                                                   data-id="{{ $alat->id }}"
                                                                   data-kode="{{ $alat->kode_alat }}"
                                                                   data-nama="{{ $alat->nama_alat }}"
                                                                   data-stok="{{ $alat->jumlah_tersedia }}"
                                                                   data-kategori-nama="{{ $alat->kategori->nama_kategori ?? '-' }}"
                                                                   onchange="toggleAlat(this)"
                                                                   @if($alat->jumlah_tersedia <= 0) disabled @endif>
                                                        </td>
                                                        <td><code>{{ $alat->kode_alat }}</code></td>
                                                        <td>
                                                            <strong>{{ $alat->nama_alat }}</strong>
                                                            @if($alat->keterangan)
                                                                <br><small class="text-muted">{{ Str::limit($alat->keterangan, 50) }}</small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info">{{ $alat->kategori->nama_kategori ?? '-' }}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            @if($alat->jumlah_tersedia > 0)
                                                                <span class="badge bg-success">{{ $alat->jumlah_tersedia }}</span>
                                                            @else
                                                                <span class="badge bg-danger">Habis</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{-- INPUT JUMLAH: diubah agar bisa diketik langsung, tidak disabled saat diceklis --}}
                                                            <input type="number"
                                                                   class="form-control form-control-sm alat-jumlah"
                                                                   data-id="{{ $alat->id }}"
                                                                   min="1"
                                                                   max="{{ $alat->jumlah_tersedia }}"
                                                                   value="1"
                                                                   style="width: 80px;"
                                                                   disabled
                                                                   oninput="updateJumlah({{ $alat->id }}, this.value)">
                                                        </td>
                                                        <td>
                                                            <select class="form-select form-select-sm alat-kondisi"
                                                                    data-id="{{ $alat->id }}"
                                                                    disabled
                                                                    onchange="updateKondisi({{ $alat->id }}, this.value)">
                                                                <option value="baik">Baik</option>
                                                                <option value="rusak">Rusak</option>
                                                                <option value="perbaikan">Perbaikan</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted py-3">
                                                            <i class="fas fa-tools fa-2x mb-2 d-block"></i>
                                                            Tidak ada alat yang tersedia
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    @if($alat_list->hasPages())
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <small class="text-muted">
                                                Menampilkan {{ $alat_list->firstItem() }} - {{ $alat_list->lastItem() }} dari {{ $alat_list->total() }} alat
                                            </small>
                                            <nav>
                                                <ul class="pagination pagination-sm mb-0">
                                                    @if($alat_list->onFirstPage())
                                                        <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                                                    @else
                                                        <li class="page-item"><a class="page-link" href="{{ $alat_list->previousPageUrl() }}">&laquo;</a></li>
                                                    @endif
                                                    @foreach($alat_list->getUrlRange(1, $alat_list->lastPage()) as $page => $url)
                                                        <li class="page-item {{ $page == $alat_list->currentPage() ? 'active' : '' }}">
                                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                                        </li>
                                                    @endforeach
                                                    @if($alat_list->hasMorePages())
                                                        <li class="page-item"><a class="page-link" href="{{ $alat_list->nextPageUrl() }}">&raquo;</a></li>
                                                    @else
                                                        <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                                                    @endif
                                                </ul>
                                            </nav>
                                        </div>
                                    @endif

                                    {{-- Hidden inputs — diisi via JS --}}
                                    <div id="alatInputs"></div>
                                </div>
                            </div>

                            {{-- ====== KOLOM KANAN ====== --}}
                            <div class="col-md-4">

                                {{-- Ringkasan --}}
                                <div class="mb-4">
                                    <h5 class="card-title mb-3">Alat Terpilih</h5>
                                    <div class="card bg-light">
                                        <div class="card-body p-0">
                                            <div id="selectedAlatList" style="max-height: 300px; overflow-y: auto;">
                                                <p class="text-muted text-center mb-0 p-3">
                                                    <i class="fas fa-info-circle"></i> Belum ada alat dipilih
                                                </p>
                                            </div>
                                            <hr class="my-0">
                                            <div class="p-3">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <strong>Total Alat:</strong>
                                                    <span id="totalAlatCount" class="badge bg-primary">0</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <strong>Total Unit:</strong>
                                                    <span id="totalUnitCount" class="badge bg-success">0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Info --}}
                                <div class="mb-4">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <small>
                                            <strong>Catatan:</strong><br>
                                            1. Centang alat yang ingin dipinjam<br>
                                            2. Atur jumlah sesuai stok tersedia<br>
                                            3. Pilih kondisi alat saat dipinjam<br>
                                            4. Isi keperluan & tanggal kembali
                                        </small>
                                    </div>
                                </div>

                                {{-- Validasi --}}
                                <div class="mb-4">
                                    <div class="alert alert-success d-none" id="validationSuccess">
                                        <i class="fas fa-check-circle"></i>
                                        <small>Form sudah valid dan siap disimpan</small>
                                    </div>
                                    <div class="alert alert-warning" id="validationWarning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <small>Pilih minimal 1 alat untuk dipinjam</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol --}}
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Batal
                                    </a>
                                    <div>
                                        <button type="button" class="btn btn-outline-secondary me-2" onclick="resetForm()">
                                            <i class="fas fa-redo"></i> Reset Form
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="submitButton" disabled>
                                            <i class="fas fa-save"></i> Simpan Peminjaman
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
@endsection

@push('scripts')
<script>
    // ================================================================
    // STATE
    // ================================================================
    let selectedAlat = {};  // { alatId: { id, kode, nama, kategoriNama, stok, jumlah, kondisi } }

    // ================================================================
    // AUTO-SELECT: jika datang dari halaman alat dengan ?alat_id=X
    // ================================================================
    document.addEventListener('DOMContentLoaded', function () {
        const params = new URLSearchParams(window.location.search);
        const alatIdFromUrl = params.get('alat_id');

        if (alatIdFromUrl) {
            const cb = document.querySelector(`.alat-checkbox[data-id="${alatIdFromUrl}"]`);
            if (cb && !cb.disabled) {
                cb.checked = true;
                toggleAlat(cb);
            }
        }

        validateForm();
    });

    // ================================================================
    // SEARCH / FILTER
    // ================================================================
    function searchAlat() {
        const searchText = document.getElementById('searchAlatInput').value.toLowerCase();
        const filterKategori = document.getElementById('filterKategori').value;

        document.querySelectorAll('.alat-row').forEach(row => {
            const nama     = row.getAttribute('data-nama');
            const kode     = row.getAttribute('data-kode');
            const kategori = row.getAttribute('data-kategori');

            const matchSearch   = !searchText   || nama.includes(searchText) || kode.includes(searchText);
            const matchKategori = !filterKategori || kategori === filterKategori;

            row.style.display = (matchSearch && matchKategori) ? '' : 'none';
        });
    }

    // ================================================================
    // SELECT ALL
    // ================================================================
    function toggleSelectAll(checkbox) {
        document.querySelectorAll('.alat-checkbox:not(:disabled)').forEach(cb => {
            if (cb.closest('tr').style.display !== 'none') {
                cb.checked = checkbox.checked;
                toggleAlat(cb);
            }
        });
    }

    // ================================================================
    // TOGGLE ALAT (centang/uncentang)
    // ================================================================
    function toggleAlat(checkbox) {
        const id           = checkbox.getAttribute('data-id');
        const jumlahInput  = document.querySelector(`.alat-jumlah[data-id="${id}"]`);
        const kondisiSelect = document.querySelector(`.alat-kondisi[data-id="${id}"]`);

        if (checkbox.checked) {
            // Aktifkan input
            jumlahInput.disabled  = false;
            kondisiSelect.disabled = false;

            // Tambah ke state
            selectedAlat[id] = {
                id:          id,
                kode:        checkbox.getAttribute('data-kode'),
                nama:        checkbox.getAttribute('data-nama'),
                kategoriNama: checkbox.getAttribute('data-kategori-nama'),
                stok:        parseInt(checkbox.getAttribute('data-stok')),
                jumlah:      parseInt(jumlahInput.value) || 1,
                kondisi:     kondisiSelect.value || 'baik',
            };
        } else {
            // Nonaktifkan input
            jumlahInput.disabled  = true;
            kondisiSelect.disabled = true;
            jumlahInput.value     = 1;
            kondisiSelect.value   = 'baik';

            delete selectedAlat[id];
        }

        renderSummary();
        updateHiddenInputs();
        validateForm();
    }

    // ================================================================
    // UPDATE JUMLAH — pakai oninput agar real-time
    // ================================================================
    function updateJumlah(id, value) {
        if (!selectedAlat[id]) return;

        let jumlah = parseInt(value);
        const stok = selectedAlat[id].stok;

        if (isNaN(jumlah) || jumlah < 1) jumlah = 1;
        if (jumlah > stok) {
            jumlah = stok;
            alert(`Maksimal stok tersedia: ${stok} unit`);
        }

        // Sinkronkan input
        const input = document.querySelector(`.alat-jumlah[data-id="${id}"]`);
        if (input) input.value = jumlah;

        selectedAlat[id].jumlah = jumlah;
        renderSummary();
        updateHiddenInputs();
    }

    // ================================================================
    // UPDATE KONDISI — dipanggil via onchange langsung di select
    // ================================================================
    function updateKondisi(id, value) {
        if (selectedAlat[id]) {
            selectedAlat[id].kondisi = value;
            updateHiddenInputs();
        }
    }

    // ================================================================
    // RENDER RINGKASAN KANAN
    // ================================================================
    function renderSummary() {
        const list       = document.getElementById('selectedAlatList');
        const countAlat  = document.getElementById('totalAlatCount');
        const countUnit  = document.getElementById('totalUnitCount');

        const items = Object.values(selectedAlat);

        countAlat.textContent = items.length;
        countUnit.textContent = items.reduce((s, a) => s + a.jumlah, 0);

        if (items.length === 0) {
            list.innerHTML = `<p class="text-muted text-center mb-0 p-3"><i class="fas fa-info-circle"></i> Belum ada alat dipilih</p>`;
            return;
        }

        let html = '<div class="list-group list-group-flush">';
        items.forEach(alat => {
            html += `
                <div class="list-group-item border-0 px-3 py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div style="flex:1;">
                            <strong class="d-block text-truncate" style="max-width:160px;">${alat.nama}</strong>
                            <small class="text-muted">${alat.kode} | ${alat.jumlah} unit | <span class="badge bg-secondary">${alat.kondisi}</span></small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger ms-2"
                                onclick="removeAlat('${alat.id}')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>`;
        });
        html += '</div>';
        list.innerHTML = html;
    }

    // ================================================================
    // HAPUS ALAT DARI RINGKASAN
    // ================================================================
    function removeAlat(id) {
        const cb = document.querySelector(`.alat-checkbox[data-id="${id}"]`);
        if (cb) {
            cb.checked = false;
            toggleAlat(cb);
        }
    }

    // ================================================================
    // UPDATE HIDDEN INPUTS (yang dikirim ke server)
    // ================================================================
    function updateHiddenInputs() {
        let html = '';
        Object.values(selectedAlat).forEach(alat => {
            html += `<input type="hidden" name="alat_id[]"       value="${alat.id}">`;
            html += `<input type="hidden" name="jumlah_pinjam[]" value="${alat.jumlah}">`;
            html += `<input type="hidden" name="kondisi_pinjam[]" value="${alat.kondisi}">`;
        });
        document.getElementById('alatInputs').innerHTML = html;
    }

    // ================================================================
    // VALIDASI FORM
    // ================================================================
    function validateForm() {
        const submitBtn = document.getElementById('submitButton');
        const ok        = document.getElementById('validationSuccess');
        const warn      = document.getElementById('validationWarning');

        const keperluan    = document.getElementById('keperluan').value.trim();
        const tanggal      = document.getElementById('tanggal_kembali_rencana').value;
        const alatDipilih  = Object.keys(selectedAlat).length > 0;

        const warnings = [];
        if (!keperluan)   warnings.push('Keperluan harus diisi');
        if (!tanggal)     warnings.push('Tanggal kembali harus diisi');
        if (!alatDipilih) warnings.push('Pilih minimal 1 alat');

        if (warnings.length === 0) {
            ok.classList.remove('d-none');
            warn.classList.add('d-none');
            submitBtn.disabled = false;
        } else {
            ok.classList.add('d-none');
            warn.classList.remove('d-none');
            warn.innerHTML = `<i class="fas fa-exclamation-triangle"></i> <small>${warnings.join(' &bull; ')}</small>`;
            submitBtn.disabled = true;
        }
    }

    // ================================================================
    // RESET FORM
    // ================================================================
    function resetForm() {
        selectedAlat = {};

        document.querySelectorAll('.alat-checkbox').forEach(cb => { cb.checked = false; });
        document.querySelectorAll('.alat-jumlah').forEach(el  => { el.disabled = true;  el.value = 1; });
        document.querySelectorAll('.alat-kondisi').forEach(el => { el.disabled = true;  el.value = 'baik'; });
        document.querySelectorAll('.alat-row').forEach(row    => { row.style.display = ''; });

        document.getElementById('keperluan').value = '';
        document.getElementById('tanggal_kembali_rencana').value = '{{ date("Y-m-d", strtotime("+7 days")) }}';
        document.getElementById('searchAlatInput').value = '';
        document.getElementById('filterKategori').value  = '';

        renderSummary();
        updateHiddenInputs();
        validateForm();
    }

    // ================================================================
    // SUBMIT GUARD
    // ================================================================
    document.getElementById('peminjamanForm').addEventListener('submit', function (e) {
        if (Object.keys(selectedAlat).length === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 alat untuk dipinjam');
            return;
        }
        if (!confirm('Simpan peminjaman ini?')) {
            e.preventDefault();
        }
    });

    // Real-time validasi
    document.getElementById('keperluan').addEventListener('input', validateForm);
    document.getElementById('tanggal_kembali_rencana').addEventListener('change', validateForm);
</script>
@endpush

@push('styles')
<style>
    .form-label      { font-weight: 600; margin-bottom: 0.5rem; }
    .card-title      { color: #495057; font-weight: 600; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.5rem; }
    .table th        { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; font-weight: 600; color: #495057; }
    .table td        { vertical-align: middle; }
    .badge           { font-size: 0.75em; padding: 0.35em 0.65em; font-weight: 500; }
    .alat-jumlah     { text-align: center; }
    .text-truncate   { max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    code             { background-color: #f8f9fa; padding: 0.2rem 0.4rem; border-radius: 0.25rem; font-size: 0.875em; color: #d63384; }
    input[type="checkbox"] { cursor: pointer; transform: scale(1.2); }
</style>
@endpush
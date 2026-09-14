@extends('layouts.master')

@section('title', 'Peminjaman')

@section('content')
<div class="row justify-content-lg-center">
    <div class="col-lg-10">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Peminjaman</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Peminjaman</li>
                    </ul>
                </div>
                <div class="col-auto">
                    @if(Auth::user()->role === 'peminjam')
                        <a href="{{ route('peminjaman.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Ajukan Peminjaman
                        </a>
                    @endif
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ================================================================ --}}
        {{-- TABEL 1: PEMINJAMAN AKTIF                                        --}}
        {{-- Aktif = menunggu / disetujui / dipinjam / terlambat              --}}
        {{-- ================================================================ --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-box-open me-2 text-primary"></i>Peminjaman Aktif
                </h5>
                {{-- Filter Aktif --}}
                <div class="d-flex gap-2">
                    <input type="text" class="form-control form-control-sm" style="width:200px;"
                           id="searchAktif" placeholder="Cari peminjam...">
                    <select class="form-select form-select-sm" style="width:180px;" id="statusAktifFilter">
                        <option value="">Semua Status</option>
                        <option value="menunggu">Menunggu</option>
                        <option value="dipinjam">Dipinjam</option>
                        <option value="terlambat">Terlambat</option>
                    </select>
                    <button class="btn btn-sm btn-outline-secondary" onclick="resetFilterAktif()">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Peminjam</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Alat</th>
                                <th>Rencana Kembali</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyAktif">
                            @php
                                $statusAktif = ['menunggu', 'dipinjam', 'terlambat', 'menunggu_pengembalian'];
                                $peminjamanAktif = $peminjaman->filter(function($item) use ($statusAktif) {
                                    $isAktif = in_array($item->status, $statusAktif) || $item->status_approval === 'menunggu';
                                    if (Auth::user()->role === 'peminjam') {
                                        return $isAktif && $item->user_id === Auth::id();
                                    }
                                    return $isAktif;
                                });
                                $noAktif = 1;
                            @endphp

                            @forelse($peminjamanAktif as $item)
                            <tr>
                                <td>{{ $noAktif++ }}</td>
                                <td>
                                    {{ $item->user->name }}
                                    @if(Auth::user()->role !== 'peminjam')
                                        <br><small class="text-muted">{{ $item->user->email }}</small>
                                        <br><small class="text-muted">{{ $item->user->phone }}</small>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</td>
                                <td>
                                    @foreach($item->detailPeminjaman as $detail)
                                        <span class="d-block">• {{ $detail->alat->nama_alat }} ({{ $detail->jumlah_pinjam }} pcs)</span>
                                    @endforeach
                                </td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('d/m/Y') }}</td>
                                <td>
                                    @if($item->status_approval === 'menunggu')
                                        <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Menunggu</span>
                                    @elseif($item->status === 'menunggu_pengembalian')
                                        <span class="badge bg-warning"><i class="fas fa-undo me-1"></i>Menunggu Pengembalian</span>
                                    @elseif($item->status === 'dipinjam')
                                        <span class="badge bg-info"><i class="fas fa-box-open me-1"></i>Dipinjam</span>
                                    @elseif($item->status === 'terlambat')
                                        <span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i>Terlambat</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        {{-- Detail --}}
                                        <a href="{{ route('peminjaman.show', $item->id) }}"
                                           class="btn btn-sm btn-outline-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- Admin/Petugas: Approve & Reject --}}
                                        @if(Auth::user()->role !== 'peminjam')
                                            @if($item->status_approval === 'menunggu')
                                                <form action="{{ route('peminjaman.approve', $item->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success"
                                                            onclick="return confirm('Setujui peminjaman ini?')" title="Setujui">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="rejectPeminjaman({{ $item->id }})" title="Tolak">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        @endif

                                        {{-- Peminjam: lihat alasan penolakan (tidak relevan di tabel aktif, tapi jaga-jaga) --}}
                                        @if(Auth::user()->role === 'peminjam' && $item->status_approval === 'ditolak' && $item->alasan_penolakan)
                                            <button type="button" class="btn btn-sm btn-outline-warning"
                                                    onclick="lihatAlasan('{{ addslashes($item->alasan_penolakan) }}')"
                                                    title="Lihat Alasan">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-box-open fa-2x mb-2 d-block"></i>
                                    Tidak ada peminjaman aktif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================================================================ --}}
        {{-- TABEL 2: RIWAYAT PEMINJAMAN                                      --}}
        {{-- Riwayat = dikembalikan / ditolak                                 --}}
        {{-- ================================================================ --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2 text-secondary"></i>Riwayat Peminjaman
                </h5>
                {{-- Filter Riwayat --}}
                <div class="d-flex gap-2">
                    <input type="text" class="form-control form-control-sm" style="width:200px;"
                           id="searchRiwayat" placeholder="Cari peminjam...">
                    <select class="form-select form-select-sm" style="width:180px;" id="statusRiwayatFilter">
                        <option value="">Semua Status</option>
                        <option value="dikembalikan">Dikembalikan</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                    <button class="btn btn-sm btn-outline-secondary" onclick="resetFilterRiwayat()">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Peminjam</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Alat</th>
                                <th>Rencana Kembali</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyRiwayat">
                            @php
                                $statusRiwayat = ['dikembalikan', 'ditolak'];
                                $peminjamanRiwayat = $peminjaman->filter(function($item) use ($statusRiwayat) {
                                    $isRiwayat = in_array($item->status, $statusRiwayat)
                                              || $item->status_approval === 'ditolak';
                                    if (Auth::user()->role === 'peminjam') {
                                        return $isRiwayat && $item->user_id === Auth::id();
                                    }
                                    return $isRiwayat;
                                });
                                $noRiwayat = 1;
                            @endphp

                            @forelse($peminjamanRiwayat as $item)
                            <tr>
                                <td>{{ $noRiwayat++ }}</td>
                                <td>
                                    {{ $item->user->name }}
                                    @if(Auth::user()->role !== 'peminjam')
                                        <br><small class="text-muted">{{ $item->user->email }}</small>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</td>
                                <td>
                                    @foreach($item->detailPeminjaman as $detail)
                                        <span class="d-block">• {{ $detail->alat->nama_alat }} ({{ $detail->jumlah_pinjam }} pcs)</span>
                                    @endforeach
                                </td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('d/m/Y') }}</td>
                                <td>
                                    @if($item->status_approval === 'ditolak')
                                        <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Ditolak</span>
                                    @elseif($item->status === 'dikembalikan')
                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Dikembalikan</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        {{-- Detail --}}
                                        <a href="{{ route('peminjaman.show', $item->id) }}"
                                           class="btn btn-sm btn-outline-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- Peminjam: lihat alasan penolakan --}}
                                        @if(Auth::user()->role === 'peminjam' && $item->status_approval === 'ditolak' && $item->alasan_penolakan)
                                            <button type="button" class="btn btn-sm btn-outline-warning"
                                                    onclick="lihatAlasan('{{ addslashes($item->alasan_penolakan) }}')"
                                                    title="Lihat Alasan">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        @endif

                                        {{-- Peminjam: tombol Hapus riwayat (hanya yang ditolak atau dikembalikan) --}}
                                        @if(Auth::user()->role === 'peminjam' && $item->user_id === Auth::id())
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Hapus"
                                                    onclick="confirmHapus({{ $item->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif

                                        {{-- Admin/Petugas: hapus juga bisa --}}
                                        @if(Auth::user()->role !== 'peminjam')
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Hapus"
                                                    onclick="confirmHapus({{ $item->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-history fa-2x mb-2 d-block"></i>
                                    Belum ada riwayat peminjaman
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        @if($peminjaman->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    Menampilkan {{ $peminjaman->firstItem() ?? 0 }} - {{ $peminjaman->lastItem() ?? 0 }}
                    dari {{ $peminjaman->total() }} peminjaman
                </div>
                <nav>{{ $peminjaman->links() }}</nav>
            </div>
        @endif

    </div>
</div>

{{-- ================= FORM HAPUS (tersembunyi) ================= --}}
<form id="deleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

{{-- ================= MODAL TOLAK (Admin/Petugas) ================= --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" id="rejectForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Peminjaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label for="alasan_penolakan" class="form-label">
                        Alasan Penolakan <span class="text-danger">*</span>
                    </label>
                    <textarea name="alasan_penolakan" id="alasan_penolakan" class="form-control" rows="4"
                        placeholder="Contoh: Stok tidak mencukupi, alat sedang perbaikan, dll" required></textarea>
                    <small class="text-muted">Alasan ini akan dilihat oleh peminjam.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Peminjaman</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================= MODAL LIHAT ALASAN (Peminjam) ================= --}}
<div class="modal fade" id="lihatAlasanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alasan Penolakan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="alasanText" class="p-3 bg-light rounded mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ============================================================
    // FILTER TABEL AKTIF
    // ============================================================
    function filterAktif() {
        const search = document.getElementById('searchAktif').value.toLowerCase();
        const status = document.getElementById('statusAktifFilter').value.toLowerCase();
        let no = 1;

        document.querySelectorAll('#tbodyAktif tr').forEach(row => {
            if (row.querySelector('td[colspan]')) return;
            const peminjam   = row.cells[1]?.textContent.toLowerCase() || '';
            const statusText = row.cells[5]?.textContent.toLowerCase() || '';
            const show = peminjam.includes(search) && (!status || statusText.includes(status));
            row.style.display = show ? '' : 'none';
            if (show) row.cells[0].textContent = no++;
        });
    }

    function resetFilterAktif() {
        document.getElementById('searchAktif').value = '';
        document.getElementById('statusAktifFilter').value = '';
        filterAktif();
    }

    document.getElementById('searchAktif').addEventListener('keyup', filterAktif);
    document.getElementById('statusAktifFilter').addEventListener('change', filterAktif);

    // ============================================================
    // FILTER TABEL RIWAYAT
    // ============================================================
    function filterRiwayat() {
        const search = document.getElementById('searchRiwayat').value.toLowerCase();
        const status = document.getElementById('statusRiwayatFilter').value.toLowerCase();
        let no = 1;

        document.querySelectorAll('#tbodyRiwayat tr').forEach(row => {
            if (row.querySelector('td[colspan]')) return;
            const peminjam   = row.cells[1]?.textContent.toLowerCase() || '';
            const statusText = row.cells[5]?.textContent.toLowerCase() || '';
            const show = peminjam.includes(search) && (!status || statusText.includes(status));
            row.style.display = show ? '' : 'none';
            if (show) row.cells[0].textContent = no++;
        });
    }

    function resetFilterRiwayat() {
        document.getElementById('searchRiwayat').value = '';
        document.getElementById('statusRiwayatFilter').value = '';
        filterRiwayat();
    }

    document.getElementById('searchRiwayat').addEventListener('keyup', filterRiwayat);
    document.getElementById('statusRiwayatFilter').addEventListener('change', filterRiwayat);

    // ============================================================
    // HAPUS PEMINJAMAN
    // ============================================================
    function confirmHapus(id) {
        if (confirm('Hapus data peminjaman ini? Tindakan tidak dapat dibatalkan.')) {
            const form = document.getElementById('deleteForm');
            form.action = '{{ url("peminjaman") }}/' + id;
            form.submit();
        }
    }

    // ============================================================
    // MODAL TOLAK
    // ============================================================
    function rejectPeminjaman(id) {
        document.getElementById('rejectForm').action = '/peminjaman/' + id + '/reject';
        new bootstrap.Modal(document.getElementById('rejectModal')).show();
    }

    // ============================================================
    // MODAL LIHAT ALASAN
    // ============================================================
    function lihatAlasan(alasan) {
        document.getElementById('alasanText').textContent = alasan;
        new bootstrap.Modal(document.getElementById('lihatAlasanModal')).show();
    }
</script>
@endpush

@push('styles')
<style>
    .card-header { background-color: #f8f9fa; }
    .card-title  { color: #343a40; font-weight: 600; }
    .table th    { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; font-weight: 600; }
    .badge       { font-size: 0.82em; padding: 0.4em 0.7em; }
    .table-hover tbody tr:hover { background-color: rgba(0,123,255,0.04); }
    .d-flex.gap-1 .btn { border-radius: 0.375rem !important; }
</style>
@endpush
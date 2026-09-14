@extends('layouts.master')

@section('title', 'Detail Peminjaman')

@section('content')
    @php
        // Peta status -> label, warna, ikon (satu sumber kebenaran untuk badge & status card)
        $statusMap = [
            'menunggu' => ['label' => 'Menunggu Persetujuan', 'color' => 'warning', 'icon' => 'fa-hourglass-half'],
            'dipinjam' => ['label' => 'Dipinjam', 'color' => 'primary', 'icon' => 'fa-box'],
            'menunggu_pengembalian' => [
                'label' => 'Menunggu Approval Pengembalian',
                'color' => 'info',
                'icon' => 'fa-user-clock',
            ],
            'dikembalikan' => ['label' => 'Dikembalikan', 'color' => 'success', 'icon' => 'fa-check-circle'],
            'ditolak' => ['label' => 'Ditolak', 'color' => 'danger', 'icon' => 'fa-times-circle'],
            'terlambat' => ['label' => 'Terlambat', 'color' => 'danger', 'icon' => 'fa-exclamation-triangle'],
        ];

        // status_approval 'menunggu' menang lebih dulu (belum di-approve sama sekali)
        $statusKey = $peminjaman->status_approval == 'menunggu' ? 'menunggu' : $peminjaman->status;
        $s = $statusMap[$statusKey] ?? [
            'label' => 'Tidak Diketahui',
            'color' => 'secondary',
            'icon' => 'fa-question-circle',
        ];
    @endphp

    <div class="row justify-content-lg-center">
        <div class="col-lg-10">
            <div class="page-header">
                <h3 class="page-title">Detail Peminjaman</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('peminjaman.index') }}">Peminjaman</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ul>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <!-- Left Column -->
                <div class="col-lg-7">
                    <!-- Informasi Peminjaman -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2 text-primary"></i>Informasi Peminjaman
                            </h5>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <th width="220" class="text-muted fw-normal">Peminjam</th>
                                    <td class="fw-medium">{{ $peminjaman->user->name }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">Tanggal Pinjam</th>
                                    <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">Rencana Kembali</th>
                                    <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">Tanggal Kembali Aktual</th>
                                    <td>
                                        @if ($peminjaman->tanggal_kembali_realisasi)
                                            {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_realisasi)->format('d/m/Y H:i') }}
                                        @else
                                            <span class="text-muted">Belum dikembalikan</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">Status</th>
                                    <td>
                                        <span class="badge bg-{{ $s['color'] }}">
                                            <i class="fas {{ $s['icon'] }} me-1"></i>{{ $s['label'] }}
                                        </span>
                                        @if ($peminjaman->status == 'ditolak' && $peminjaman->alasan_penolakan)
                                            <div class="small text-muted mt-1">{{ $peminjaman->alasan_penolakan }}</div>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Alat yang Dipinjam -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-tools me-2 text-primary"></i>Alat yang Dipinjam
                            </h5>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Kode Alat</th>
                                            <th>Nama Alat</th>
                                            <th>Jumlah</th>
                                            <th>Kondisi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($peminjaman->detailPeminjaman ?? [] as $d)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td><code>{{ $d->alat->kode_alat }}</code></td>
                                                <td>{{ $d->alat->nama_alat }}</td>
                                                <td><span class="badge bg-info-subtle text-info-emphasis">{{ $d->jumlah_pinjam }}</span></td>
                                                <td>
                                                    <span class="badge bg-{{ $d->kondisi_pinjam == 'baik' ? 'success' : ($d->kondisi_pinjam == 'rusak' ? 'danger' : 'warning') }}">
                                                        {{ ucfirst($d->kondisi_pinjam) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">Tidak ada alat yang dipinjam</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-history me-2 text-primary"></i>Timeline
                            </h5>
                        </div>
                        <div class="card-body px-4 pb-4">
                            @php
                                $sudahDipinjam = true;
                                $sudahJatuhTempo = in_array($statusKey, ['dikembalikan', 'terlambat']);
                                $sudahDikembalikan = $statusKey == 'dikembalikan';
                                $ditolakFlag = $statusKey == 'ditolak';
                            @endphp
                            <div class="timeline">
                                <div class="timeline-item {{ $sudahDipinjam ? 'active' : '' }}">
                                    <div class="timeline-point"></div>
                                    <div class="timeline-content">
                                        <small class="text-muted">Dipinjam</small>
                                        <p class="mb-0">{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d M Y') }}</p>
                                    </div>
                                </div>

                                @if ($ditolakFlag)
                                    <div class="timeline-item active timeline-danger">
                                        <div class="timeline-point"></div>
                                        <div class="timeline-content">
                                            <small class="text-muted">Ditolak</small>
                                            <p class="mb-0 text-danger">{{ $peminjaman->alasan_penolakan ?? '-' }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="timeline-item {{ $sudahJatuhTempo ? 'active' : '' }} {{ $statusKey == 'terlambat' ? 'timeline-danger' : '' }}">
                                        <div class="timeline-point"></div>
                                        <div class="timeline-content">
                                            <small class="text-muted">Jatuh Tempo</small>
                                            <p class="mb-0">{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d M Y') }}</p>
                                        </div>
                                    </div>

                                    @if ($sudahDikembalikan)
                                        <div class="timeline-item active">
                                            <div class="timeline-point"></div>
                                            <div class="timeline-content">
                                                <small class="text-muted">Dikembalikan</small>
                                                <p class="mb-0">{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_realisasi)->format('d M Y H:i') }}</p>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-lg-5">
                    <!-- Status Card -->
                    <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                        <div class="card-header bg-{{ $s['color'] }} text-white border-0 py-3">
                            <h5 class="card-title mb-0">
                                <i class="fas {{ $s['icon'] }} me-2"></i>Status Peminjaman
                            </h5>
                        </div>
                        <div class="card-body text-center py-4">
                            <i class="fas {{ $s['icon'] }} fa-3x text-{{ $s['color'] }} mb-3"></i>
                            <p class="mb-1 fw-bold">{{ $s['label'] }}</p>

                            @if ($statusKey == 'menunggu')
                                <small class="text-muted">Peminjaman sedang menunggu approval admin</small>
                            @elseif ($statusKey == 'dipinjam')
                                <small class="text-muted d-block">Harap dikembalikan sebelum</small>
                                <div class="fw-bold text-danger mb-3">{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d M Y') }}</div>

                                @if (!$peminjaman->pengembalian)
                                    <form action="{{ route('pengembalian.ajukan', $peminjaman->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-warning w-100">
                                            <i class="fas fa-undo-alt me-1"></i> Ajukan Pengembalian
                                        </button>
                                    </form>
                                @endif

                                @can('update-peminjaman')
                                    <button type="button" class="btn btn-success w-100 mt-2" data-bs-toggle="modal"
                                        data-bs-target="#kembalikanModal">
                                        <i class="fas fa-undo me-2"></i>Kembalikan
                                    </button>
                                @endcan
                            @elseif ($statusKey == 'menunggu_pengembalian')
                                <small class="text-muted">Pengembalian sedang diperiksa oleh admin</small>
                            @elseif ($statusKey == 'dikembalikan')
                                <small class="text-muted d-block">Tanggal pengembalian:</small>
                                <div class="fw-bold">{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_realisasi)->format('d M Y H:i') }}</div>
                            @elseif ($statusKey == 'ditolak')
                                <small class="text-muted">{{ $peminjaman->alasan_penolakan ?? 'Tidak ada alasan penolakan' }}</small>
                            @elseif ($statusKey == 'terlambat')
                                <small class="text-muted">Harap segera mengembalikan alat</small>
                            @endif
                        </div>
                    </div>

                    <!-- Card Perpanjangan (opsional) -->
                    @if ($peminjaman->status_perpanjangan === 'menunggu')
                        <div class="card border-0 shadow-sm mb-4 overflow-hidden border-start border-warning border-3">
                            <div class="card-header bg-warning text-dark border-0 py-3 px-4">
                                <h5 class="card-title mb-0 fw-bold">
                                    <i class="fas fa-clock me-2"></i>Pengajuan Perpanjangan
                                </h5>
                            </div>
                            <div class="card-body px-4 pb-4">
                                <div class="bg-light rounded-3 p-3 mb-3">
                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-1">Peminjam</small>
                                        <strong>{{ $peminjaman->user->name ?? '-' }}</strong>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block mb-2">Alat yang Dipinjam</small>
                                        @forelse($peminjaman->detailPeminjaman ?? [] as $d)
                                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                                <span><i class="fas fa-tools text-primary me-2"></i>{{ $d->alat->nama_alat ?? '-' }}</span>
                                                <span class="badge bg-info-subtle text-info-emphasis">{{ $d->jumlah_pinjam }} unit</span>
                                            </div>
                                        @empty
                                            <span class="text-muted">Tidak ada data alat</span>
                                        @endforelse
                                    </div>
                                </div>

                                <div class="bg-white border rounded-3 p-3 mb-3">
                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-1"><i class="fas fa-calendar-alt me-1"></i>Jatuh Tempo Saat Ini</small>
                                        <strong class="text-danger">{{ $peminjaman->tanggal_kembali_rencana?->format('d M Y') ?? '-' }}</strong>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block mb-1"><i class="fas fa-calendar-plus me-1"></i>Tanggal Perpanjangan Diminta</small>
                                        <strong class="text-success">{{ $peminjaman->tanggal_perpanjangan_diminta?->format('d M Y') ?? '-' }}</strong>
                                    </div>
                                </div>

                                <div class="alert alert-warning py-3 px-3 mb-3">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-info-circle me-2 mt-1"></i>
                                        <div>
                                            <strong>Keterangan</strong>
                                            <div class="small mt-1">Peminjam meminta tambahan waktu penggunaan alat hingga <strong>{{ $peminjaman->tanggal_perpanjangan_diminta?->format('d M Y') ?? '-' }}</strong>.</div>
                                        </div>
                                    </div>
                                </div>

                                @if (in_array(Auth::user()->role, ['admin', 'petugas']))
                                    <form action="{{ route('peminjaman.perpanjang.approve', $peminjaman->id) }}" method="POST" class="mb-2">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-check me-1"></i>Setujui Perpanjangan
                                        </button>
                                    </form>

                                    <button class="btn btn-outline-danger w-100" type="button" data-bs-toggle="collapse" data-bs-target="#formTolakPerpanjang">
                                        <i class="fas fa-times me-1"></i>Tolak Perpanjangan
                                    </button>

                                    <div class="collapse mt-2" id="formTolakPerpanjang">
                                        <form action="{{ route('peminjaman.perpanjang.reject', $peminjaman->id) }}" method="POST">
                                            @csrf
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Alasan Penolakan</label>
                                                <textarea name="alasan_tolak_perpanjangan" class="form-control form-control-sm" rows="3" placeholder="Contoh: Alat sudah dibutuhkan oleh pengguna lain."></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                                <i class="fas fa-times me-1"></i>Konfirmasi Tolak
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="alert alert-warning py-2 px-3 mb-0 small">
                                        <i class="fas fa-hourglass-half me-1"></i>Menunggu persetujuan admin/petugas
                                    </div>
                                @endif
                            </div>
                        </div>
                    @elseif ($peminjaman->status_perpanjangan === 'disetujui')
                        <div class="card border-0 shadow-sm mb-4 overflow-hidden border-start border-success border-3">
                            <div class="card-header bg-success text-white border-0 py-3 px-4">
                                <h5 class="card-title mb-0 fw-bold"><i class="fas fa-check-circle me-2"></i>Perpanjangan Disetujui</h5>
                            </div>
                            <div class="card-body px-4 py-3">
                                <p class="mb-2"><strong>Peminjam:</strong> {{ $peminjaman->user->name ?? '-' }}</p>
                                <p class="mb-0 text-success"><strong>Batas pengembalian baru: {{ $peminjaman->tanggal_kembali_rencana?->format('d M Y') ?? '-' }}</strong></p>
                            </div>
                        </div>
                    @elseif ($peminjaman->status_perpanjangan === 'ditolak')
                        <div class="card border-0 shadow-sm mb-4 overflow-hidden border-start border-danger border-3">
                            <div class="card-header bg-danger text-white border-0 py-3 px-4">
                                <h5 class="card-title mb-0 fw-bold"><i class="fas fa-times-circle me-2"></i>Perpanjangan Ditolak</h5>
                            </div>
                            <div class="card-body px-4 py-3">
                                <p class="mb-2"><strong>Peminjam:</strong> {{ $peminjaman->user->name ?? '-' }}</p>
                                @if ($peminjaman->alasan_tolak_perpanjangan)
                                    <div class="alert alert-danger py-2 px-3 mb-0">
                                        <small><strong>Alasan:</strong><br>{{ $peminjaman->alasan_tolak_perpanjangan }}</small>
                                    </div>
                                @else
                                    <small class="text-muted">Tidak ada alasan penolakan.</small>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('peminjaman.index') }}" class="btn btn-light border">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                                </a>

                                @can('update-peminjaman')
                                    @if ($peminjaman->status == 'dipinjam')
                                        <a href="{{ route('peminjaman.edit', $peminjaman->id) }}" class="btn btn-primary">
                                            <i class="fas fa-edit me-2"></i>Edit Peminjaman
                                        </a>
                                    @endif
                                @endcan

                                @can('delete-peminjaman')
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#hapusModal">
                                        <i class="fas fa-trash me-2"></i>Hapus Peminjaman
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Kembalikan -->
    @can('update-peminjaman')
        <div class="modal fade" id="kembalikanModal" tabindex="-1" aria-labelledby="kembalikanModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('peminjaman.kembalikan', $peminjaman->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="kembalikanModalLabel"><i class="fas fa-undo me-2 text-success"></i>Konfirmasi Pengembalian</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted">Konfirmasi bahwa alat berikut telah dikembalikan dalam kondisi:</p>
                            <div class="mb-3">
                                <label for="kondisi_kembali" class="form-label">Kondisi Alat Saat Dikembalikan</label>
                                <select name="kondisi_kembali" id="kondisi_kembali" class="form-select" required>
                                    <option value="baik">Baik</option>
                                    <option value="rusak_ringan">Rusak Ringan</option>
                                    <option value="rusak">Rusak</option>
                                </select>
                            </div>
                            <div class="mb-1">
                                <label for="catatan_kembali" class="form-label">Catatan (opsional)</label>
                                <textarea name="catatan_kembali" id="catatan_kembali" class="form-control" rows="3" placeholder="Contoh: baret kecil di sisi kiri"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success"><i class="fas fa-check me-2"></i>Konfirmasi Kembali</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    <!-- Modal Hapus -->
    <div class="modal fade" id="hapusModal" tabindex="-1" aria-labelledby="hapusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="hapusModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                        <p class="fw-bold">Apakah Anda yakin ingin menghapus peminjaman ini?</p>
                    </div>
                    <div class="alert alert-warning rounded-3">
                        <strong>Informasi Peminjaman:</strong><br>
                        <small>Kode: {{ $peminjaman->kode_peminjaman }}</small><br>
                        <small>Peminjam: {{ $peminjaman->user->name ?? 'N/A' }}</small><br>
                        <small>Tanggal: {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d M Y') }}</small>
                    </div>
                    <div class="alert alert-danger rounded-3 mb-0">
                        <i class="fas fa-exclamation-circle me-2"></i><strong>PERHATIAN:</strong> Data yang dihapus tidak dapat dikembalikan!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('peminjaman.destroy', $peminjaman->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-2"></i>Hapus Permanen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .timeline {
            position: relative;
            padding-left: 20px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 6px;
            top: 4px;
            bottom: 4px;
            width: 2px;
            background-color: #dee2e6;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-point {
            position: absolute;
            left: -20px;
            top: 4px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #adb5bd;
            border: 2px solid white;
            box-shadow: 0 0 0 1px #dee2e6;
        }

        .timeline-item.active .timeline-point {
            background-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2);
        }

        .timeline-item.timeline-danger.active .timeline-point {
            background-color: #dc3545;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.2);
        }

        .timeline-content {
            padding-left: 10px;
        }

        .table th {
            background-color: transparent;
        }

        .badge {
            font-size: 0.82em;
            padding: 0.4em 0.7em;
            font-weight: 500;
        }

        .card-header h5 {
            font-size: 1.05rem;
        }

        .alert {
            border-radius: 10px;
        }

        code {
            background-color: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
            color: #d63384;
        }

        .card {
            border-radius: 14px;
        }
    </style>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var kembalikanModal = document.getElementById('kembalikanModal');
            if (kembalikanModal) {
                kembalikanModal.addEventListener('shown.bs.modal', function() {
                    document.getElementById('kondisi_kembali').focus();
                });
            }
        });
    </script>
@endpush
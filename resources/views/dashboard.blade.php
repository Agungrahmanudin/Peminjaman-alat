@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')

{{-- CUSTOM CSS UNTUK TAMPILAN ELEGAN --}}
<style>
    /* Efek Hover Kartu */
    .hover-elevate { transition: all 0.3s ease; }
    .hover-elevate:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important; }

    /* Warna Pastel / Soft (Aman untuk Bootstrap versi lama) */
    .bg-soft-primary { background-color: #e7f1ff; color: #0d6efd; }
    .bg-soft-success { background-color: #e6f8f0; color: #198754; }
    .bg-soft-warning { background-color: #fff8e6; color: #ffc107; }
    .bg-soft-danger { background-color: #fce8e8; color: #dc3545; }
    .bg-soft-info { background-color: #e0f6fd; color: #0dcaf0; }

    /* Timeline Tracker Peminjam */
    .timeline-tracker { position: relative; display: flex; justify-content: space-between; align-items: center; margin: 20px 0 30px 0; }
    .timeline-tracker::before { content: ''; position: absolute; top: 50%; left: 0; right: 0; height: 4px; background: #e9ecef; z-index: 1; transform: translateY(-50%); }
    .timeline-step { position: relative; z-index: 2; background: white; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; border: 4px solid #e9ecef; color: #adb5bd; transition: all 0.3s; }
    .timeline-step.active { border-color: #0d6efd; color: #0d6efd; box-shadow: 0 0 0 5px rgba(13, 110, 253, 0.15); }
    .timeline-step.completed { background: #198754; border-color: #198754; color: white; }
    .timeline-label { position: absolute; top: 55px; font-size: 13px; color: #495057; white-space: nowrap; text-align: center; font-weight: 600; }

    /* Vertical Timeline Admin */
    .activity-timeline { position: relative; padding-left: 30px; margin-bottom: 0; }
    .activity-timeline::before { content: ''; position: absolute; top: 0; left: 7px; bottom: 0; width: 2px; background: #e9ecef; }
    .activity-item { position: relative; margin-bottom: 20px; }
    .activity-item:last-child { margin-bottom: 0; }
    .activity-point { position: absolute; left: -30px; top: 4px; width: 16px; height: 16px; border-radius: 50%; background: #fff; border: 3px solid #0d6efd; }
    .activity-point.success { border-color: #198754; }
    .activity-point.warning { border-color: #ffc107; }
    .activity-point.danger { border-color: #dc3545; }

    /* Badge Countdown (fitur baru) */
    .badge-countdown-danger { animation: pulseDanger 1.8s infinite; }
    @keyframes pulseDanger {
        0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.35); }
        70% { box-shadow: 0 0 0 8px rgba(220, 53, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }

    /* List item alat (fitur Pinjam Lagi / Perlu Perhatian) */
    .quick-item { transition: background 0.2s ease; border-radius: 10px; }
    .quick-item:hover { background: #f8f9fa; }
</style>

@php
    // LOGIKA SAPAAN WAKTU OTOMATIS
    $hour = date('H');
    if ($hour >= 5 && $hour < 11) $sapaan = 'Selamat Pagi';
    elseif ($hour >= 11 && $hour < 15) $sapaan = 'Selamat Siang';
    elseif ($hour >= 15 && $hour < 18) $sapaan = 'Selamat Sore';
    else $sapaan = 'Selamat Malam';
@endphp

{{-- CEK ROLE PENGGUNA --}}
@if(auth()->check() && auth()->user()->role == 'peminjam')

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm hover-elevate bg-primary text-white" style="border-radius: 20px; background-image: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%); overflow: hidden; position: relative;">
                <div class="card-body p-4 p-md-5 d-flex align-items-center justify-content-between position-relative" style="z-index: 2;">
                    <div>
                        <span class="badge bg-white text-primary mb-3 px-3 py-2 rounded-pill fw-bold shadow-sm">
                            <i class="fas fa-user-graduate me-1"></i> Area Peminjam
                        </span>
                        <h2 class="fw-bold mb-2 text-white">{{ $sapaan }}, {{ explode(' ', auth()->user()->name)[0] }}! 👋</h2>
                        <p class="mb-0 fs-6 text-light opacity-75" style="max-width: 600px;">Siapkan perlengkapan praktikmu hari ini. Cek status peminjaman dan pastikan tidak ada alat yang tertinggal.</p>
                    </div>
                    <div class="d-none d-md-block opacity-25">
                        <i class="fas fa-tools fa-6x" style="transform: rotate(-15deg);"></i>
                    </div>
                </div>
                <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%; z-index: 1;"></div>
            </div>
        </div>
    </div>

    @php
        $pinjamanMendesak = \App\Models\Peminjaman::where('user_id', auth()->id())
                            ->whereIn('status', ['aktif', 'dipinjam'])
                            ->orderBy('tanggal_kembali_rencana', 'asc')
                            ->first();

        // FITUR BARU: hitung sisa hari menuju tanggal kembali rencana
        $sisaHari = null;
        if ($pinjamanMendesak) {
            $tglKembali = \Carbon\Carbon::parse($pinjamanMendesak->tanggal_kembali_rencana)->startOfDay();
            $hariIni = \Carbon\Carbon::now()->startOfDay();
            $sisaHari = (int) floor(($tglKembali->timestamp - $hariIni->timestamp) / 86400);
        }
    @endphp

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm hover-elevate h-100" style="border-radius: 15px;">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-soft-primary p-3 rounded-circle me-3 text-center" style="width: 55px; height: 55px; line-height: 25px;">
                        <i class="fas fa-box-open fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 fw-bold fs-6">Aktif Dipinjam</p>
                        <h3 class="mb-0 fw-bold text-dark">{{ $pinjamanAktifUser ?? 0 }} <span class="fs-6 text-muted fw-normal">Alat</span></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm hover-elevate h-100" style="border-radius: 15px;">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-soft-success p-3 rounded-circle me-3 text-center" style="width: 55px; height: 55px; line-height: 25px;">
                        <i class="fas fa-check fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 fw-bold fs-6">Alat Tersedia</p>
                        <h3 class="mb-0 fw-bold text-dark">{{ $alatTersedia ?? 0 }} <span class="fs-6 text-muted fw-normal">Item</span></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm hover-elevate h-100" style="border-radius: 15px;">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-soft-danger p-3 rounded-circle me-3 text-center" style="width: 55px; height: 55px; line-height: 25px;">
                        <i class="fas fa-money-bill-wave fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 fw-bold fs-6">Tagihan Denda</p>
                        <h3 class="mb-0 fw-bold text-dark">Rp{{ number_format($tagihanUser ?? 0, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm hover-elevate h-100" style="border-radius: 15px; background: #f8f9fa;">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-soft-warning p-3 rounded-circle me-3 text-center border border-warning" style="width: 55px; height: 55px; line-height: 25px;">
                        <i class="fas fa-star fa-lg"></i>
                    </div>
                    <div class="w-100">
                        <div class="d-flex justify-content-between">
                            <p class="text-muted mb-1 fw-bold fs-6">Reputasi</p>
                            <span class="text-success fw-bold">100%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($pinjamanMendesak)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm hover-elevate" style="border-radius: 15px; border-top: 4px solid #ffc107 !important;">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4 flex-wrap gap-2">
                        <h5 class="fw-bold text-dark mb-0"><i class="fas fa-map-marker-alt text-danger me-2"></i>Lacak Peminjaman: <span class="text-primary">{{ $pinjamanMendesak->alat->nama_alat ?? 'Alat Anda' }}</span></h5>

                        {{-- FITUR BARU: badge countdown sisa hari, warna menyesuaikan urgensi --}}
                        @if ($sisaHari < 0)
                            <span class="badge bg-soft-danger badge-countdown-danger border border-danger px-3 py-2 rounded-pill">
                                <i class="fas fa-exclamation-triangle me-1"></i> Terlambat {{ abs($sisaHari) }} hari
                            </span>
                        @elseif ($sisaHari == 0)
                            <span class="badge bg-soft-danger badge-countdown-danger border border-danger px-3 py-2 rounded-pill">
                                <i class="fas fa-clock me-1"></i> Jatuh tempo hari ini
                            </span>
                        @elseif ($sisaHari <= 2)
                            <span class="badge bg-soft-warning text-dark border border-warning px-3 py-2 rounded-pill">
                                <i class="fas fa-clock me-1"></i> Sisa {{ $sisaHari }} hari lagi
                            </span>
                        @else
                            <span class="badge bg-soft-success border border-success px-3 py-2 rounded-pill">
                                <i class="fas fa-clock me-1"></i> Sisa {{ $sisaHari }} hari lagi
                            </span>
                        @endif
                    </div>

                    <div class="px-md-5 py-2">
                        <div class="timeline-tracker mb-4">
                            <div class="timeline-step completed">
                                <i class="fas fa-check"></i>
                                <div class="timeline-label text-success">Diajukan</div>
                            </div>
                            <div class="timeline-step {{ in_array(strtolower($pinjamanMendesak->status_approval ?? ''), ['disetujui']) ? 'completed' : 'active' }}">
                                <i class="fas {{ in_array(strtolower($pinjamanMendesak->status_approval ?? ''), ['disetujui']) ? 'fa-check' : 'fa-spinner fa-spin' }}"></i>
                                <div class="timeline-label {{ in_array(strtolower($pinjamanMendesak->status_approval ?? ''), ['disetujui']) ? 'text-success' : 'text-primary' }}">Disetujui</div>
                            </div>
                            <div class="timeline-step {{ strtolower($pinjamanMendesak->status) == 'dipinjam' ? 'active' : '' }}">
                                <i class="fas fa-handshake"></i>
                                <div class="timeline-label text-dark">Diambil</div>
                            </div>
                            <div class="timeline-step">
                                <i class="fas fa-box"></i>
                                <div class="timeline-label text-muted">Dikembalikan</div>
                            </div>
                        </div>
                    </div>

                    {{-- Perpanjangan — selalu tampil selama dipinjam/terlambat --}}
                    @if(in_array($pinjamanMendesak->status, ['dipinjam', 'terlambat']))
                        <div class="mt-4 pt-3 border-top">
                            @if ($pinjamanMendesak->status_perpanjangan === 'menunggu')
                                {{-- Sedang menunggu persetujuan --}}
                                <div class="alert alert-warning d-flex align-items-center gap-2 mb-0 py-2 px-3 rounded-3">
                                    <i class="fas fa-hourglass-half fa-spin"></i>
                                    <span class="small">Pengajuan perpanjangan sedang <strong>menunggu persetujuan admin</strong>. Tanggal diminta: <strong>{{ $pinjamanMendesak->tanggal_perpanjangan_diminta?->format('d/m/Y') }}</strong></span>
                                </div>
                            @elseif ($pinjamanMendesak->status_perpanjangan === 'ditolak')
                                {{-- Ditolak, bisa ajukan ulang --}}
                                <div class="alert alert-danger d-flex align-items-center gap-2 mb-2 py-2 px-3 rounded-3">
                                    <i class="fas fa-times-circle"></i>
                                    <span class="small">Perpanjangan sebelumnya <strong>ditolak</strong>.
                                        @if($pinjamanMendesak->alasan_tolak_perpanjangan)
                                            Alasan: {{ $pinjamanMendesak->alasan_tolak_perpanjangan }}
                                        @endif
                                    </span>
                                </div>
                                <form action="{{ route('peminjaman.perpanjang', $pinjamanMendesak->id) }}" method="POST" class="d-flex align-items-end gap-2 flex-wrap">
                                    @csrf
                                    <div class="flex-grow-1">
                                        <label class="form-label small mb-1">Ajukan tanggal baru</label>
                                        <input type="date" name="tanggal_perpanjangan_diminta" class="form-control form-control-sm"
                                            min="{{ now()->addDay()->format('Y-m-d') }}" required>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-warning rounded-pill px-3 fw-bold">
                                        <i class="fas fa-calendar-plus me-1"></i> Ajukan Ulang
                                    </button>
                                </form>
                            @else
                                {{-- Bisa ajukan perpanjangan --}}
                                <p class="mb-2 small text-muted">
                                    <i class="fas fa-calendar-plus me-1"></i>
                                    @if ($sisaHari < 0)
                                        Peminjaman melewati tenggat. Segera kembalikan atau ajukan perpanjangan.
                                    @elseif ($sisaHari <= 2)
                                        Waktu pengembalian hampir habis. Butuh waktu tambahan?
                                    @else
                                        Ingin memperpanjang masa pinjam? Ajukan perpanjangan sekarang.
                                    @endif
                                </p>
                                <form action="{{ route('peminjaman.perpanjang', $pinjamanMendesak->id) }}" method="POST" class="d-flex align-items-end gap-2 flex-wrap">
                                    @csrf
                                    <div class="flex-grow-1">
                                        <label class="form-label small mb-1">Tanggal perpanjangan yang diminta</label>
                                        <input type="date" name="tanggal_perpanjangan_diminta" class="form-control form-control-sm"
                                            min="{{ $pinjamanMendesak->tanggal_kembali_rencana->addDay()->format('Y-m-d') }}" required>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-warning rounded-pill px-3 fw-bold">
                                        <i class="fas fa-calendar-plus me-1"></i> Ajukan Perpanjangan
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100 hover-elevate" style="border-radius: 15px;">
                <div class="card-header bg-white pt-4 pb-3 border-bottom-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-clipboard-list text-primary me-2"></i>Histori Peminjaman</h5>
                    <a href="{{ route('peminjaman.index') }}" class="btn btn-sm bg-soft-primary text-primary rounded-pill px-3 fw-bold">Detail Data</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th class="ps-4 border-0">Nama Alat</th>
                                    <th class="border-0">Tanggal Transaksi</th>
                                    <th class="border-0">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($aktivitasTerbaruUser ?? [] as $aktivitas)
                                    <tr>
                                        <td class="ps-4 py-3 border-light">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light border p-2 rounded text-secondary me-3"><i class="fas fa-wrench"></i></div>
                                                <span class="fw-bold text-dark">{{ $aktivitas->alat->nama_alat ?? 'Alat Tidak Diketahui' }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 text-muted small border-light">
                                            @if(in_array(strtolower($aktivitas->status), ['dikembalikan', 'selesai']))
                                                <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Selesai:</span> {{ \Carbon\Carbon::parse($aktivitas->tanggal_kembali_realisasi)->translatedFormat('d M Y') }}
                                            @else
                                                <span class="text-primary fw-bold"><i class="fas fa-calendar-alt me-1"></i> Pinjam:</span> {{ \Carbon\Carbon::parse($aktivitas->tanggal_pinjam)->translatedFormat('d M Y') }}
                                            @endif
                                        </td>
                                        <td class="py-3 border-light">
                                            @if(strtolower($aktivitas->status) == 'dipinjam')
                                                <span class="badge bg-soft-warning text-dark px-3 py-2 border border-warning rounded-pill">Aktif</span>
                                            @elseif(strtolower($aktivitas->status) == 'dikembalikan' || strtolower($aktivitas->status) == 'selesai')
                                                <span class="badge bg-soft-success px-3 py-2 border border-success rounded-pill">Dikembalikan</span>
                                            @elseif(strtolower($aktivitas->status) == 'terlambat')
                                                <span class="badge bg-soft-danger px-3 py-2 border border-danger rounded-pill">Terlambat</span>
                                            @else
                                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">{{ ucfirst($aktivitas->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">
                                            <i class="fas fa-folder-open fa-3x mb-3 text-light"></i>
                                            <p class="mb-0">Belum ada riwayat transaksi Anda.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 mb-4 hover-elevate text-white" style="border-radius: 15px; background: linear-gradient(135deg, #2b32b2 0%, #1488cc 100%);">
                <div class="card-body p-4 text-center">
                    <h5 class="fw-bold text-white mb-3"><i class="fas fa-rocket me-2"></i>Aksi Cepat</h5>
                    <p class="small text-light opacity-75 mb-4">Butuh perlengkapan praktik tambahan hari ini?</p>
                    <a href="{{ url('/peminjaman/create') }}" class="btn btn-light text-primary w-100 mb-2 py-2 rounded-pill shadow-sm fw-bold">
                        <i class="fas fa-plus-circle me-1"></i> Pinjam Alat Baru
                    </a>
                </div>
            </div>

            {{-- FITUR BARU: Pinjam Lagi (reorder cepat dari alat yang sering dipakai user) --}}
            <div class="card shadow-sm border-0 mb-4 hover-elevate" style="border-radius: 15px;">
                <div class="card-header bg-white pt-4 pb-2 border-bottom-0">
                    <h6 class="fw-bold text-dark mb-0"><i class="fas fa-redo text-info me-2"></i>Pinjam Lagi</h6>
                    <small class="text-muted">Alat yang sering Anda gunakan</small>
                </div>
                <div class="card-body pt-2">
                    <ul class="list-group list-group-flush">
                        @forelse($riwayatPinjamUser ?? [] as $riwayatAlat)
                            <li class="list-group-item quick-item px-2 py-2 border-0 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="bg-soft-info p-2 rounded-circle me-3"><i class="fas fa-tools"></i></div>
                                    <span class="fw-bold fs-6 text-dark">{{ $riwayatAlat->nama_alat }}</span>
                                </div>
                                @if(($riwayatAlat->jumlah_tersedia ?? 0) > 0)
                                    <a href="{{ url('/peminjaman/create?alat='.$riwayatAlat->id) }}" class="btn btn-sm bg-soft-primary text-primary rounded-pill px-3 fw-bold">
                                        <i class="fas fa-plus me-1"></i>Pinjam
                                    </a>
                                @else
                                    <span class="badge bg-light text-muted border rounded-pill px-3 py-2">Kosong</span>
                                @endif
                            </li>
                        @empty
                            <p class="text-muted text-center py-3 small mb-0">Belum ada riwayat peminjaman untuk dipinjam ulang.</p>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm border-0 hover-elevate" style="border-radius: 15px;">
                <div class="card-header bg-white pt-4 pb-2 border-bottom-0">
                    <h6 class="fw-bold text-dark mb-0"><i class="fas fa-fire text-danger me-2"></i>Sering Dipinjam</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($alatPopuler ?? [] as $alat)
                            <li class="list-group-item px-0 py-3 border-light d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="bg-soft-info p-2 rounded-circle me-3"><i class="fas fa-tools"></i></div>
                                    <div>
                                        <h6 class="mb-0 fw-bold fs-6 text-dark">{{ $alat->nama_alat }}</h6>
                                        <small class="text-success"><i class="fas fa-circle" style="font-size: 8px;"></i> Tersedia</small>
                                    </div>
                                </div>
                                <a href="#" class="btn btn-sm btn-light border text-dark rounded-circle"><i class="fas fa-chevron-right"></i></a>
                            </li>
                        @empty
                            <p class="text-muted text-center py-3 small mb-0">Belum ada data popularitas alat.</p>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

@else

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0 text-dark">{{ $sapaan }}, {{ ucfirst(auth()->user()->role) }}!</h3>
            <p class="text-muted mb-0">Ringkasan operasional peminjaman alat praktik.</p>
        </div>
        <div class="bg-white px-4 py-2 rounded-pill shadow-sm border d-flex align-items-center">
            <div class="bg-soft-primary p-2 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                <i class="fas fa-calendar-day text-primary"></i>
            </div>
            <span class="text-dark fw-bold">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 col-12 mb-3">
            <div class="card shadow-sm border-0 hover-elevate h-100" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-soft-primary p-3 rounded-3"><i class="fas fa-boxes fa-2x"></i></div>
                    </div>
                    <p class="text-muted mb-1 fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 1px;">Koleksi Alat</p>
                    <h2 class="mb-0 fw-bold text-dark">{{ $totalAlat ?? 0 }} <span class="fs-6 text-muted fw-normal">Item</span></h2>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 mb-3">
            <div class="card shadow-sm border-0 hover-elevate h-100" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-soft-warning p-3 rounded-3"><i class="fas fa-people-carry fa-2x"></i></div>
                    </div>
                    <p class="text-muted mb-1 fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 1px;">Dipinjam Siswa</p>
                    <h2 class="mb-0 fw-bold text-dark">{{ $alatDipinjam ?? 0 }} <span class="fs-6 text-muted fw-normal">Item</span></h2>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 mb-3">
            <div class="card shadow-sm border-0 hover-elevate h-100" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-soft-success p-3 rounded-3"><i class="fas fa-check-double fa-2x"></i></div>
                    </div>
                    <p class="text-muted mb-1 fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 1px;">Siap Digunakan</p>
                    <h2 class="mb-0 fw-bold text-dark">{{ $alatTersedia ?? 0 }} <span class="fs-6 text-muted fw-normal">Item</span></h2>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 mb-3">
            <div class="card shadow-sm border-0 hover-elevate h-100" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-soft-danger p-3 rounded-3"><i class="fas fa-tools fa-2x"></i></div>
                    </div>
                    <p class="text-muted mb-1 fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 1px;">Perbaikan/Rusak</p>
                    <h2 class="mb-0 fw-bold text-dark">{{ $alatRusak ?? 0 }} <span class="fs-6 text-muted fw-normal">Item</span></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-4 d-flex flex-column mb-4 mb-lg-0">
            <div class="card shadow-sm border-0 mb-4 hover-elevate" style="border-radius: 15px; background: linear-gradient(135deg, #198754 0%, #115736 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p class="mb-0 fw-bold opacity-75">Pemasukan Denda</p>
                        <i class="fas fa-wallet fa-lg opacity-50"></i>
                    </div>
                    <h2 class="fw-bold mb-0 text-white">Rp{{ number_format($totalUangMasuk ?? 0, 0, ',', '.') }}</h2>
                </div>
            </div>

            <div class="card flex-fill shadow-sm border-0 hover-elevate" style="border-radius: 15px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h6 class="card-title fw-bold text-dark mb-0"><i class="fas fa-chart-pie text-primary me-2"></i>Status Inventaris</h6>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center pb-2">
                    <div id="render_donut_chart" style="width: 100%;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 d-flex flex-column mb-4 mb-lg-0">
            <div class="card flex-fill shadow-sm border-0 hover-elevate" style="border-radius: 15px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="card-title fw-bold text-dark mb-0"><i class="fas fa-chart-bar text-primary me-2"></i>Statistik Pergerakan</h6>
                </div>
                <div class="card-body pt-0 mt-3">
                    <div id="render_bar_chart" style="width: 100%;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 d-flex flex-column">
            <div class="card flex-fill shadow-sm border-0 hover-elevate" style="border-radius: 15px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
                    <h6 class="fw-bold text-dark mb-0"><i class="fas fa-bolt text-warning me-2"></i>Sistem Terkini</h6>
                </div>
                <div class="card-body">
                    <div class="activity-timeline">
                        <div class="activity-item">
                            <div class="activity-point danger"></div>
                            <h6 class="mb-1 fw-bold text-danger">Perhatian: Terlambat</h6>
                            <p class="text-muted small mb-0">Terdapat <strong>{{ $countTerlambat ?? 0 }} alat</strong> yang belum dikembalikan melewati batas waktu.</p>
                        </div>

                        <div class="activity-item">
                            <div class="activity-point warning"></div>
                            <h6 class="mb-1 fw-bold text-warning">Menunggu Persetujuan</h6>
                            <p class="text-muted small mb-0">Ada <strong>{{ $countMenunggu ?? 0 }} permohonan</strong> peminjaman baru yang butuh konfirmasi.</p>
                        </div>

                        <div class="activity-item">
                            <div class="activity-point success"></div>
                            <h6 class="mb-1 fw-bold text-success">Sistem Berjalan Normal</h6>
                            <p class="text-muted small mb-0">Semua layanan peminjaman aktif dan termonitoring dengan baik hari ini.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 mb-4 hover-elevate" style="border-radius: 15px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-rocket text-primary me-2"></i>Navigasi Cepat</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ url('/peminjaman/create') }}" class="btn bg-soft-primary text-primary w-100 py-3 rounded-3 fw-bold border-0 hover-elevate">
                                <i class="fas fa-cart-plus fa-2x mb-2 d-block"></i> Pinjam
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="btn bg-soft-warning text-dark w-100 py-3 rounded-3 fw-bold border-0 hover-elevate">
                                <i class="fas fa-qrcode fa-2x mb-2 d-block"></i> Scan
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ url('/alat/create') }}" class="btn bg-soft-success text-success w-100 py-3 rounded-3 fw-bold border-0 hover-elevate">
                                <i class="fas fa-box fa-2x mb-2 d-block"></i> Alat Baru
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="btn bg-light border text-dark w-100 py-3 rounded-3 fw-bold hover-elevate">
                                <i class="fas fa-print fa-2x mb-2 d-block"></i> Laporan
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 hover-elevate" style="border-radius: 15px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0"><i class="fas fa-medal text-warning me-2"></i>Peminjam Teraktif</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($topPeminjam ?? [] as $index => $userActive)
                            <li class="list-group-item px-0 py-2 border-light d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="fw-bold text-muted me-3">#{{ $index + 1 }}</div>
                                    <div class="bg-soft-primary text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                        {{ strtoupper(substr($userActive->name, 0, 1)) }}
                                    </div>
                                    <span class="fw-bold fs-6 text-dark" style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $userActive->name }}</span>
                                </div>
                                <span class="badge bg-light text-dark border px-2 py-1 rounded">{{ $userActive->peminjaman_count }} TRX</span>
                            </li>
                        @empty
                            <p class="text-muted small text-center mb-0 mt-2">Belum ada data peminjam.</p>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100 hover-elevate" style="border-radius: 15px;">
                <div class="card-header bg-white pt-4 pb-3 border-bottom-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-list-alt text-success me-2"></i>Log Peminjaman Terakhir</h6>
                    <a href="{{ url('/peminjaman') }}" class="btn btn-sm bg-soft-primary text-primary rounded-pill px-3 fw-bold">Semua Data</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th class="ps-4 border-0">Peminjam</th>
                                    <th class="border-0">Alat</th>
                                    <th class="border-0">Tgl Pinjam</th>
                                    <th class="border-0">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaksiTerbaru ?? [] as $transaksi)
                                    <tr>
                                        <td class="ps-4 py-3 border-light">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-white border text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 38px; height: 38px;">
                                                    {{ strtoupper(substr($transaksi->user->name ?? 'U', 0, 1)) }}
                                                </div>
                                                <span class="fw-bold text-dark">{{ $transaksi->user->name ?? 'User Dihapus' }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 text-secondary border-light">{{ $transaksi->alat->nama_alat ?? 'Alat Dihapus' }}</td>
                                        <td class="py-3 text-secondary border-light"><i class="fas fa-calendar-alt me-1 text-muted"></i> {{ \Carbon\Carbon::parse($transaksi->tanggal_pinjam)->translatedFormat('d M Y') }}</td>
                                        <td class="py-3 border-light">
                                            @if(strtolower($transaksi->status) == 'dipinjam')
                                                <span class="badge bg-soft-warning text-dark px-3 py-2 border border-warning rounded-pill">Dipinjam</span>
                                            @elseif(in_array(strtolower($transaksi->status), ['selesai', 'dikembalikan']))
                                                <span class="badge bg-soft-success px-3 py-2 border border-success rounded-pill">Selesai</span>
                                            @elseif(strtolower($transaksi->status) == 'terlambat')
                                                <span class="badge bg-soft-danger px-3 py-2 border border-danger rounded-pill">Terlambat</span>
                                            @elseif(strtolower($transaksi->status) == 'menunggu')
                                                <span class="badge bg-soft-primary px-3 py-2 border border-primary rounded-pill">Menunggu Konfirmasi</span>
                                            @else
                                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">{{ ucfirst($transaksi->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">
                                            <i class="fas fa-folder-open fa-3x mb-3 text-light"></i><br>
                                            Belum ada log transaksi terbaru.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FITUR BARU: Jadwal Pengembalian Mendatang + Alat Perlu Perhatian --}}
    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm border-0 h-100 hover-elevate" style="border-radius: 15px;">
                <div class="card-header bg-white pt-4 pb-3 border-bottom-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-calendar-check text-primary me-2"></i>Jadwal Pengembalian Mendatang</h6>
                    <a href="{{ url('/peminjaman?status=dipinjam') }}" class="btn btn-sm bg-soft-primary text-primary rounded-pill px-3 fw-bold">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th class="ps-4 border-0">Peminjam</th>
                                    <th class="border-0">Alat</th>
                                    <th class="border-0">Jatuh Tempo</th>
                                    <th class="border-0 pe-4">Sisa Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jadwalPengembalianMendatang ?? [] as $jadwal)
                                    @php
                                        $tglJatuhTempo = \Carbon\Carbon::parse($jadwal->tanggal_kembali_rencana)->startOfDay();
                                        $sisaJadwal = (int) floor(($tglJatuhTempo->timestamp - \Carbon\Carbon::now()->startOfDay()->timestamp) / 86400);
                                    @endphp
                                    <tr>
                                        <td class="ps-4 py-3 border-light fw-bold text-dark">{{ $jadwal->user->name ?? 'N/A' }}</td>
                                        <td class="py-3 text-secondary border-light">{{ $jadwal->alat->nama_alat ?? '-' }}</td>
                                        <td class="py-3 text-secondary border-light">{{ $tglJatuhTempo->translatedFormat('d M Y') }}</td>
                                        <td class="py-3 border-light pe-4">
                                            @if ($sisaJadwal < 0)
                                                <span class="badge bg-soft-danger border border-danger px-3 py-2 rounded-pill">Telat {{ abs($sisaJadwal) }} hari</span>
                                            @elseif ($sisaJadwal == 0)
                                                <span class="badge bg-soft-danger border border-danger px-3 py-2 rounded-pill">Hari ini</span>
                                            @elseif ($sisaJadwal <= 2)
                                                <span class="badge bg-soft-warning text-dark border border-warning px-3 py-2 rounded-pill">{{ $sisaJadwal }} hari lagi</span>
                                            @else
                                                <span class="badge bg-soft-success border border-success px-3 py-2 rounded-pill">{{ $sisaJadwal }} hari lagi</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">
                                            <i class="fas fa-calendar-check fa-3x mb-3 text-light"></i><br>
                                            Tidak ada peminjaman yang mendekati jatuh tempo.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm border-0 h-100 hover-elevate" style="border-radius: 15px;">
                <div class="card-header bg-white pt-4 pb-2 border-bottom-0">
                    <h6 class="fw-bold text-dark mb-0"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Alat Perlu Perhatian</h6>
                    <small class="text-muted">Rusak atau stok tersedia habis</small>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($alatPerluPerhatian ?? [] as $alatPerhatian)
                            <li class="list-group-item quick-item px-2 py-2 border-0 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="bg-soft-danger p-2 rounded-circle me-3"><i class="fas fa-tools"></i></div>
                                    <div>
                                        <h6 class="mb-0 fw-bold fs-6 text-dark">{{ $alatPerhatian->nama_alat }}</h6>
                                        <small class="text-muted">{{ $alatPerhatian->kode_alat }}</small>
                                    </div>
                                </div>
                                @if (($alatPerhatian->kondisi ?? '') != 'baik')
                                    <span class="badge bg-soft-danger border border-danger px-3 py-2 rounded-pill">{{ ucfirst($alatPerhatian->kondisi) }}</span>
                                @else
                                    <span class="badge bg-soft-warning text-dark border border-warning px-3 py-2 rounded-pill">Stok Habis</span>
                                @endif
                            </li>
                        @empty
                            <p class="text-muted text-center py-3 small mb-0">Semua alat dalam kondisi baik dan tersedia.</p>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endif

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // GRAFIK BATANG ADMIN
        var barElement = document.getElementById("render_bar_chart");
        if(barElement) {
            var optionsBar = {
                chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'inherit' },
                series: [{ name: 'Jumlah Transaksi', data: [{{ $totalPeminjaman ?? 0 }}, {{ $dikembalikan ?? 0 }}, {{ $terlambat ?? 0 }}, {{ $aktif ?? 0 }}] }],
                xaxis: {
                    categories: ['Semua', 'Kembali', 'Telat', 'Aktif'],
                    labels: { style: { colors: '#6c757d', fontWeight: 'bold' } },
                    axisBorder: { show: false }, axisTicks: { show: false }
                },
                yaxis: { show: false }, grid: { show: false },
                colors: ['#0d6efd', '#198754', '#dc3545', '#ffc107'],
                plotOptions: { bar: { borderRadius: 6, columnWidth: '40%', distributed: true } },
                dataLabels: { enabled: true, style: { fontSize: '14px', colors: ['#343a40'] }, offsetY: -25 },
                legend: { show: false }
            };
            var chartBar = new ApexCharts(barElement, optionsBar);
            chartBar.render();
        }

        // GRAFIK DONUT ADMIN
        var donutElement = document.getElementById("render_donut_chart");
        if(donutElement) {
            var optionsDonut = {
                chart: { type: 'donut', height: 280, fontFamily: 'inherit' },
                series: [{{ $alatTersedia ?? 0 }}, {{ $alatDipinjam ?? 0 }}, {{ $alatRusak ?? 0 }}],
                labels: ['Tersedia', 'Dipinjam', 'Rusak'],
                colors: ['#198754', '#ffc107', '#dc3545'],
                stroke: { width: 0 },
                legend: { position: 'bottom', markers: { radius: 12 } },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '75%',
                            labels: {
                                show: true,
                                name: { show: false },
                                value: { show: true, fontSize: '28px', fontWeight: 'bold', color: '#212529' },
                                total: { show: true, label: 'Total Alat', color: '#adb5bd' }
                            }
                        }
                    }
                }
            };
            var chartDonut = new ApexCharts(donutElement, optionsDonut);
            chartDonut.render();
        }
    });
</script>

@endsection
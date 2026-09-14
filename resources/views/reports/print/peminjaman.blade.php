<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peminjaman Alat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .info-section {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        .info-section table {
            width: 100%;
        }
        .info-section td {
            padding: 5px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            text-align: center;
            font-size: 12px;
        }
        table.data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }
        .summary-box {
            border: 1px solid #4CAF50;
            padding: 10px;
            margin-top: 20px;
            border-radius: 5px;
            background-color: #f0f9f0;
        }
        .badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }
        .badge-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PEMINJAMAN ALAT</h1>
        <p>Periode: {{ request('start_date', 'Semua') }} - {{ request('end_date', 'Semua') }}</p>
        <p>Tanggal Cetak: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="info-section">
        <table>
            <tr>
                <td width="150px"><strong>Total Peminjaman</strong></td>
                <td>: {{ $totalPeminjaman ?? 0 }}</td>
                <td width="150px"><strong>Peminjaman Aktif</strong></td>
                <td>: {{ $peminjamanAktif ?? 0 }}</td>
            </tr>
            <tr>
                <td><strong>Peminjaman Terlambat</strong></td>
                <td>: {{ $peminjamanTerlambat ?? 0 }}</td>
                <td><strong>Peminjaman Selesai</strong></td>
                <td>: {{ $peminjamanDikembalikan ?? 0 }}</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">ID</th>
                <th width="15%">Peminjam</th>
                <th width="20%">Alat</th>
                <th width="10%">Tgl Pinjam</th>
                <th width="10%">Tgl Rencana</th>
                <th width="10%">Tgl Kembali</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($peminjaman as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->id }}</td>
                    <td>{{ $item->user->name ?? '-' }}</td>
                    <td>
                        @foreach($item->detailPeminjaman as $detail)
                            - {{ $detail->alat->nama_alat ?? '-' }} ({{ $detail->jumlah_pinjam }} pcs)<br>
                        @endforeach
                    </td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('d/m/Y') }}</td>
                    <td class="text-center">
                        {{ $item->tanggal_kembali_realisasi ? \Carbon\Carbon::parse($item->tanggal_kembali_realisasi)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="text-center">
                        @php
                            $statusClass = match($item->status) {
                                'dipinjam' => 'badge-info',
                                'dikembalikan' => 'badge-success',
                                'terlambat' => 'badge-danger',
                                default => 'badge-warning'
                            };
                            $statusText = match($item->status) {
                                'dipinjam' => 'Dipinjam',
                                'dikembalikan' => 'Dikembalikan',
                                'terlambat' => 'Terlambat',
                                default => ucfirst($item->status)
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">
                        Tidak ada data peminjaman
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh: {{ Auth::user()->name ?? 'Sistem' }}</p>
        <p>© {{ date('Y') }} - Aplikasi Peminjaman Alat</p>
    </div>
</body>
</html>
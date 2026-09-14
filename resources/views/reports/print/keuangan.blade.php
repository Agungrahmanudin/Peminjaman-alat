<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan</title>
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
        .summary-box {
            background-color: #f0f8ff;
            border: 1px solid #007bff;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .summary-box table {
            width: 100%;
        }
        .summary-box td {
            padding: 5px;
        }
        .summary-box .label {
            font-weight: bold;
            width: 200px;
        }
        .summary-box .value {
            font-size: 16px;
            font-weight: bold;
        }
        .text-success {
            color: #28a745;
        }
        .text-danger {
            color: #dc3545;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th {
            background-color: #6c757d;
            color: white;
            padding: 10px;
            text-align: center;
        }
        table.data-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN KEUANGAN</h1>
        <p>Periode: {{ request('start_date', 'Semua') }} - {{ request('end_date', 'Semua') }}</p>
        <p>Tanggal Cetak: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="summary-box">
        <h3 style="margin-top:0;">Ringkasan Keuangan</h3>
        <table>
            <tr>
                <td class="label">Total Peminjaman:</td>
                <td class="value">{{ $totalPeminjaman ?? 0 }}</td>
                <td class="label">Total Terlambat:</td>
                <td class="value">{{ $totalTerlambat ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Total Denda:</td>
                <td class="value">Rp {{ number_format($totalDenda ?? 0, 0, ',', '.') }}</td>
                <td class="label">Denda Lunas:</td>
                <td class="value">{{ $totalDendaLunas ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Total Dibayar:</td>
                <td class="value text-success">Rp {{ number_format($totalDendaDibayar ?? 0, 0, ',', '.') }}</td>
                <td class="label">Denda Belum Lunas:</td>
                <td class="value">{{ $totalDendaBelum ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">Sisa Denda:</td>
                <td class="value text-danger">Rp {{ number_format($sisaDenda ?? 0, 0, ',', '.') }}</td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>

    <h3>Detail Peminjaman</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>Peminjam</th>
                <th>Tgl Pinjam</th>
                <th>Tgl Rencana</th>
                <th>Status</th>
                <th>Denda</th>
            </tr>
        </thead>
        <tbody>
            @forelse($peminjaman as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->id }}</td>
                    <td>{{ $item->user->name ?? '-' }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('d/m/Y') }}</td>
                    <td class="text-center">{{ ucfirst($item->status) }}</td>
                    <td class="text-right">Rp {{ number_format($item->denda ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data peminjaman</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3>Detail Pengembalian & Denda</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>ID Pinjam</th>
                <th>Peminjam</th>
                <th>Tgl Kembali</th>
                <th>Denda</th>
                <th>Dibayar</th>
                <th>Sisa</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengembalian as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->peminjaman_id }}</td>
                    <td>{{ $item->peminjaman->user->name ?? '-' }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_kembali_realisasi)->format('d/m/Y') }}</td>
                    <td class="text-right">Rp {{ number_format($item->denda, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->denda_dibayar, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->denda - $item->denda_dibayar, 0, ',', '.') }}</td>
                    <td class="text-center">{{ ucfirst($item->status_denda ?? 'belum') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data pengembalian</td>
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
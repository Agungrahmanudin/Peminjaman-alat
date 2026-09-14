<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengembalian Alat</title>
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
            background-color: #FF9800;
            color: white;
            padding: 10px;
            text-align: center;
            font-size: 12px;
        }
        table.data-table td {
            border: 1px solid #ddd;
            padding: 8px;
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
        .badge-success {
            background-color: #d4edda;
            color: #155724;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PENGEMBALIAN ALAT</h1>
        <p>Periode: {{ request('start_date', 'Semua') }} - {{ request('end_date', 'Semua') }}</p>
        <p>Tanggal Cetak: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="info-section">
        <table>
            <tr>
                <td width="150px"><strong>Total Pengembalian</strong></td>
                <td>: {{ $totalPengembalian ?? 0 }}</td>
                <td width="150px"><strong>Tepat Waktu</strong></td>
                <td>: {{ $tepatWaktu ?? 0 }}</td>
            </tr>
            <tr>
                <td><strong>Terlambat</strong></td>
                <td>: {{ $terlambat ?? 0 }}</td>
                <td><strong>Kondisi Baik</strong></td>
                <td>: {{ $kondisiBaik ?? 0 }}</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>ID Pinjam</th>
                <th>Peminjam</th>
                <th>Alat</th>
                <th>Tgl Kembali</th>
                <th>Status</th>
                <th>Denda</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengembalian as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->peminjaman_id }}</td>
                    <td>{{ $item->peminjaman->user->name ?? '-' }}</td>
                    <td>
                        @foreach($item->peminjaman->detailPeminjaman as $detail)
                            - {{ $detail->alat->nama_alat ?? '-' }} ({{ $detail->jumlah_pinjam }} pcs)<br>
                        @endforeach
                    </td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_kembali_realisasi)->format('d/m/Y') }}</td>
                    <td class="text-center">
                        @if($item->peminjaman && $item->tanggal_kembali_realisasi <= $item->peminjaman->tanggal_kembali_rencana)
                            <span class="badge-success">Tepat Waktu</span>
                        @else
                            <span class="badge-danger">Terlambat</span>
                        @endif
                    </td>
                    <td class="text-right">Rp {{ number_format($item->denda, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data pengembalian</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh: {{ Auth::user()->name ?? 'Sistem' }}</p>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Alat</title>
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
            background-color: #2196F3;
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
        }
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA ALAT</h1>
        <p>Tanggal Cetak: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="info-section">
        <table>
            <tr>
                <td width="150px"><strong>Total Alat</strong></td>
                <td>: {{ $totalAlat ?? 0 }}</td>
                <td width="150px"><strong>Alat Tersedia</strong></td>
                <td>: {{ $alatTersedia ?? 0 }}</td>
            </tr>
            <tr>
                <td><strong>Alat Dipinjam</strong></td>
                <td>: {{ $alatDipinjam ?? 0 }}</td>
                <td><strong>Alat Rusak</strong></td>
                <td>: {{ $alatRusak ?? 0 }}</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Alat</th>
                <th>Kategori</th>
                <th>Kondisi</th>
                <th>Total Stok</th>
                <th>Tersedia</th>
                <th>Lokasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($alat as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->kode_alat }}</td>
                    <td>{{ $item->nama_alat }}</td>
                    <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                    <td class="text-center">
                        @php
                            $kondisiClass = match($item->kondisi) {
                                'baik' => 'badge-success',
                                'rusak' => 'badge-danger',
                                'perbaikan' => 'badge-warning',
                                default => 'badge-info'
                            };
                        @endphp
                        <span class="badge {{ $kondisiClass }}">{{ ucfirst($item->kondisi) }}</span>
                    </td>
                    <td class="text-center">{{ $item->jumlah_total }}</td>
                    <td class="text-center">{{ $item->jumlah_tersedia }}</td>
                    <td>{{ $item->lokasi }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data alat</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh: {{ Auth::user()->name ?? 'Sistem' }}</p>
    </div>
</body>
</html>
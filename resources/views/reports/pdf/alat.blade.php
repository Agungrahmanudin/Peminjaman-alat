<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Alat</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .summary { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2>Laporan Data Alat</h2>
    <div class="summary">
        <p><strong>Total Alat:</strong> {{ $totalAlat }}</p>
        <p><strong>Tersedia:</strong> {{ $alatTersedia }}</p>
        <p><strong>Dipinjam:</strong> {{ $alatDipinjam }}</p>
        <p><strong>Rusak:</strong> {{ $alatRusak }}</p>
    </div>
    <table>
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
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->kode_alat }}</td>
                    <td>{{ $item->nama_alat }}</td>
                    <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                    <td>{{ ucfirst($item->kondisi) }}</td>
                    <td>{{ $item->jumlah_total }}</td>
                    <td>{{ $item->jumlah_tersedia }}</td>
                    <td>{{ $item->lokasi }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
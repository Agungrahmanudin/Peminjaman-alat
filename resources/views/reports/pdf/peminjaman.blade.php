<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Peminjaman</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .summary { margin-bottom: 20px; }
        .summary p { margin: 2px 0; }
    </style>
</head>
<body>
    <h2>Laporan Peminjaman</h2>
    <div class="summary">
        <p><strong>Total Peminjaman:</strong> {{ $totalPeminjaman }}</p>
        <p><strong>Aktif:</strong> {{ $peminjamanAktif }}</p>
        <p><strong>Dikembalikan:</strong> {{ $peminjamanDikembalikan }}</p>
        <p><strong>Terlambat:</strong> {{ $peminjamanTerlambat }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>Peminjam</th>
                <th>Alat</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Rencana</th>
                <th>Tanggal Kembali</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($peminjaman as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->user->name ?? '-' }}</td>
                    <td>
                        @foreach ($item->detailPeminjaman as $detail)
                            {{ $detail->alat->nama_alat ?? '-' }} ({{ $detail->jumlah_pinjam }} pcs)<br>
                        @endforeach
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('d/m/Y') }}</td>
                    <td>{{ $item->tanggal_kembali_realisasi ? \Carbon\Carbon::parse($item->tanggal_kembali_realisasi)->format('d/m/Y') : '-' }}</td>
                    <td>{{ ucfirst($item->status) }}</td>
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
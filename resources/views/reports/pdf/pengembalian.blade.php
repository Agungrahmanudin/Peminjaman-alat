<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengembalian</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .summary { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2>Laporan Pengembalian</h2>
    <div class="summary">
        <p><strong>Total Pengembalian:</strong> {{ $totalPengembalian }}</p>
        <p><strong>Tepat Waktu:</strong> {{ $tepatWaktu }}</p>
        <p><strong>Terlambat:</strong> {{ $terlambat }}</p>
        <p><strong>Kondisi Baik:</strong> {{ $kondisiBaik }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID Pinjam</th>
                <th>Peminjam</th>
                <th>Alat</th>
                <th>Tanggal Kembali</th>
                <th>Status</th>
                <th>Denda</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengembalian as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->peminjaman_id }}</td>
                    <td>{{ $item->peminjaman->user->name ?? '-' }}</td>
                    <td>
                        @foreach ($item->peminjaman->detailPeminjaman as $detail)
                            {{ $detail->alat->nama_alat ?? '-' }} ({{ $detail->jumlah_pinjam }} pcs)<br>
                        @endforeach
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali_realisasi)->format('d/m/Y') }}</td>
                    <td>
                        @if ($item->peminjaman && $item->tanggal_kembali_realisasi <= $item->peminjaman->tanggal_kembali_rencana)
                            Tepat Waktu
                        @else
                            Terlambat
                        @endif
                    </td>
                    <td>Rp {{ number_format($item->denda, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
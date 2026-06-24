<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #222;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .lunas { background-color: #d1fae5; color: #065f46; }
        .pending { background-color: #fef3c7; color: #92400e; }
        ul { margin: 0; padding-left: 15px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Seluruh Transaksi</h1>
        <p>Dicetak pada: {{ now()->format('d M Y, H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tanggal</th>
                <th>Kasir</th>
                <th>Metode</th>
                <th class="text-center">Status</th>
                <th class="text-right">Total</th>
                <th>Detail Item</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $t)
            <tr>
                <td>{{ $t->formatted_id ?? $t->id }}</td>
                <td>{{ \Carbon\Carbon::parse($t->transaction_date)->format('d/m/Y H:i') }}</td>
                <td>{{ $t->cashier->name ?? '-' }}</td>
                <td>{{ strtoupper($t->payment_method) }}</td>
                <td class="text-center">
                    <span class="status-badge {{ $t->status === 'lunas' ? 'lunas' : 'pending' }}">
                        {{ strtoupper($t->status) }}
                    </span>
                </td>
                <td class="text-right font-bold">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                <td>
                    <ul>
                        @foreach($t->items as $item)
                            <li>{{ $item->product->name ?? 'Produk' }} (x{{ $item->qty }})</li>
                        @endforeach
                    </ul>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px;">Belum ada data transaksi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>

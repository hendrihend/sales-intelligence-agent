<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - Sales Intelligence</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page { size: landscape; margin: 1cm; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; background-color: white !important; }
            .no-print { display: none !important; }
            .shadow-sm { box-shadow: none !important; }
            .border { border: none !important; }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans p-8">
    <div class="max-w-7xl mx-auto bg-white p-8 rounded-lg shadow-sm border border-gray-100">
        <div class="flex justify-between items-end mb-8 border-b pb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Laporan Seluruh Transaksi</h1>
                <p class="text-gray-500 mt-1">Dicetak pada: {{ now()->format('d M Y, H:i') }}</p>
            </div>
            <button onclick="window.print()" class="no-print bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2.5 rounded-lg font-semibold shadow-md transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                Cetak ke PDF
            </button>
        </div>

        <table class="w-full text-sm text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-600 uppercase text-xs tracking-wider">
                    <th class="p-3 border-b">ID</th>
                    <th class="p-3 border-b">Tanggal</th>
                    <th class="p-3 border-b">Kasir</th>
                    <th class="p-3 border-b">Metode</th>
                    <th class="p-3 border-b text-center">Status</th>
                    <th class="p-3 border-b text-right">Total</th>
                    <th class="p-3 border-b w-1/3">Detail Item</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transactions as $t)
                <tr class="hover:bg-gray-50">
                    <td class="p-3 font-medium text-gray-900">{{ $t->formatted_id ?? $t->id }}</td>
                    <td class="p-3 text-gray-600">{{ \Carbon\Carbon::parse($t->transaction_date)->format('d/m/Y H:i') }}</td>
                    <td class="p-3 text-gray-600">{{ $t->cashier->name ?? '-' }}</td>
                    <td class="p-3 text-gray-600 uppercase text-xs">{{ $t->payment_method }}</td>
                    <td class="p-3 text-center">
                        <span class="px-2 py-1 rounded text-xs font-semibold 
                            {{ $t->status === 'lunas' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ strtoupper($t->status) }}
                        </span>
                    </td>
                    <td class="p-3 text-right font-bold text-gray-900">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                    <td class="p-3 text-gray-500 text-xs">
                        <ul class="list-disc pl-4 space-y-1">
                            @foreach($t->items as $item)
                                <li>{{ $item->product->name ?? 'Produk' }} (x{{ $item->qty }})</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-8 text-center text-gray-500">Belum ada data transaksi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <script>
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>

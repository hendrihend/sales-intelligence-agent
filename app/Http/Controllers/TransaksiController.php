<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TransaksiController extends Controller
{
    public function index()
    {
        // Ambil data diskon aktif dari AI
        $activeDiscounts = \App\Models\DiscountRecommendation::with('deadstockAnalysis')
            ->get()
            ->mapWithKeys(function ($rec) {
                if ($rec->deadstockAnalysis && $rec->deadstockAnalysis->product_id) {
                    return [$rec->deadstockAnalysis->product_id => $rec->discount_percent];
                }
                return [];
            });

        // Ambil produk yang stoknya masih ada
        $products = Product::where('stock', '>', 0)->get()->map(function ($product) use ($activeDiscounts) {
            
            // Map gambar dummy berdasarkan kategori
            $imageMap = [
                'Processor'   => 'https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?w=300&h=300&fit=crop',
                'VGA'         => 'https://images.unsplash.com/photo-1591488320449-011701bb6704?w=300&h=300&fit=crop',
                'Motherboard' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=300&h=300&fit=crop',
                'RAM'         => 'https://images.unsplash.com/photo-1563920443079-783e5c786bce?w=300&h=300&fit=crop',
                'SSD'         => 'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?w=300&h=300&fit=crop',
                'PSU'         => 'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?w=300&h=300&fit=crop',
                'Casing'      => 'https://images.unsplash.com/photo-1555680202-c86f0e12f086?w=300&h=300&fit=crop',
                'Cooler'      => 'https://images.unsplash.com/photo-1587829191301-32b86b2b94f5?w=300&h=300&fit=crop',
                'Aksesoris'   => 'https://images.unsplash.com/photo-1527814050087-3793815479db?w=300&h=300&fit=crop',
            ];

            $discount = $activeDiscounts->get($product->id, 0);
            $finalPrice = (float) $product->price;
            if ($discount > 0) {
                $finalPrice = $finalPrice - ($finalPrice * $discount / 100);
            }

            // Gunakan gambar dari database jika ada, jika tidak gunakan dummy map
            $productImage = $product->image 
                ? '/images/products/' . $product->image 
                : ($imageMap[$product->category] ?? 'https://images.unsplash.com/photo-1587829191301-32b86b2b94f5?w=300&h=300&fit=crop');

            return [
                'id'       => $product->id,
                'name'     => $product->name,
                'category' => $product->category,
                'price'    => $finalPrice,
                'original_price' => (float) $product->price,
                'image'    => $productImage,
                'desc'     => 'Komponen ' . $product->category . ' berkualitas tinggi.',
                'seller'   => 'Toko Pusat',
                'rating'   => rand(45, 50) / 10, // dummy rating 4.5 - 5.0
                'reviews'  => rand(10, 500),     // dummy reviews count
                'discount' => $discount,
                'is_ai_recommended' => $discount > 0 ? 1 : 0,
            ];
        })->sortByDesc('is_ai_recommended')->values();

        // 5 transaksi terakhir untuk Activities Section
        $recentTransactions = Transaction::with(['items.product', 'cashier'])
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return Inertia::render('Transaksi', [
            'products' => $products,
            'recentTransactions' => $recentTransactions,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'nullable|string',
            'payment_method' => 'required|string|in:qris,kasir',
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
        ]);

        $total = 0;
        foreach ($request->items as $item) {
            $total += $item['price'] * $item['qty'];
        }

        $paymentMethod = $request->payment_method === 'qris' ? 'qris' : 'unpaid';
        $status = $request->payment_method === 'qris' ? 'lunas' : 'pending';

        // Simpan ke tabel transactions (cashier_id = 3 adalah dummy "Kasir Toko")
        $transaction = Transaction::create([
            'cashier_id' => 3, 
            'transaction_date' => now(),
            'total' => $total,
            'payment_method' => $paymentMethod,
            'status' => $status,
        ]);

        // Simpan ke tabel transaction_items dan kurangi stok
        foreach ($request->items as $item) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $item['id'],
                'qty' => $item['qty'],
                'price' => $item['price'],
                'subtotal' => $item['price'] * $item['qty'],
            ]);

            // Kurangi stok produk
            Product::where('id', $item['id'])->decrement('stock', $item['qty']);
        }

        return redirect()->back()->with([
            'po_popup' => [
                'transaction_id' => $transaction->formatted_id,
                'total' => $total,
            ]
        ]);
    }

    public function exportExcel()
    {
        $transactions = Transaction::with(['items.product', 'cashier'])->orderByDesc('id')->get();
        
        $filename = "transactions_export_" . date('Y-m-d_H-i-s') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $columns = ['ID Transaksi', 'Tanggal', 'Kasir', 'Total (Rp)', 'Metode Pembayaran', 'Status', 'Detail Item'];

        $callback = function() use($transactions, $columns) {
            $file = fopen('php://output', 'w');
            // Tambahkan BOM untuk excel UTF-8
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns);
            
            foreach ($transactions as $t) {
                $itemsStr = $t->items->map(function($item) {
                    return ($item->product->name ?? 'Produk') . " (x{$item->qty})";
                })->implode(', ');

                $row = [
                    $t->formatted_id ?? $t->id,
                    $t->transaction_date,
                    $t->cashier->name ?? '-',
                    $t->total,
                    $t->payment_method,
                    strtoupper($t->status),
                    $itemsStr
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $transactions = Transaction::with(['items.product', 'cashier'])->orderByDesc('id')->get();
        $pdf = app('dompdf.wrapper')->loadView('print.transactions-pdf', compact('transactions'));
        return $pdf->download('laporan_transaksi.pdf');
    }
}

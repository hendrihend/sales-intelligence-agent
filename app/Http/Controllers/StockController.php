<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index()
    {
        // TAMBAHKAN BARIS INI: Menghilangkan batasan waktu 30 detik khusus untuk halaman ini
        set_time_limit(0); 

        // Ambil data produk dengan pengaman
        $productsData = DB::table('products')->get();

        if ($productsData->isEmpty()) {
            return Inertia::render('Stock/Index', [
                'products' => []
            ]);
        }

        $products = $productsData->map(function ($product) {
            $unitsSold = DB::table('transaction_items')
                ->join('transactions', 'transactions.id', '=', 'transaction_items.transaction_id')
                ->where('transaction_items.product_id', $product->id)
                ->where('transactions.transaction_date', '>=', Carbon::now()->subDays(30))
                ->sum('transaction_items.qty');

            $currentStock = isset($product->stock) ? (int)$product->stock : 0;
            $product->units_sold_30_days = (int)$unitsSold;

            $dailyVelocity = $product->units_sold_30_days / 30;

            if ($dailyVelocity > 0) {
                $product->days_of_supply = ceil($currentStock / $dailyVelocity);
            } else {
                $product->days_of_supply = $currentStock > 0 ? 999 : 0; 
            }

            if ($currentStock == 0) {
                $product->ai_status = 'Out of Stock';
                $product->ai_recommendation = null;
            } elseif ($product->days_of_supply > 90 || ($product->units_sold_30_days == 0 && $currentStock > 0)) {
                $product->ai_status = 'Deadstock';
                $product->ai_recommendation = 'Diskon Otomatis 20% di Kasir';
            } elseif ($product->days_of_supply > 45) {
                $product->ai_status = 'Slow-Moving';
                $product->ai_recommendation = 'Diskon Otomatis 10% di Kasir';
            } else {
                $product->ai_status = 'Healthy';
                $product->ai_recommendation = null;
            }

            return $product;
        });

        return Inertia::render('Stock/Index', [
            'products' => $products
        ]);
    }

    // fungsi simpan data stok baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|required|string|max:255',
            'purchase_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        // otomatisasi status berdasarkan jumlah stok
        $validated['status'] = $validated['stock'] > 0 ? 'Available' : 'Out of Stock';
        $validated['created_at'] = Carbon::now();
        $validated['updated_at'] = Carbon::now();

        DB::table('products')->insert($validated);

        return redirect()->back()->with('success', 'Produk baru berhasil ditambahkan.');
    }

    // fungsi ubah data stok
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|required|string|max:255',
            'purchase_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        // otomatisasi status berdasarkan jumlah stok
        $validated['status'] = $validated['stock'] > 0 ? 'Available' : 'Out of Stock';
        $validated['updated_at'] = Carbon::now();

        DB::table('products')->where('id', $id)->update($validated);

        return redirect()->back()->with('success', 'Produk berhasil diperbarui.');
    }

    // fungsi hapus data stok
    public function destroy($id)
    {
        DB::table('products')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Produk '. $id . ' berhasil dihapus.');
    }


}

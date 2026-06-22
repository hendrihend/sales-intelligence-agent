<?php

namespace App\Services;

use App\Models\Product;
use App\Models\DeadstockAnalysis;
use App\Models\DiscountRecommendation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesIntelligenceService
{
    /**
     * Analisis produk deadstock dan buat rekomendasi diskon otomatis.
     * Produk dianggap deadstock jika:
     * - Stok > 5
     * - Tidak ada transaksi dalam 30 hari terakhir
     */
    public function analyzeDeadstock()
    {
        $daysThreshold = 30;
        $cutOffDate = Carbon::now()->subDays($daysThreshold);
        $now = Carbon::now();

        // Ambil produk yang stok > 5 dan TIDAK ada di transaction_items
        // pada transaksi yang terjadi dalam 30 hari terakhir
        $deadstocks = Product::where('stock', '>', 5)
            ->whereNotExists(function ($query) use ($cutOffDate) {
                $query->select(DB::raw(1))
                    ->from('transaction_items')
                    ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                    ->whereRaw('transaction_items.product_id = products.id')
                    ->where('transactions.transaction_date', '>=', $cutOffDate);
            })->get();

        $results = [];

        foreach ($deadstocks as $product) {
            // Cari transaksi terakhir produk ini (kapan terakhir kali laku)
            $lastSale = DB::connection('pgsql_sales')
                ->table('transaction_items')
                ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                ->where('transaction_items.product_id', $product->id)
                ->orderByDesc('transactions.transaction_date')
                ->first();

            $lastSaleDate = $lastSale
                ? Carbon::parse($lastSale->transaction_date)
                : Carbon::parse($product->created_at);

            $daysInactive = (int) $lastSaleDate->diffInDays($now);

            // Hitung total penjualan sepanjang waktu
            $totalSales = DB::connection('pgsql_sales')
                ->table('transaction_items')
                ->where('product_id', $product->id)
                ->sum('qty');

            // Simpan/update DeadstockAnalysis
            $analysis = DeadstockAnalysis::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'last_sale_date' => $lastSale ? $lastSale->transaction_date : null,
                    'total_sales' => (int) $totalSales,
                    'stock_remaining' => $product->stock,
                    'status' => 'deadstock',
                    'analysis_date' => $now,
                ]
            );

            // Logika penentuan diskon berdasarkan lama tidak laku
            if ($daysInactive >= 60) {
                $discountPercent = 30;
                $note = "Produk tidak laku selama {$daysInactive} hari. Diskon agresif 30% disarankan untuk menggerakkan stok.";
            } elseif ($daysInactive >= 45) {
                $discountPercent = 20;
                $note = "Produk tidak laku selama {$daysInactive} hari. Diskon moderat 20% disarankan.";
            } else {
                $discountPercent = 10;
                $note = "Produk tidak laku selama {$daysInactive} hari. Diskon ringan 10% disarankan untuk menarik pembeli.";
            }

            // Simpan/update DiscountRecommendation
            DiscountRecommendation::updateOrCreate(
                ['deadstock_analysis_id' => $analysis->id],
                [
                    'discount_percent' => $discountPercent,
                    'recommendation_note' => $note,
                ]
            );

            $results[] = [
                'product' => $product->name,
                'days_inactive' => $daysInactive,
                'discount' => $discountPercent . '%',
            ];
        }

        return $results;
    }
}

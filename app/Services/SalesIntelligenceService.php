<?php

namespace App\Services;

use App\Models\Product;
use App\Models\AiDiscountSuggestion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesIntelligenceService
{
    public function analyzeDeadstock()
    {
        // Parameter ambang batas: Produk tidak laku dalam 30 hari terakhir & stok > 5
        $daysThreshold = 30;
        $cutOffDate = Carbon::now()->subDays($daysThreshold);

        // Ambil produk yang tidak ada di detail transaksi selama 30 hari terakhir
        $deadstocks = Product::where('stock', '>', 5)
            ->whereNotExists(function ($query) use ($cutOffDate) {
                $query->select(DB::raw(1))
                    ->from('transaction_details')
                    ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
                    ->whereRaw('transaction_details.product_id = products.id')
                    ->where('transactions.created_at', '>=', $cutOffDate);
            })->get();

        foreach ($deadstocks as $product) {
            // Hitung sudah berapa lama mengendap secara teoritis (menggunakan transaksi terakhir yang tercatat jika ada)
            $lastSales = DB::table('transaction_details')
                ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
                ->where('transaction_details.product_id', $product->id)
                ->orderBy('transactions.created_at', 'desc')
                ->first();

            $daysInactive = $lastSales ? Carbon::parse($lastSales->created_at)->diffInDays(Carbon::now()) : $daysThreshold;

            // Logika Penentuan Diskon Pintar (Sederhana Berbasis Aturan)
            // Makin lama mengendap, diskon naik bertahap, max memotong 50% dari margin profit
            $profitMargin = $product->selling_price - $product->cost_price;
            if ($profitMargin <= 0) continue;

            if ($daysInactive >= 60) {
                $discountPercent = 30; // Diskon 30% jika > 60 hari mampet
            } elseif ($daysInactive >= 45) {
                $discountPercent = 20;
            } else {
                $discountPercent = 10;
            }

            // Validasi pelindung: Pastikan harga setelah diskon tidak merugi di bawah modal
            $discountAmount = ($product->selling_price * $discountPercent) / 100;
            if (($product->selling_price - $discountAmount) < $product->cost_price) {
                // Jika rugi, set diskon maksimal agar pas dengan harga modal
                $maxSafeDiscount = (($product->selling_price - $product->cost_price) / $product->selling_price) * 100;
                $discountPercent = floor($maxSafeDiscount);
            }

            if ($discountPercent > 0) {
                // Simpan atau update saran diskon otomatis ke database
                AiDiscountSuggestion::updateOrCreate(
                    ['product_id' => $product->id, 'status' => 'pending'],
                    [
                        'days_inactive' => $daysInactive,
                        'recommended_discount' => $discountPercent,
                        'expires_at' => Carbon::now()->addDays(7)
                    ]
                );
            }
        }
    }
}

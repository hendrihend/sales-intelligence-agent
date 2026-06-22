<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;

class StockController extends Controller
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

        // Ambil data produk dari PostgreSQL database
        $products = Product::orderBy('name')->get()->map(function ($product) use ($activeDiscounts) {
            
            // Tentukan status berdasarkan stok
            $status = 'available';
            if ($product->stock <= 0) {
                $status = 'empty';
            } elseif ($product->stock < 10) {
                $status = 'low';
            }

            // Cek apakah ada diskon AI untuk produk ini
            $discountPercent = $activeDiscounts->get($product->id, 0);
            $finalPrice = (float) $product->price;
            if ($discountPercent > 0) {
                $finalPrice = $finalPrice - ($finalPrice * $discountPercent / 100);
            }

            // Generate SKU sederhana berdasarkan ID
            $sku = 'PRD-' . str_pad($product->id, 3, '0', STR_PAD_LEFT);

            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $sku,
                'category' => $product->category,
                'qty' => $product->stock,
                'minQty' => 10, // Minimal stok default
                'price' => (float) $product->price,
                'finalPrice' => $finalPrice,
                'discountPercent' => $discountPercent,
                'status' => $status,
            ];
        });

        return Inertia::render('Stock', [
            'stockItems' => $products
        ]);
    }
}

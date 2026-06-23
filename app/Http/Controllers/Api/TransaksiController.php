<!-- <?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
// Contoh di dalam TransactionController.php saat produk dimasukkan ke keranjang
class TransaksiController extends Controller
{
    public function index()
    {
        // Ambil semua produk beserta kalkulasi dari tabel transaction_details & transactions Anda
        $products = Product::select('products.*')
            ->addSelect([
                // SESUAI DATABASE: Menghitung total quantity dari tabel transaction_details
                'units_sold_30_days' => DB::table('transaction_details')
                    ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
                    ->whereColumn('transaction_details.product_id', 'products.id')
                    ->where('transactions.created_at', '>=', Carbon::now()->subDays(30))
                    ->selectRaw('COALESCE(SUM(transaction_details.quantity), 0)')
            ])
            ->get()
            ->map(function ($product) {
                // 1. Hitung Kecepatan Jual Harian (Sales Velocity)
                $dailyVelocity = $product->units_sold_30_days / 30;

                // 2. Hitung Estimasi Sisa Hari Stok (Days of Supply) berdasarkan kolom 'stock' Anda
                if ($dailyVelocity > 0) {
                    $product->days_of_supply = ceil($product->stock / $dailyVelocity);
                } else {
                    // Jika tidak ada penjualan dalam 30 hari terakhir
                    $product->days_of_supply = $product->stock > 0 ? 999 : 0; 
                }

                return $product;
            });

        return Inertia::render('Transaksi', [
            'products' => $products
        ]); 
    }
} -->
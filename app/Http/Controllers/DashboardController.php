<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\DeadstockAnalysis;
use App\Models\DiscountRecommendation;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();

        // ============================
        // SUMMARY CARDS
        // ============================

        // Total pendapatan bulan ini
        $totalRevenue = Transaction::where('transaction_date', '>=', $startOfMonth)
            ->sum('total');

        // Total pendapatan bulan lalu (untuk perbandingan)
        $lastMonthRevenue = Transaction::whereBetween('transaction_date', [$lastMonthStart, $lastMonthEnd])
            ->sum('total');

        // Persentase perubahan pendapatan
        $revenueChange = $lastMonthRevenue > 0
            ? round((($totalRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        // Total transaksi bulan ini
        $totalTransactions = Transaction::where('transaction_date', '>=', $startOfMonth)
            ->count();

        $lastMonthTransactions = Transaction::whereBetween('transaction_date', [$lastMonthStart, $lastMonthEnd])
            ->count();

        $transactionChange = $lastMonthTransactions > 0
            ? round((($totalTransactions - $lastMonthTransactions) / $lastMonthTransactions) * 100, 1)
            : 0;

        // Total produk aktif
        $totalProducts = Product::count();

        // Produk dengan stok rendah (< 10)
        $lowStockCount = Product::where('stock', '<', 10)->where('stock', '>', 0)->count();

        // Produk habis stok
        $outOfStockCount = Product::where('stock', '<=', 0)->count();

        // ============================
        // LINE CHART: Pendapatan Bulanan
        // ============================

        $requestedYear = request('year', $now->year);
        $currentYear = (int) $requestedYear;
        $lastYear = $currentYear - 1;

        $monthlyRevenueCurrentYear = Transaction::selectRaw(
                "EXTRACT(MONTH FROM transaction_date) as month, SUM(total) as total"
            )
            ->whereRaw("EXTRACT(YEAR FROM transaction_date) = ?", [$currentYear])
            ->groupByRaw("EXTRACT(MONTH FROM transaction_date)")
            ->orderByRaw("EXTRACT(MONTH FROM transaction_date)")
            ->pluck('total', 'month')
            ->toArray();

        $monthlyRevenueLastYear = Transaction::selectRaw(
                "EXTRACT(MONTH FROM transaction_date) as month, SUM(total) as total"
            )
            ->whereRaw("EXTRACT(YEAR FROM transaction_date) = ?", [$lastYear])
            ->groupByRaw("EXTRACT(MONTH FROM transaction_date)")
            ->orderByRaw("EXTRACT(MONTH FROM transaction_date)")
            ->pluck('total', 'month')
            ->toArray();

        // Fill all 12 months with 0 if no data
        $currentYearData = [];
        $lastYearData = [];
        for ($i = 1; $i <= 12; $i++) {
            $currentYearData[] = (float) ($monthlyRevenueCurrentYear[$i] ?? 0);
            $lastYearData[] = (float) ($monthlyRevenueLastYear[$i] ?? 0);
        }

        // ============================
        // BAR CHART: Penjualan per Kategori
        // ============================

        $categorySalesCurrentMonth = DB::connection('pgsql_sales')
            ->table('transaction_items as ti')
            ->join('products as p', 'ti.product_id', '=', 'p.id')
            ->join('transactions as t', 'ti.transaction_id', '=', 't.id')
            ->where('t.transaction_date', '>=', $startOfMonth)
            ->selectRaw("COALESCE(p.category, 'Lainnya') as category, COUNT(*) as count")
            ->groupBy('p.category')
            ->orderByDesc('count')
            ->get();

        $categorySalesLastMonth = DB::connection('pgsql_sales')
            ->table('transaction_items as ti')
            ->join('products as p', 'ti.product_id', '=', 'p.id')
            ->join('transactions as t', 'ti.transaction_id', '=', 't.id')
            ->whereBetween('t.transaction_date', [$lastMonthStart, $lastMonthEnd])
            ->selectRaw("COALESCE(p.category, 'Lainnya') as category, COUNT(*) as count")
            ->groupBy('p.category')
            ->orderByDesc('count')
            ->get();

        // Collect all unique categories
        $allCategories = $categorySalesCurrentMonth->pluck('category')
            ->merge($categorySalesLastMonth->pluck('category'))
            ->unique()
            ->values()
            ->toArray();

        $currentMonthCategoryData = [];
        $lastMonthCategoryData = [];
        foreach ($allCategories as $cat) {
            $currentMonthCategoryData[] = (int) ($categorySalesCurrentMonth->firstWhere('category', $cat)->count ?? 0);
            $lastMonthCategoryData[] = (int) ($categorySalesLastMonth->firstWhere('category', $cat)->count ?? 0);
        }

        // ============================
        // DOUGHNUT CHART: Metode Pembayaran
        // ============================

        $paymentMethodsCurrentMonth = Transaction::where('transaction_date', '>=', $startOfMonth)
            ->selectRaw("COALESCE(payment_method, 'Belum Bayar') as method, COUNT(*) as count")
            ->groupBy('payment_method')
            ->orderByDesc('count')
            ->get();

        $allMethods = $paymentMethodsCurrentMonth->pluck('method')->toArray();
        $currentMonthMethodData = [];
        foreach ($allMethods as $method) {
            $currentMonthMethodData[] = (int) ($paymentMethodsCurrentMonth->firstWhere('method', $method)->count ?? 0);
        }

        // ============================
        // TOP PRODUCTS
        // ============================

        $topProducts = DB::connection('pgsql_sales')
            ->table('transaction_items as ti')
            ->join('products as p', 'ti.product_id', '=', 'p.id')
            ->selectRaw("p.name, SUM(ti.qty) as total_sold, SUM(ti.subtotal) as total_revenue")
            ->groupBy('p.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get()
            ->map(function ($item, $index) {
                $maxSold = 1; // prevent division by zero
                return [
                    'name' => $item->name,
                    'sold' => (int) $item->total_sold,
                    'revenue' => 'Rp ' . number_format($item->total_revenue, 0, ',', '.'),
                    'progress' => min(100, (int) $item->total_sold), // will normalize on frontend
                ];
            })
            ->toArray();

        // Normalize progress relative to the top seller
        if (count($topProducts) > 0) {
            $maxSold = $topProducts[0]['sold'];
            foreach ($topProducts as &$product) {
                $product['progress'] = $maxSold > 0 ? round(($product['sold'] / $maxSold) * 100) : 0;
            }
        }

        // ============================
        // RECENT TRANSACTIONS
        // ============================

        $recentTransactions = Transaction::orderByDesc('transaction_date')
            ->limit(5)
            ->get()
            ->map(function ($t) {
                $timeDiff = Carbon::parse($t->transaction_date)->diffForHumans();
                return [
                    'id' => $t->id,
                    'type' => 'sale',
                    'title' => 'Transaksi #' . $t->id,
                    'amount' => 'Rp ' . number_format($t->total, 0, ',', '.'),
                    'time' => $timeDiff,
                    'status' => 'success',
                ];
            })
            ->toArray();

        // ============================
        // DEADSTOCK INFO
        // ============================

        $deadstockCount = DeadstockAnalysis::where('status', 'deadstock')->count();

        // ============================
        // AI DISCOUNT SUGGESTIONS
        // ============================

        $discountSuggestions = DiscountRecommendation::with(['deadstockAnalysis.product'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($rec) {
                $analysis = $rec->deadstockAnalysis;
                $product = $analysis?->product;

                if (!$product) return null;

                $daysInactive = $analysis->last_sale_date
                    ? (int) Carbon::parse($analysis->last_sale_date)->diffInDays(Carbon::now())
                    : null;

                $originalPrice = (float) $product->price;
                $discountedPrice = $originalPrice - ($originalPrice * $rec->discount_percent / 100);

                return [
                    'id' => $rec->id,
                    'product_name' => $product->name,
                    'category' => $product->category ?? 'Lainnya',
                    'stock_remaining' => $analysis->stock_remaining,
                    'days_inactive' => $daysInactive,
                    'discount_percent' => $rec->discount_percent,
                    'original_price' => 'Rp ' . number_format($originalPrice, 0, ',', '.'),
                    'discounted_price' => 'Rp ' . number_format($discountedPrice, 0, ',', '.'),
                    'note' => $rec->recommendation_note,
                    'status' => $analysis->status,
                ];
            })
            ->filter()
            ->values()
            ->toArray();

        // ============================
        // SEND TO FRONTEND
        // ============================

        return Inertia::render('Dashboard', [
            'filters' => ['year' => $currentYear],
            'summaryCards' => [
                [
                    'label' => 'Total Pendapatan',
                    'value' => 'Rp ' . number_format($totalRevenue, 0, ',', '.'),
                    'change' => ($revenueChange >= 0 ? '+' : '') . $revenueChange . '%',
                    'changeType' => $revenueChange >= 0 ? 'up' : 'down',
                    'icon' => 'revenue',
                    'color' => 'emerald',
                ],
                [
                    'label' => 'Transaksi Bulan Ini',
                    'value' => (string) $totalTransactions,
                    'change' => ($transactionChange >= 0 ? '+' : '') . $transactionChange . '%',
                    'changeType' => $transactionChange >= 0 ? 'up' : 'down',
                    'icon' => 'transaction',
                    'color' => 'blue',
                ],
                [
                    'label' => 'Total Produk',
                    'value' => (string) $totalProducts,
                    'change' => $lowStockCount . ' stok rendah',
                    'changeType' => $lowStockCount > 0 ? 'down' : 'up',
                    'icon' => 'customer',
                    'color' => 'violet',
                ],
                [
                    'label' => 'Stok Habis',
                    'value' => (string) $outOfStockCount,
                    'change' => $deadstockCount . ' deadstock',
                    'changeType' => $outOfStockCount > 0 ? 'down' : 'up',
                    'icon' => 'efficiency',
                    'color' => 'amber',
                ],
            ],
            'revenueChart' => [
                'currentYear' => $currentYearData,
                'lastYear' => $lastYearData,
                'currentYearLabel' => "Pendapatan $currentYear",
                'lastYearLabel' => "Pendapatan $lastYear",
            ],
            'categoryChart' => [
                'labels' => $allCategories,
                'currentMonth' => $currentMonthCategoryData,
                'lastMonth' => $lastMonthCategoryData,
            ],
            'paymentChart' => [
                'labels' => $allMethods,
                'currentMonth' => $currentMonthMethodData,
            ],
            'topProducts' => $topProducts,
            'recentTransactions' => $recentTransactions,
            'discountSuggestions' => $discountSuggestions,
        ]);
    }
}

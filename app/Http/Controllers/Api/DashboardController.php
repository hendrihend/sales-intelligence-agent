<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\AiDiscountSuggestion;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->user()->role;
        $data = [];

        if ($role === 'manager' || $role === 'admin') {
            $data['summary'] = [
                'total_sales_today' => Transaction::whereDate('created_at', Carbon::today())->sum('total_final'),
                'total_products' => Product::count(),
                'deadstock_alert_count' => AiDiscountSuggestion::where('status', 'pending')->count()
            ];
            
            // Grafik Penjualan 7 Hari Terakhir
            $data['sales_chart'] = Transaction::selectRaw('DATE(created_at) as date, SUM(total_final) as total')
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->groupBy('date')
                ->get();
        }

        if ($role === 'manager') {
            // Tampilkan list rekomendasi diskon AI yang butuh approval manager
            $data['ai_suggestions'] = AiDiscountSuggestion::with('product')
                ->where('status', 'pending')
                ->get();
        }

        if ($role === 'kasir') {
            // Tampilan ringkas performa shift kasir hari ini
            $data['summary'] = [
                'my_transactions_count' => Transaction::where('user_id', $request->user()->id)
                    ->whereDate('created_at', Carbon::today())->count(),
                'my_sales_total' => Transaction::where('user_id', $request->user()->id)
                    ->whereDate('created_at', Carbon::today())->sum('total_final'),
            ];
        }

        return response()->json($data);
    }
}
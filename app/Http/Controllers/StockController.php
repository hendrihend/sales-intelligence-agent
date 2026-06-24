<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;

class StockController extends Controller
{
    public function index()
    {
        // Ambil data produk
        $products = Product::orderBy('name')->get()->map(function ($product) {
            
            // Hitung unit terjual 30 hari terakhir
            $unitsSold = \App\Models\TransactionItem::where('product_id', $product->id)
                ->whereHas('transaction', function($query) {
                    $query->where('transaction_date', '>=', now()->subDays(30));
                })->sum('qty');

            // Ambil status AI dari rekomendasi jika ada
            $aiStatus = 'Available';
            $deadstock = \App\Models\DeadstockAnalysis::where('product_id', $product->id)->first();
            if ($deadstock) {
                // Sederhanakan: Jika ada rekomendasi diskon -> Deadstock, jika hari tidak aktif besar tapi blm direkomendasikan -> Slow-Moving
                $hasDiscount = \App\Models\DiscountRecommendation::where('deadstock_analysis_id', $deadstock->id)->exists();
                if ($hasDiscount) {
                    $aiStatus = 'Deadstock';
                } elseif ($deadstock->days_inactive > 15) {
                    $aiStatus = 'Slow-Moving';
                }
            } elseif ($unitsSold > 20) {
                $aiStatus = 'Fast-Moving';
            }

            return [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category,
                'stock' => $product->stock,
                'purchase_price' => (float) $product->purchase_price,
                'price' => (float) $product->price,
                'image' => $product->image,
                'ai_status' => $aiStatus,
                'units_sold_30_days' => (int) $unitsSold,
            ];
        });

        return Inertia::render('Stock', [
            'products' => $products
        ]);
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'purchase_price' => 'required|numeric|min:0|max:1000000000000',
            'price' => 'required|numeric|min:0|max:1000000000000',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Handle file upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/products'), $filename);
            $validated['image'] = $filename;
        } else {
            unset($validated['image']);
        }

        Product::create($validated);

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'purchase_price' => 'required|numeric|min:0|max:1000000000000',
            'price' => 'required|numeric|min:0|max:1000000000000',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Handle file upload
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($product->image && file_exists(public_path('images/products/' . $product->image))) {
                unlink(public_path('images/products/' . $product->image));
            }
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/products'), $filename);
            $validated['image'] = $filename;
        } else {
            unset($validated['image']); // Jangan timpa gambar lama jika tidak upload baru
        }

        $product->update($validated);

        return redirect()->back()->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->back()->with('success', 'Produk berhasil dihapus.');
    }
}

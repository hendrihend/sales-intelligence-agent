<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KasirController extends Controller
{
    public function index(Request $request)
    {
        $transactionId = $request->input('search_id');
        $transaction = null;

        if ($transactionId) {
            // parse TRX-IN001 back to integer using regex to remove non-numeric chars
            $parsedId = preg_replace('/[^0-9]/', '', $transactionId);
            
            // Cari transaksi berdasarkan ID, muat relasi items (beserta data produknya) dan data kasir
            $transaction = Transaction::with(['items.product', 'cashier'])
                ->find($parsedId);
        }

        return Inertia::render('Kasir', [
            'transaction' => $transaction,
            'searchQuery' => $transactionId
        ]);
    }

    public function updatePayment(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|string|in:cash,debit'
        ]);

        $transaction = Transaction::findOrFail($id);
        
        if ($transaction->status === 'lunas') {
            return response()->json(['message' => 'Transaksi sudah lunas sebelumnya.'], 400);
        }

        $transaction->update([
            'payment_method' => $request->payment_method,
            'status' => 'lunas'
        ]);

        // Karena kita menggunakan Inertia, kita bisa return back agar datanya otomatis di-refresh
        return redirect()->back();
    }
}

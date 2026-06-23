<?php


use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\StockController;

Route::get('/', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

// route tampil data stok
Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');

// route crud produk
Route::post('/stocks', [StockController::class, 'store'])->name('stocks.store');
Route::put('/stocks/{id}', [StockController::class, 'update'])->name('stocks.update');
Route::delete('/stocks/{id}', [StockController::class, 'destroy'])->name('stocks.destroy');

// Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');

<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\KasirController;

Route::middleware(['auth', \App\Http\Middleware\GuestAccess::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/stock', [StockController::class, 'index'])->name('stock');
    Route::post('/stock', [StockController::class, 'store'])->name('stocks.store');
    Route::put('/stock/{id}', [StockController::class, 'update'])->name('stocks.update');
    Route::post('/stock/{id}', [StockController::class, 'update'])->name('stocks.update.post');
    Route::delete('/stock/{id}', [StockController::class, 'destroy'])->name('stocks.destroy');
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi');
    Route::post('/transaksi', [TransaksiController::class, 'store']);
    Route::get('/transaksi/export/excel', [TransaksiController::class, 'exportExcel'])->name('transaksi.export.excel');
    Route::get('/transaksi/export/pdf', [TransaksiController::class, 'exportPdf'])->name('transaksi.export.pdf');
    Route::get('/kasir', [KasirController::class, 'index'])->name('kasir');
    Route::post('/kasir/{id}/pay', [KasirController::class, 'updatePayment']);
});

// Login Routes
Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard', absolute: false));
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->onlyInput('email');
})->name('login.attempt');

// Profile/Logout Dummy Routes
Route::get('/profile', function() { return 'profile'; })->name('profile.edit');
Route::post('/logout', function(Request $request) { 
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login'); 
})->name('logout');

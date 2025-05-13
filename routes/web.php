<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PembayaranController;

Route::get('/', function () {
    return view('welcome');
});

// /routes/web.php



Route::get('struk/{pembayaran}', [PembayaranController::class, 'cetakStruk'])->name('struk.pembayaran');


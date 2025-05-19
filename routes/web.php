<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PembayaranController;

// Redirect root URL to admin login
Route::get('/', function () {
    return redirect('/admin/login');
});


Route::get('struk/{pembayaran}', [PembayaranController::class, 'cetakStruk'])->name('struk.pembayaran');

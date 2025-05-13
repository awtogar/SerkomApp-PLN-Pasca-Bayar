<?php
// /app/Http/Controllers/PembayaranController.php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Barryvdh\DomPDF\Facade\Pdf;

class PembayaranController extends Controller
{
    public function cetakStruk(Pembayaran $pembayaran)
    {
        $data = [
            'pembayaran' => $pembayaran,
            'pelanggan'  => $pembayaran->pelanggan,
            'tagihan'    => $pembayaran->tagihan,
            'agen'       => $pembayaran->agen,
        ];

        $pdf = Pdf::loadView('struk', $data);

        return $pdf->download("struk_pembayaran_{$pembayaran->id}.pdf");
    }
}

<?php
// /app/Http/Controllers/PembayaranController.php
namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Pelanggan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function cetakStruk(Pembayaran $pembayaran)
    {
        // Mengambil data terkait menggunakan relasi Eloquent
        $tagihan = $pembayaran->tagihan;
        $pelanggan = $pembayaran->pelanggan;
        $agen = $pembayaran->agen;
        
        // Validasi data terkait
        if (!$tagihan || !$pelanggan || !$agen) {
            abort(404, 'Data tidak ditemukan');
        }
        
        // Hitung total keseluruhan
        $totalBayar = $tagihan->total_bayar + $pembayaran->biaya_admin;
        
        // Data untuk view
        $data = [
            'pembayaran' => $pembayaran,
            'tagihan' => $tagihan,
            'pelanggan' => $pelanggan,
            'agen' => $agen,
            'totalBayar' => $totalBayar
        ];
        
        // Buat PDF dengan ukuran khusus
        $pdf = Pdf::loadView('struk', $data)
                ->setPaper([0, 0, 210, 370], 'portrait');
        
        // Return PDF untuk ditampilkan di browser
        return $pdf->stream('struk_pembayaran-' . $pelanggan->nomor_meter . '.pdf');
    }
}
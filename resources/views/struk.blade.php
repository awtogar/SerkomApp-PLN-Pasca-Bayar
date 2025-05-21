<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran Listrik</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap');
        
        @media print {
            body {
                width: 58mm; /* Lebar kertas termal standar */
                margin: 0;
                padding: 0;
            }
            .struk {
                width: 100%;
                box-shadow: none;
            }
        }
        
        body {
            font-family: 'Roboto Mono', monospace;
            font-size: 10px;
            margin: 0;
            padding: 0;
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .struk {
            width: 210px; /* B5 kertas termal width ~58mm */
            background-color: white;
            padding: 5px;
            position: relative;
        }
        
        .logo-container {
            display: flex;
            justify-content: center;
            margin-bottom: 10px;
        }
        
        .logo {
            width: 40px;
            height: 40px;
            background-color: #FFD700;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .logo .logo-items {
            width: 28px;
            height: 28px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 5px;
        }
        
        .title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 2px;
        }
        
        .date {
            font-size: 11px;
            color: #555;
        }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        
        .content {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 3px 8px;
            margin-bottom: 5px;
            font-size: 9px;
        }
        
        .label {
            font-weight: bold;
        }
        
        .value {
            text-align: right;
        }
        
        .total-row {
            font-weight: bold;
            margin-top: 5px;
        }
        
        .footer {
            text-align: center;
            margin-top: 5px;
            font-size: 9px;
        }
        
        .footer p {
            margin: 1px 0;
        }
        
        .footer-bold {
            font-weight: bold;
        }
        
        .watermark {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 18px;
            color: rgba(0,0,0,0.05);
            font-weight: bold;
            z-index: 0;
            white-space: nowrap;
        }
        
        .summary {
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="struk">
        <div class="watermark">LUNAS</div>
        
        <!-- Logo -->
        <div class="logo-container">
            <div class="logo">
                <img class="logo-items" src="{{ public_path('images/electricity.png') }}" alt="Logo">
            </div>
        </div>
        
        <!-- Header -->
        <div class="header">
            <div class="title">GARXS ELECTRIC</div>
            <div class="date">{{ now()->format('d/m/Y H:i:s') }}</div>
        </div>
        
        <div class="divider"></div>
        
        <!-- Content -->
        <div class="content">
            <div class="label">IDPEL</div>
            <div class="value">{{ $pelanggan->nomor_meter }}</div>
            
            <div class="label">NAMA</div>
            <div class="value">{{ strtoupper($pelanggan->nama_pelanggan) }}</div>
            
            <div class="label">BULAN</div>
            <div class="value">{{ strtoupper($tagihan->bulan) }} {{ $tagihan->tahun }}</div>
            
            <div class="label">AGEN</div>
            <div class="value">{{ $agen->nama_agen }}</div>
            
            <div class="label">TGL BAYAR</div>
            <div class="value">{{ $pembayaran->tanggal_pembayaran->format('d/m/Y') }}</div>
        </div>

        <div class="divider"></div>
        
        <!-- Rincian Pembayaran -->
        <div class="content summary">
            <div class="label">TAGIHAN</div>
            <div class="value">Rp {{ number_format($tagihan->total_bayar, 0, ',', '.') }}</div>
            
            <div class="label">BIAYA ADMIN</div>
            <div class="value">Rp {{ number_format($pembayaran->biaya_admin, 0, ',', '.') }}</div>
            
            <div class="divider"></div>
            
            <div class="label total-row">TOTAL BAYAR</div>
            <div class="value total-row">Rp {{ number_format($pembayaran->total_bayar + $pembayaran->biaya_admin, 0, ',', '.') }}</div>
        </div>
        
        <div class="divider"></div>
        
        <!-- Footer -->
        <div class="footer">
            <p class="footer-bold">TERIMA KASIH</p>
            <p>STRUK INI ADALAH BUKTI</p>
            <p>PEMBAYARAN YANG SAH</p>
        </div>
    </div>
</body>
</html>
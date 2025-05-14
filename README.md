
# ⚡️ Aplikasi Tagihan Listrik Pascabayar

Ini adalah aplikasi berbasis web untuk manajemen tagihan listrik pascabayar. Dibangun menggunakan Laravel 12 dan Filament 3 sebagai admin panel. Mendukung pencatatan penggunaan listrik, perhitungan tagihan, serta proses pembayaran.

## 🛠️ Tech Stack

- **Backend**: Laravel 12, Sanctum
- **Frontend**: Filament 3, TailwindCSS 4, Vite
- **PDF Export**: barryvdh/laravel-dompdf
- **Dev Tools**: Laravel Sail, Pint, PHPUnit, Pail (log viewer)

## ⚙️ Fitur Utama

- Manajemen Pelanggan & Agen
- Pencatatan Penggunaan Listrik
- Otomatisasi Tagihan per Bulan
- Proses Pembayaran Tagihan
- Laporan dalam bentuk PDF
- Otentikasi dengan Laravel Sanctum
- Logging realtime dengan Laravel Pail
- Live queue listener untuk proses async

## 🚀 Instalasi Lokal

### 1. Clone project
```bash
git clone https://github.com/awtogar/PLN-Pasca_Bayar.git
cd tagihan-listrik
```

### 2. Install dependency PHP & JS
```bash
composer install
npm install
```

### 3. Setup environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Migrasi & seeder (opsional)
```bash
php artisan migrate --seed
```

### 5. Jalankan dev server
```bash
composer run dev
```

## 📁 Struktur Utama Database

- **pelanggan**: Data pengguna listrik
- **agen**: Petugas yang memproses pembayaran
- **penggunaan**: Catatan meter awal dan akhir
- **tagihan**: Total tagihan berdasarkan penggunaan
- **pembayaran**: Riwayat pembayaran tagihan
- **tarif**: Golongan dan tarif per kWh

## 🧪 Testing
```bash
php artisan test
```

## 📄 Lisensi
MIT © 2025 Awaluddin Togar

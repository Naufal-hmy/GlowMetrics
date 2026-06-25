# GlowMetrics - Dashboard Analitik Penjualan Kosmetik

GlowMetrics adalah aplikasi dashboard analitik penjualan kosmetik ritel interaktif tahun 2025. Aplikasi ini dibangun menggunakan framework PHP **Laravel 11**, database **MySQL**, dan visualisasi chart interaktif berbasis **Chart.js**. Selain visualisasi standar, proyek ini juga mengimplementasikan algoritma data mining **K-Means Clustering** untuk melakukan segmentasi pelanggan berdasarkan data transaksi penjualan.

## Fitur Utama

- **Dashboard Analitik Interaktif**: Visualisasi Key Performance Indicators (KPI) utama seperti total pendapatan, unit terjual, rata-rata harga, dan jumlah transaksi.
- **Dynamic Multi-Select Filters**: Slicer filter interaktif menggunakan dropdown checkbox kustom dengan tampilan glassmorphic premium (Rose Gold & Dark theme) untuk menyaring data berdasarkan Brand, Jenis Produk, Negara, Saluran Penjualan, Metode Pembayaran, dan Rentang Tanggal.
- **Dynamic Bar Chart & Scatter Plot**: Diagram batang yang berubah secara dinamis (membandingkan performa antar brand jika memilih banyak brand, atau membandingkan kategori produk jika memilih satu brand khusus). Sumbu sebaran scatter plot juga dinamis mengikuti filter terpilih.
- **Segmentasi Pelanggan K-Means**: Mengelompokkan 500 transaksi secara otomatis menjadi 3 klaster (Ekonomis, Menengah, dan Premium) berdasarkan harga produk (*Price_USD*) dan volume pembelian (*Units_Sold*).
- **Unduh Laporan PDF Otomatis**: Fitur cetak laporan ringkasan eksekutif berformat PDF berdasarkan filter data yang sedang diterapkan secara langsung.

## Persyaratan Sistem

Sebelum menjalankan aplikasi, pastikan komputer Anda telah terinstal:
- XAMPP / Laragon (PHP >= 8.2 & MySQL)
- Composer (Dependency Manager untuk PHP)

## Langkah Instalasi & Penggunaan

1. **Clone Repository & Masuk ke Folder Proyek**
   ```bash
   cd "analitik data"
   ```

2. **Instalasi Dependensi PHP**
   ```bash
   composer install
   ```

3. **Konfigurasi Environment**
   Salin file `.env.example` menjadi `.env` dan konfigurasikan koneksi database Anda (default menggunakan database `makeup_sales_db`):
   ```bash
   cp .env.example .env
   ```
   Buat database bernama `makeup_sales_db` di phpMyAdmin Anda.

4. **Menjalankan Migrasi & Database Seeder**
   Impor dan bersihkan seluruh dataset transaksi dari file CSV ke database:
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Server Lokal**
   ```bash
   php artisan serve
   ```
   Buka browser dan akses alamat `http://127.0.0.1:8000`.

## Struktur Teknologi
- **Backend**: Laravel 11
- **Database**: MySQL (Eloquent ORM)
- **Frontend**: HTML5, Vanilla CSS (Glassmorphism Rose Gold), Chart.js (v4 via CDN)
- **Data Mining**: Algoritma K-Means Clustering (Pure PHP implementation)
- **Laporan PDF**: barryvdh/laravel-dompdf

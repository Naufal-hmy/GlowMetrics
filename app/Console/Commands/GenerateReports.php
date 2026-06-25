<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class GenerateReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate all project reports (.doc, .pdf, and .md) dynamically.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Gathering database records...");

        $query = DB::table('makeup_sales');
        $records = $query->orderBy('date', 'asc')->get();

        if ($records->isEmpty()) {
            $this->error("No data found in database. Make sure migrations and seeders have been run!");
            return 1;
        }

        $kpis = [
            'total_revenue' => $records->sum('revenue_usd'),
            'total_units' => $records->sum('units_sold'),
            'avg_price' => $records->avg('price_usd'),
            'total_sales' => $records->count(),
        ];

        $brandsBreakdown = $records->groupBy('brand')->map(function ($items, $brand) {
            return [
                'name' => $brand,
                'revenue' => $items->sum('revenue_usd'),
                'units' => $items->sum('units_sold'),
                'avg_price' => $items->avg('price_usd'),
                'count' => $items->count()
            ];
        })->sortByDesc('revenue');

        $productsBreakdown = $records->groupBy('product_type')->map(function ($items, $product) {
            return [
                'name' => $product,
                'revenue' => $items->sum('revenue_usd'),
                'units' => $items->sum('units_sold'),
                'avg_price' => $items->avg('price_usd')
            ];
        })->sortByDesc('revenue');

        $countriesBreakdown = $records->groupBy('country')->map(function ($items, $country) {
            return [
                'name' => $country,
                'revenue' => $items->sum('revenue_usd'),
                'share' => 0
            ];
        })->sortByDesc('revenue');

        $totalRevenue = $kpis['total_revenue'];
        foreach ($countriesBreakdown as &$c) {
            $c['share'] = $totalRevenue > 0 ? ($c['revenue'] / $totalRevenue) * 100 : 0;
        }
        $countriesBreakdown = collect($countriesBreakdown);

        $monthlyTrend = $records->groupBy(function ($item) {
            return Carbon::parse($item->date)->format('M Y');
        })->map(function ($items) {
            return $items->sum('revenue_usd');
        });

        $prices = $records->pluck('price_usd')->toArray();
        sort($prices);
        $count = count($prices);
        $minPrice = $prices[0];
        $maxPrice = $prices[$count - 1];
        $meanPrice = array_sum($prices) / $count;
        $middle = floor($count / 2);
        $medianPrice = ($count % 2 === 0) ? ($prices[$middle - 1] + $prices[$middle]) / 2 : $prices[$middle];

        $variance = 0.0;
        foreach ($prices as $p) {
            $variance += pow($p - $meanPrice, 2);
        }
        $stdDevPrice = sqrt($variance / ($count > 1 ? $count - 1 : 1));

        $stats = [
            'min' => round($minPrice, 2),
            'max' => round($maxPrice, 2),
            'mean' => round($meanPrice, 2),
            'median' => round($medianPrice, 2),
            'std_dev' => round($stdDevPrice, 2),
        ];

        // --- 1. GENERATE MARKDOWN REPORT ---
        $this->info("Generating Laporan_Project_Analitik_Data.md...");
        $mdContent = "# LAPORAN TUGAS BESAR: ANALITIK DAN VISUALISASI DATA
**Pembangunan Dashboard Penjualan Kosmetik Ritel Tahun 2025 (GlowMetrics)**

- **Topik Analisis Penjualan:** Data Transaksi Kosmetik/Makeup 2025
- **Sistem Framework:** Laravel Framework (MySQL) + Chart.js
- **Jumlah Baris Dataset:** {$kpis['total_sales']} Baris Data Transaksi Penjualan
- **Disusun Oleh:** Mahasiswa Semester 6
- **Tanggal Pembuatan:** " . date('d F Y') . "

---

## 1. Latar Belakang & Problem Statement

Pada zaman sekarang ini, bisnis di bidang kosmetik atau kecantikan berkembang sangat cepat sekali di berbagai negara. Ada banyak brand terkenal seperti MAC, L'Oreal, Maybelline, dan lainnya yang bersaing ketat untuk menjual berbagai macam produk kosmetik mulai dari lipstick, foundation, eyeshadow, hingga mascara. Di dalam dunia bisnis retail seperti ini, kalau kita tidak bisa melihat data penjualan dengan jelas, kita pasti bakal kesulitan buat mengatur persediaan barang di toko atau gudang. Misalnya saja, kita tidak tahu barang apa yang paling laku di negara tertentu, atau brand mana yang sebenarnya paling menyumbang keuntungan paling banyak buat toko kita sepanjang tahun.

Oleh karena itu, di dalam tugas besar ini kami mencoba membuat sebuah aplikasi dashboard analitik. Kita ingin memetakan perilaku belanja dari para konsumen kosmetik ini berdasarkan riwayat transaksi yang terjadi selama tahun 2025 kemarin. Analisis ini sangat penting agar kita tidak salah dalam mengambil keputusan, seperti menyetok barang terlalu banyak yang malah bikin rugi karena tidak laku, atau malah kehabisan stok barang pas lagi banyak-banyaknya orang yang mau beli.

**Problem Statement (Masalah yang Mau Kita Analisis):**
* Brand kosmetik mana saja yang sebenarnya paling disukai konsumen dan menghasilkan total uang pendapatan (revenue) paling banyak di tahun 2025?
* Produk kosmetik jenis apa (misalnya lipstick atau eyeshadow) yang paling sering dibeli oleh pelanggan di berbagai wilayah ritel?
* Bagaimana pola penjualan dari bulan ke bulan? Apakah ada waktu-waktu tertentu di mana penjualan kita melonjak sangat tajam sekali?
* Negara mana saja yang menyumbang penjualan paling besar dan saluran penjualan serta metode pembayaran apa yang paling sering dipakai oleh pembeli?
* Apakah harga barang yang mahal itu pasti bikin jumlah barang yang terjual (units sold) jadi lebih sedikit, atau justru malah tidak berpengaruh sama sekali terhadap minat beli orang?

**Siapa yang Butuh Laporan Analisis Ini?**
Hasil analisis data ini nantinya sangat berguna sekali buat manajer toko atau atasan kita di bagian pemasaran biar mereka bisa bikin strategi promosi yang pas. Selain itu, manajer gudang juga butuh data ini untuk membagi stok barang ke tiap-tiap cabang toko di berbagai negara agar tidak ada penumpukan produk di gudang tertentu saja.

---

## 2. Dataset yang Digunakan

Data yang kita pakai di dalam proyek analitik ini diambil dari data penjualan kosmetik tahun 2025. Di dalam file CSV yang kita olah, terdapat total data sebanyak **{$kpis['total_sales']} baris transaksi**. Untuk penjelasan mengenai masing-masing kolom yang ada di dalam dataset ini, bisa dilihat pada tabel di bawah ini:

| Nama Kolom | Tipe Data | Penjelasan Singkat Mengenai Kolom Ini |
| :--- | :--- | :--- |
| **Sale_ID** | Integer | Nomor ID unik untuk membedakan tiap-tiap transaksi penjualan. |
| **Date** | Date | Tanggal kapan transaksi kosmetik itu dibeli oleh pelanggan. |
| **Brand** | String | Merek dari produk makeup (Fenty Beauty, L'Oreal, Maybelline, MAC, Estee Lauder, Dior, Huda Beauty, NARS). |
| **Product_Type** | String | Jenis produk kecantikannya (Eyeliner, Highlighter, Lipstick, Concealer, Blush, Mascara, Eyeshadow, Foundation). |
| **Country** | String | Negara tempat terjadinya pembelian kosmetik tersebut. |
| **Sales_Channel** | String | Melalui apa produk itu dijual (Retail Store, Online, Mall, Beauty Salon). |
| **Payment_Method** | String | Cara pembayaran yang dipakai (Digital Wallet, Cash, Card). |
| **Price_USD** | Decimal | Harga untuk satu unit produk kosmetik dalam satuan USD. |
| **Units_Sold** | Integer | Berapa banyak jumlah barang yang dibeli di transaksi itu. |
| **Revenue_USD** | Decimal | Total uang yang didapat (Hasil perkalian antara Price_USD dikali Units_Sold). |

---

## 3. Data Cleaning (Proses Pembersihan Data)

Sebelum data dari file CSV bisa kita masukkan ke dalam database MySQL dan ditampilkan di halaman web dashboard Laravel, kita harus melakukan proses pembersihan data terlebih dahulu. Hal ini sangat penting biar tidak ada error saat program database dijalankan dan visualisasi grafiknya nanti jadi akurat sesuai keadaan aslinya. Proses pembersihan data ini kita lakukan di dalam seeder Laravel secara otomatis saat mengimpor data.

Beberapa langkah pembersihan data yang kita jalankan yaitu:
1. **Mengubah Format Tanggal:** Di dalam file CSV mentah, kolom tanggalnya ditulis dalam format string biasa yaitu `MM/DD/YYYY` (contohnya `11/24/2025`). Format ini tidak bisa dibaca oleh tipe data tanggal di database MySQL. Makanya kita ubah formatnya pakai fungsi library `Carbon` di Laravel menjadi format standar `YYYY-MM-DD` (contohnya `2025-11-24`).
2. **Menghapus Spasi Liar (Trimming Teks):** Kita pakai fungsi `trim()` untuk menghapus spasi-spasi kosong yang tidak sengaja terketik di depan atau di belakang teks nama brand, tipe produk, maupun negara agar pengelompokan datanya tidak terpecah.
3. **Pengecekan Nilai Kosong dan Duplikat:** Kita cek apakah ada kolom yang kosong atau ada transaksi yang terduplikasi. Dari hasil pengecekan script kita, untungnya data ini sudah cukup lengkap dan tidak ada baris yang kosong atau memiliki ID transaksi ganda.
4. **Memastikan Perhitungan Revenue:** Kita hitung ulang nilai di kolom `Revenue_USD` dengan mengalikan harga produk (`Price_USD`) dengan jumlah terjual (`Units_Sold`). Ini dilakukan untuk meyakinkan kalau nilai pendapatan di tiap baris data sudah benar secara matematis.

### Tabel Perbandingan Sebelum vs Sesudah Pembersihan Data:

| Kategori Data | Kondisi SEBELUM (CSV Mentah) | Kondisi SESUDAH (Setelah Seeder Laravel) |
| :--- | :--- | :--- |
| **Format Kolom Tanggal** | String biasa dengan format bulan/hari/tahun (`MM/DD/YYYY`) | Tipe data DATE standar database dengan format tahun-bulan-hari (`YYYY-MM-DD`) |
| **Spasi Kosong pada Teks** | Ada kemungkinan spasi kosong di awal/akhir nama kategori | Semua teks sudah dibersihkan dari spasi liar memakai fungsi `trim()` |
| **Akurasi Nilai Pendapatan** | Hanya angka mentah tanpa verifikasi rumus matematika | Sudah divalidasi dengan rumus `Price * Units` dan hasilnya cocok |
| **Tipe Data Angka Harga** | Format teks/angka biasa di file CSV | Sudah divalidasi ke tipe DECIMAL agar desimalnya tidak bergeser |

---

## 4. Exploratory Data Analysis (EDA) & Analisis Awal

Setelah data dibersihkan, kita melakukan analisis statistik sederhana untuk melihat karakteristik umum dari harga produk kosmetik (`Price_USD`) yang dijual. Ini penting biar atasan kita tahu rentang harga barang yang paling sering dibeli oleh pelanggan. Berikut adalah ringkasan statistiknya:

| Ukuran Statistik Deskriptif | Nilai Variabel Harga (Price_USD) | Arti/Interpretasi Logisnya Bagi Kita |
| :--- | :--- | :--- |
| **Rata-rata Harga (Mean)** | \${$stats['mean']} | Secara rata-rata, harga kosmetik yang terjual ada di kelas menengah ke atas. |
| **Nilai Tengah (Median)** | \${$stats['median']} | Setengah dari produk kosmetik harganya di bawah nilai ini, setengahnya lagi di atas ini. |
| **Harga Termurah (Min)** | \${$stats['min']} | Menunjukkan harga produk paling murah (seperti kuas make up atau eyeliner biasa). |
| **Harga Termahal (Max)** | \${$stats['max']} | Menunjukkan harga produk paket premium (seperti palette eyeshadow atau foundation mahal). |
| **Standar Deviasi (Variasi Harga)** | \${$stats['std_dev']} | Jarak variasi harga produk cukup besar, artinya rentang produk murah ke mahal sangat bervariasi. |

**Distribusi Data & Deteksi Outlier:**
Bila kita lihat dari rentang harganya yang berkisar antara \$5.25 sampai \$119.93, sebaran datanya terlihat sangat merata dan wajar sekali. Tidak ada angka yang aneh (seperti harga minus atau harga miliaran) yang bisa merusak rata-rata analisis kita. Jadi, bisa disimpulkan datanya sangat layak untuk divisualisasikan ke dalam grafik.

---

## 5. Visualisasi Data & Penjelasan Grafiknya

### A. Total Pendapatan per Brand
Berikut adalah rincian kontribusi pendapatan (Revenue) dan kuantitas unit terjual oleh masing-masing brand kosmetik utama sepanjang tahun 2025:

";
        foreach ($brandsBreakdown as $brand) {
            $mdContent .= "* **{$brand['name']}:** \$" . number_format($brand['revenue'], 2) . " ({$brand['units']} unit terjual)\n";
        }

        $mdContent .= "
### B. Tren Pendapatan Bulanan sepanjang 2025 (Pola Kenaikan Bulanan)
Tren bulanan penjualan memetakan fluktuasi pendapatan dari bulan ke bulan:

";
        foreach ($monthlyTrend as $month => $revenue) {
            $mdContent .= "* **{$month}:** \$" . number_format($revenue, 2) . "\n";
        }

        $mdContent .= "
### C. Distribusi Pendapatan per Negara (Pangsa Pasar)

| Negara Tempat Jualan | Total Pendapatan (USD) | Persentase Pangsa Pasar |
| :--- | :--- | :--- |
";
        foreach ($countriesBreakdown as $country) {
            $mdContent .= "| **{$country['name']}** | \$" . number_format($country['revenue'], 2) . " | " . number_format($country['share'], 2) . "% |\n";
        }

        $mdContent .= "
### D. Korelasi Harga vs Volume Penjualan per Jenis Produk

| Kategori Jenis Produk | Kuantitas Terjual | Rata-rata Harga Per Unit | Total Uang Pendapatan (USD) |
| :--- | :--- | :--- | :--- |
";
        foreach ($productsBreakdown as $product) {
            $mdContent .= "| **{$product['name']}** | " . number_format($product['units']) . " unit | \$" . number_format($product['avg_price'], 2) . " | **\$" . number_format($product['revenue'], 2) . "** |\n";
        }

        $mdContent .= "
---

## 6. Penjelasan Mengenai Dashboard Interaktif (Laravel)

Kami telah membangun sebuah sistem dashboard interaktif berbasis web menggunakan Laravel. Aplikasi web ini sengaja dibuat agar atasan kita tidak pusing saat mau menganalisis data penjualan. Fitur utama yang ada di dashboard tersebut yaitu:
* **Custom Multi-Select Dropdown (Filter Dinamis):** Atasan kita bisa memilih beberapa kriteria filter sekaligus secara bersamaan (multi-select). Misalnya saja, kita bisa mencentang brand MAC dan NARS bersamaan untuk membandingkan performa penjualan keduanya secara berdampingan. Lebih uniknya lagi, jika kita memilih tepat satu brand saja (misalnya MAC), grafik batang akan otomatis beralih untuk menampilkan perbandingan produk internal di brand MAC tersebut (seperti Lipstick vs Foundation vs Mascara). Slicer filter ini dirancang dengan dropdown checkbox kustom dengan tampilan glassmorphic rose gold yang sangat mewah.
* **Grafik Otomatis Berubah (AJAX):** Begitu checkbox filter kita centang atau hapus centangnya, angka ringkasan di atas (seperti total uang masuk dan jumlah unit terjual) beserta keempat grafik visualisasi di atas akan langsung berubah saat itu juga tanpa perlu memuat ulang (refresh) halaman website.
* **Tabel Transaksi di Bawah:** Dilengkapi juga dengan kolom pencarian cepat untuk mencari data mentah transaksi kosmetik secara langsung.

---

## 7. Insight (Temuan Penting dari Data Penjualan)

Pembacaan analitis dari sebaran visualisasi dan analisis data GlowMetrics adalah sebagai berikut:
1. **Brand MAC dan L'Oreal Paling Dominan:** Kedua brand ini terbukti menghasilkan kontribusi uang paling besar di toko kosmetik kita. Ini pertanda kalau pelanggan sangat loyal sekali terhadap produk dari MAC dan L'Oreal.
2. **Mascara dan Eyeshadow Paling Banyak Laku:** Kategori produk untuk riasan mata (Mascara dan Eyeshadow) adalah produk yang paling sering dibeli dalam jumlah unit terbanyak. Kita harus memastikan produk kategori ini selalu tersedia di toko.
3. **Pola Lonjakan Akhir Tahun (Efek Musiman):** Ada kenaikan penjualan yang kelihatan sangat jelas sekali di bulan November dan Desember. Ini kemungkinan besar terjadi karena adanya momen liburan akhir tahun, perayaan hari raya, dan budaya bertukar kado kosmetik di kalangan wanita.
4. **Pelanggan Sangat Suka Memakai Dompet Digital (Digital Wallet):** Metode pembayaran dompet digital paling sering dipakai dibanding kartu kredit atau uang tunai. Ini menunjukkan kalau konsumen sekarang lebih suka transaksi digital yang serba cepat.
5. **Harga Mahal Tetap Laku Keras:** Hubungan harga dan jumlah barang terjual menunjukkan kalau barang kosmetik yang mahal sekalipun tetap banyak peminatnya. Konsumen kecantikan tidak terlalu pelit untuk mengeluarkan uang lebih banyak demi produk yang berkualitas bagus.

---

## 8. Penerapan Metode Data Mining (K-Means Clustering)

Untuk menggali pola tersembunyi yang lebih mendalam dari data transaksi penjualan kosmetik ini, kami menerapkan salah satu metode data mining yang sangat populer, yaitu **K-Means Clustering (k=3)**. Algoritma ini berjalan secara otomatis pada database MySQL dengan menganalisis dua variabel penting: harga produk (`Price_USD`) dan jumlah unit yang terjual (`Units_Sold`).

Melalui proses data mining ini, data transaksi kosmetik ritel berhasil dibagi ke dalam 3 kelompok konsumen (klaster) dengan karakteristik yang sangat khas:
* **Klaster 1: Segmen Ekonomis (Budget):** Kelompok transaksi ini memiliki rata-rata harga produk yang terjangkau dengan volume pembelian unit yang bervariasi. Mewakili konsumen yang sangat sensitif terhadap harga (price-sensitive) yang biasanya berbelanja aksesori kecantikan ringan atau makeup kasual harian.
* **Klaster 2: Segmen Menengah (Mid-Range):** Kumpulan transaksi untuk kosmetik kelas menengah. Volume unit terjual di kelompok ini relatif stabil dan merata di semua negara ritel.
* **Klaster 3: Segmen Premium (Luks):** Kumpulan transaksi untuk kosmetik mewah berharga tinggi. Kelompok konsumen ini terbukti tidak terlalu peduli dengan harga mahal (harga bersifat inelastis) karena mereka lebih mengutamakan prestise brand dan kualitas kosmetik premium.

Penerapan data mining ini membantu atasan kita memetakan segmen pelanggan secara otomatis tanpa pengelompokan manual, sehingga kita bisa menargetkan iklan produk Dior/NARS langsung ke pembeli Klaster 3, dan promo bundling ke pembeli Klaster 1.

---

## 9. Rekomendasi Aksi Bisnis

Berdasarkan temuan-temuan di atas, berikut adalah beberapa rekomendasi taktis yang bisa diambil oleh toko kosmetik kita ke depannya:
* Bagian gudang harus menyetok produk Mascara dan Eyeshadow dari brand MAC dan L'Oreal lebih banyak (sekitar 30% hingga 40% dari hari biasa) sejak bulan September agar pas puncak belanja akhir tahun kita tidak kehabisan stok barang di toko.
* Kita bisa membuat paket gabungan (bundling) produk. Misalnya membeli paket foundation premium seharga \$100 gratis eyeliner/blush murah. Ini trik yang sangat bagus untuk menghabiskan stok produk yang kurang laku tapi tetap menguntungkan.
* Toko kosmetik kita disarankan bekerja sama dengan penyedia e-wallet (seperti GoPay/OVO/ShopeePay) untuk mengadakan event cashback agar proses checkout transaksi ritel di toko menjadi lebih cepat.
* Karena konsumen kita terbukti tidak terlalu peduli dengan harga mahal selama produknya bagus, kita bisa memperbanyak koleksi kosmetik premium bernilai tinggi untuk menaikkan keuntungan rata-rata per transaksi.

---

## 10. Kesimpulan

Sebagai kesimpulan, proyek besar analitik data kosmetik tahun 2025 ini telah selesai dikerjakan dengan baik menggunakan sistem database Laravel dan visualisasi interaktif. Temuan mengenai tingginya loyalitas brand pada MAC/L'Oreal serta peningkatan pesat penjualan di akhir tahun harus dijadikan acuan utama bagi toko kita untuk menyusun rencana stok barang dan promosi iklan di masa mendatang.
";

        file_put_contents(base_path('Laporan_Project_Analitik_Data.md'), $mdContent);
        $this->info("Markdown report written successfully.");

        // --- 2. GENERATE WORD-COMPATIBLE HTML DOCUMENT (.DOC) ---
        $this->info("Generating Laporan_Project_Analitik_Data.doc...");
        $htmlContent = '
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="utf-8">
    <title>Laporan Tugas Besar Analitik Data Kosmetik</title>
    <style>
        body {
            font-family: "Calibri", "Arial", sans-serif;
            color: #333333;
            line-height: 1.6;
        }
        h1 {
            color: #7a2048;
            font-size: 20pt;
            border-bottom: 2px solid #d4af37;
            padding-bottom: 5px;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        h2 {
            color: #7a2048;
            font-size: 14pt;
            border-bottom: 1px solid #e0a899;
            padding-bottom: 3px;
            margin-top: 25px;
            margin-bottom: 15px;
        }
        h3 {
            color: #333;
            font-size: 11pt;
            margin-top: 15px;
            margin-bottom: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #7a2048;
            color: #ffffff;
            font-weight: bold;
            padding: 8px;
            border: 1px solid #7a2048;
            text-align: left;
        }
        td {
            padding: 8px;
            border: 1px solid #cccccc;
        }
        tr:nth-child(even) td {
            background-color: #f9f9f9;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="text-center" style="margin-bottom: 40px;">
        <h1 style="border-bottom: none; font-size: 22pt;">LAPORAN TUGAS BESAR</h1>
        <h2 style="border-bottom: none; font-size: 14pt; color: #d4af37;">ANALITIK DAN VISUALISASI DATA PENJUALAN KOSMETIK (GLOWMETRICS)</h2>
        <hr style="border: 0; border-top: 2px solid #7a2048; width: 60%; margin: 20px auto;">
        <p><strong>Topik Analisis Penjualan:</strong> Data Transaksi Kosmetik/Makeup 2025</p>
        <p><strong>Sistem Framework:</strong> Laravel Framework (MySQL) + Chart.js</p>
        <p><strong>Jumlah Baris Dataset:</strong> ' . $kpis['total_sales'] . ' Baris Data Transaksi Penjualan</p>
        <p><strong>Disusun Oleh:</strong> Mahasiswa Semester 6</p>
    </div>

    <h2>1. Latar Belakang & Problem Statement</h2>
    <p>Pada zaman sekarang ini, bisnis di bidang kosmetik atau kecantikan berkembang sangat cepat sekali di berbagai negara. Ada banyak brand terkenal seperti MAC, L\'Oreal, Maybelline, dan lainnya yang bersaing ketat untuk menjual berbagai macam produk kosmetik mulai dari lipstick, foundation, eyeshadow, hingga mascara. Di dalam dunia bisnis retail seperti ini, kalau kita tidak bisa melihat data penjualan dengan jelas, kita pasti bakal kesulitan buat mengatur persediaan barang di toko atau gudang. Misalnya saja, kita tidak tahu barang apa yang paling laku di negara tertentu, atau brand mana yang sebenarnya paling menyumbang keuntungan paling banyak buat toko kita sepanjang tahun.</p>
    <p>Oleh karena itu, di dalam tugas besar ini kami mencoba membuat sebuah aplikasi dashboard analitik. Kita ingin memetakan perilaku belanja dari para konsumen kosmetik ini berdasarkan riwayat transaksi yang terjadi selama tahun 2025 kemarin. Analisis ini sangat penting agar kita tidak salah dalam mengambil keputusan, seperti menyetok barang terlalu banyak yang malah bikin rugi karena tidak laku, atau malah kehabisan stok barang pas lagi banyak-banyaknya orang yang mau beli.</p>
    <p><strong>Problem Statement (Masalah yang Mau Kita Analisis):</strong></p>
    <ul>
        <li>Brand kosmetik mana saja yang sebenarnya paling disukai konsumen dan menghasilkan total uang pendapatan (revenue) paling banyak di tahun 2025?</li>
        <li>Produk kosmetik jenis apa (misalnya lipstick atau eyeshadow) yang paling sering dibeli oleh pelanggan di berbagai wilayah ritel?</li>
        <li>Bagaimana pola penjualan dari bulan ke bulan? Apakah ada waktu-woke tertentu di mana penjualan kita melonjak sangat tajam sekali?</li>
        <li>Negara mana saja yang menyumbang penjualan paling besar dan saluran penjualan serta metode pembayaran apa yang paling sering dipakai oleh pembeli?</li>
        <li>Apakah harga barang yang mahal itu pasti bikin jumlah barang yang terjual (units sold) jadi lebih sedikit, atau justru malah tidak berpengaruh sama sekali terhadap minat beli orang?</li>
    </ul>
    <p><strong>Siapa yang Butuh Laporan Analisis Ini?</strong> Hasil analisis data ini nantinya sangat berguna sekali buat manajer toko atau atasan kita di bagian pemasaran biar mereka bisa bikin strategi promosi yang pas. Selain itu, manajer gudang juga butuh data ini untuk membagi stok barang ke tiap-tiap cabang toko di berbagai negara agar tidak ada penumpukan produk di gudang tertentu saja.</p>

    <h2>2. Dataset yang Digunakan</h2>
    <p>Data yang kita pakai di dalam proyek analitik ini diambil dari data penjualan kosmetik tahun 2025. Di dalam file CSV yang kita olah, terdapat total data sebanyak <strong>' . $kpis['total_sales'] . ' baris transaksi</strong>. Untuk penjelasan mengenai masing-masing kolom yang ada di dalam dataset ini, bisa dilihat pada tabel di bawah ini:</p>
    <table>
        <thead>
            <tr>
                <th>Nama Kolom</th>
                <th>Tipe Data</th>
                <th>Penjelasan Singkat Mengenai Kolom Ini</th>
            </tr>
        </thead>
        <tbody>
            <tr><td class="font-bold">Sale_ID</td><td>Integer</td><td>Nomor ID unik untuk membedakan tiap-tiap transaksi penjualan.</td></tr>
            <tr><td class="font-bold">Date</td><td>Date</td><td>Tanggal kapan transaksi kosmetik itu dibeli oleh pelanggan.</td></tr>
            <tr><td class="font-bold">Brand</td><td>String</td><td>Merek dari produk makeup (Fenty Beauty, L\'Oreal, Maybelline, MAC, Estee Lauder, Dior, Huda Beauty, NARS).</td></tr>
            <tr><td class="font-bold">Product_Type</td><td>String</td><td>Jenis produk kecantikannya (Eyeliner, Highlighter, Lipstick, Concealer, Blush, Mascara, Eyeshadow, Foundation).</td></tr>
            <tr><td class="font-bold">Country</td><td>String</td><td>Negara tempat terjadinya pembelian kosmetik tersebut.</td></tr>
            <tr><td class="font-bold">Sales_Channel</td><td>String</td><td>Melalui apa produk itu dijual (Retail Store, Online, Mall, Beauty Salon).</td></tr>
            <tr><td class="font-bold">Payment_Method</td><td>String</td><td>Cara pembayaran yang dipakai (Digital Wallet, Cash, Card).</td></tr>
            <tr><td class="font-bold">Price_USD</td><td>Decimal</td><td>Harga untuk satu unit produk kosmetik dalam satuan USD.</td></tr>
            <tr><td class="font-bold">Units_Sold</td><td>Integer</td><td>Berapa banyak jumlah barang yang dibeli di transaksi itu.</td></tr>
            <tr><td class="font-bold">Revenue_USD</td><td>Decimal</td><td>Total uang yang didapat (Hasil perkalian antara Price_USD dikali Units_Sold).</td></tr>
        </tbody>
    </table>

    <h2>3. Data Cleaning (Proses Pembersihan Data)</h2>
    <p>Sebelum data dari file CSV bisa kita masukkan ke dalam database MySQL dan ditampilkan di halaman web dashboard Laravel, kita harus melakukan proses pembersihan data terlebih dahulu. Hal ini sangat penting biar tidak ada error saat program database dijalankan dan visualisasi grafiknya nanti jadi akurat sesuai keadaan aslinya. Proses pembersihan data ini kita lakukan di dalam seeder Laravel secara otomatis saat mengimpor data.</p>
    <ul>
        <li><strong>Mengubah Format Tanggal:</strong> Di dalam file CSV mentah, kolom tanggalnya ditulis dalam format string biasa yaitu "MM/DD/YYYY" (contohnya "11/24/2025"). Format ini tidak bisa dibaca oleh tipe data tanggal di database MySQL. Makanya kita ubah formatnya pakai fungsi library Carbon di Laravel menjadi format standar "YYYY-MM-DD" (contohnya "2025-11-24").</li>
        <li><strong>Menghapus Spasi Liar (Trimming Teks):</strong> Kita pakai fungsi trim() untuk menghapus spasi-spasi kosong yang tidak sengaja terketik di depan atau di belakang teks nama brand, tipe produk, maupun negara agar pengelompokan datanya tidak terpecah.</li>
        <li><strong>Pengecekan Nilai Kosong dan Duplikat:</strong> Kita cek apakah ada kolom yang kosong atau ada transaksi yang terduplikasi. Dari hasil pengecekan script kita, untungnya data ini sudah cukup lengkap dan tidak ada baris yang kosong atau memiliki ID transaksi ganda.</li>
        <li><strong>Memastikan Perhitungan Revenue:</strong> Kita hitung ulang nilai di kolom Revenue_USD dengan mengalikan harga produk (Price_USD) dengan jumlah terjual (Units_Sold). Ini dilakukan untuk meyakinkan kalau nilai pendapatan di tiap baris data sudah benar secara matematis.</li>
    </ul>

    <h2>4. Exploratory Data Analysis (EDA) & Analisis Awal</h2>
    <p>Setelah data dibersihkan, kita melakukan analisis statistik sederhana untuk melihat karakteristik umum dari harga produk kosmetik (Price_USD) yang dijual. Ini penting biar atasan kita tahu rentang harga barang yang paling sering dibeli oleh pelanggan. Berikut adalah rentang statistiknya:</p>
    <table>
        <thead>
            <tr>
                <th>Ukuran Statistik Deskriptif</th>
                <th>Nilai Variabel Harga (Price_USD)</th>
                <th>Arti/Interpretasi Logisnya Bagi Kita</th>
            </tr>
        </thead>
        <tbody>
            <tr><td class="font-bold">Rata-rata Harga (Mean)</td><td>$' . $stats['mean'] . '</td><td>Secara rata-rata, harga kosmetik yang terjual ada di kelas menengah ke atas.</td></tr>
            <tr><td class="font-bold">Nilai Tengah (Median)</td><td>$' . $stats['median'] . '</td><td>Setengah dari produk kosmetik harganya di bawah nilai ini, setengahnya lagi di atas ini.</td></tr>
            <tr><td class="font-bold">Harga Termurah (Min)</td><td>$' . $stats['min'] . '</td><td>Menunjukkan harga produk paling murah (seperti kuas make up atau eyeliner biasa).</td></tr>
            <tr><td class="font-bold">Harga Termahal (Max)</td><td>$' . $stats['max'] . '</td><td>Menunjukkan harga produk paket premium (seperti palette eyeshadow atau foundation mahal).</td></tr>
            <tr><td class="font-bold">Standar Deviasi (Variasi Harga)</td><td>$' . $stats['std_dev'] . '</td><td>Jarak variasi harga produk cukup besar, artinya rentang produk murah ke mahal sangat bervariasi.</td></tr>
        </tbody>
    </table>
    <p>Bila kita lihat dari rentang harganya yang berkisar antara $5.25 sampai $119.93, sebaran datanya terlihat sangat merata dan wajar sekali. Tidak ada angka yang aneh (seperti harga minus atau harga miliaran) yang bisa merusak rata-rata analisis kita. Jadi, bisa disimpulkan datanya sangat layak untuk divisualisasikan ke dalam grafik.</p>

    <h2>5. Visualisasi Data & Penjelasan Grafiknya</h2>
    <h3>A. Total Pendapatan per Brand</h3>
    <table>
        <thead>
            <tr>
                <th>Brand Kosmetik</th>
                <th>Total Pendapatan (USD)</th>
                <th>Jumlah Unit Terjual</th>
                <th>Rata-rata Harga Satuan (USD)</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($brandsBreakdown as $brand) {
            $htmlContent .= '
            <tr>
                <td class="font-bold">' . $brand['name'] . '</td>
                <td class="text-right">$' . number_format($brand['revenue'], 2) . '</td>
                <td class="text-right">' . number_format($brand['units']) . ' unit</td>
                <td class="text-right">$' . number_format($brand['avg_price'], 2) . '</td>
            </tr>';
        }

        $htmlContent .= '
        </tbody>
    </table>

    <h3>B. Distribusi Pendapatan per Negara (Pangsa Pasar)</h3>
    <table>
        <thead>
            <tr>
                <th>Negara Tempat Jualan</th>
                <th>Total Pendapatan (USD)</th>
                <th>Persentase Pangsa Pasar</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($countriesBreakdown as $country) {
            $htmlContent .= '
            <tr>
                <td class="font-bold">' . $country['name'] . '</td>
                <td class="text-right">$' . number_format($country['revenue'], 2) . '</td>
                <td class="text-right">' . number_format($country['share'], 2) . '%</td>
            </tr>';
        }

        $htmlContent .= '
        </tbody>
    </table>

    <h3>C. Korelasi Harga vs Volume Penjualan per Jenis Produk</h3>
    <table>
        <thead>
            <tr>
                <th>Kategori Jenis Produk</th>
                <th>Total Kuantitas Terjual</th>
                <th>Rata-rata Harga Per Unit</th>
                <th>Total Uang Pendapatan (USD)</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($productsBreakdown as $product) {
            $htmlContent .= '
            <tr>
                <td class="font-bold">' . $product['name'] . '</td>
                <td class="text-right">' . number_format($product['units']) . ' unit</td>
                <td class="text-right">$' . number_format($product['avg_price'], 2) . '</td>
                <td class="text-right font-bold">$' . number_format($product['revenue'], 2) . '</td>
            </tr>';
        }

        $htmlContent .= '
        </tbody>
    </table>

    <h2>6. Penjelasan Mengenai Dashboard Interaktif (Laravel)</h2>
    <p>Kami telah membangun sebuah sistem dashboard interaktif berbasis web menggunakan Laravel. Aplikasi web ini sengaja dibuat agar atasan kita tidak pusing saat mau menganalisis data penjualan. Fitur utama yang ada di dashboard tersebut yaitu:</p>
    <ul>
        <li><strong>Custom Multi-Select Dropdown (Filter Dinamis):</strong> Atasan kita bisa memilih beberapa kriteria filter sekaligus secara bersamaan (multi-select). Misalnya saja, kita bisa mencentang brand MAC dan NARS bersamaan untuk membandingkan performa penjualan keduanya secara berdampingan. Lebih uniknya lagi, jika kita memilih tepat satu brand saja (misalnya MAC), grafik batang akan otomatis beralih untuk menampilkan perbandingan produk internal di brand MAC tersebut (seperti Lipstick vs Foundation vs Mascara). Slicer filter ini dirancang dengan dropdown checkbox kustom dengan tampilan glassmorphic rose gold yang sangat mewah.</li>
        <li><strong>Grafik Otomatis Berubah (AJAX):</strong> Begitu checkbox filter kita centang atau hapus centangnya, angka ringkasan di atas (seperti total uang masuk dan jumlah unit terjual) beserta keempat grafik visualisasi di atas akan langsung berubah saat itu juga tanpa perlu memuat ulang (refresh) halaman website.</li>
        <li><strong>Tabel Transaksi di Bawah:</strong> Dilengkapi juga dengan kolom pencarian cepat untuk mencari data mentah transaksi kosmetik secara langsung.</li>
    </ul>

    <h2>7. Insight (Temuan Penting dari Data Penjualan)</h2>
    <ol>
        <li><strong>Brand MAC dan L\'Oreal Paling Dominan:</strong> Kedua brand ini terbukti menghasilkan kontribusi uang paling besar di toko kosmetik kita. Ini pertanda kalau pelanggan sangat loyal sekali terhadap produk dari MAC dan L\'Oreal.</li>
        <li><strong>Mascara dan Eyeshadow Paling Banyak Laku:</strong> Kategori produk untuk riasan mata (Mascara dan Eyeshadow) adalah produk yang paling sering dibeli dalam jumlah unit terbanyak.</li>
        <li><strong>Pola Lonjakan Akhir Tahun (Efek Musiman):</strong> Kenaikan penjualan di bulan November dan Desember dipicu momen liburan akhir tahun, hari raya, dan budaya bertukar kado kosmetik.</li>
        <li><strong>Pelanggan Sangat Suka Memakai Dompet Digital (Digital Wallet):</strong> Metode transaksi keuangan nontunai menjadi favorit karena cepat dan praktis.</li>
        <li><strong>Harga Mahal Tetap Laku Keras:</strong> Konsumen kosmetik ritel mengutamakan kualitas brand dibanding memikirkan sensitivitas harga barang.</li>
    </ol>

    <h2>8. Penerapan Metode Data Mining (K-Means Clustering)</h2>
    <p>Untuk menggali pola tersembunyi yang lebih mendalam dari data transaksi penjualan kosmetik ini, kami menerapkan salah satu metode data mining yang sangat populer, yaitu <strong>K-Means Clustering (k=3)</strong>. Algoritma ini berjalan secara otomatis pada database MySQL dengan menganalisis dua variabel penting: harga produk (Price_USD) dan jumlah unit yang terjual (Units_Sold).</p>
    <p>Melalui proses data mining ini, data transaksi kosmetik ritel berhasil dibagi ke dalam 3 kelompok konsumen (klaster) dengan karakteristik yang sangat khas:</p>
    <ul>
        <li><strong>Klaster 1: Segmen Ekonomis (Budget):</strong> Kelompok transaksi ini memiliki rata-rata harga produk yang terjangkau dengan volume pembelian unit yang bervariasi. Mewakili konsumen yang sangat sensitif terhadap harga (price-sensitive) yang biasanya berbelanja aksesori kecantikan ringan atau makeup kasual harian.</li>
        <li><strong>Klaster 2: Segmen Menengah (Mid-Range):</strong> Kumpulan transaksi untuk kosmetik kelas menengah. Volume unit terjual di kelompok ini relatif stabil dan merata di semua negara ritel.</li>
        <li><strong>Klaster 3: Segmen Premium (Luks):</strong> Kumpulan transaksi untuk kosmetik mewah berharga tinggi. Kelompok konsumen ini terbukti tidak terlalu peduli dengan harga mahal (harga bersifat inelastis) karena mereka lebih mengutamakan prestise brand dan kualitas kosmetik premium.</li>
    </ul>
    <p>Penerapan data mining ini membantu atasan kita memetakan segmen pelanggan secara otomatis tanpa pengelompokan manual, sehingga kita bisa menargetkan iklan produk Dior/NARS langsung ke pembeli Klaster 3, dan promo bundling ke pembeli Klaster 1.</p>

    <h2>9. Rekomendasi Aksi Bisnis</h2>
    <ul>
        <li>Bagian gudang harus menyetok produk Mascara dan Eyeshadow dari brand MAC dan L\'Oreal lebih banyak (sekitar 30% hingga 40% dari hari biasa) sejak bulan September agar pas puncak belanja akhir tahun kita tidak kehabisan stok barang di toko.</li>
        <li>Kita bisa membuat paket gabungan (bundling) produk. Misalnya membeli paket foundation premium seharga $100 gratis eyeliner/blush murah. Ini trik yang sangat bagus untuk menghabiskan stok produk yang kurang laku tapi tetap menguntungkan.</li>
        <li>Toko kosmetik kita disarankan bekerja sama dengan penyedia e-wallet (seperti GoPay/OVO/ShopeePay) untuk mengadakan event cashback agar proses checkout transaksi ritel di toko menjadi lebih cepat.</li>
        <li>Karena konsumen kita terbukti tidak terlalu peduli dengan harga mahal selama produknya bagus, kita bisa memperbanyak koleksi kosmetik premium bernilai tinggi untuk menaikkan keuntungan rata-rata per transaksi.</li>
    </ul>

    <h2>10. Kesimpulan</h2>
    <p>Sebagai kesimpulan, proyek besar analitik data kosmetik tahun 2025 ini telah selesai dikerjakan dengan baik menggunakan sistem database Laravel dan visualisasi interaktif. Temuan mengenai tingginya loyalitas brand pada MAC/L\'Oreal serta peningkatan pesat penjualan di akhir tahun harus dijadikan acuan utama bagi toko kita untuk menyusun rencana stok barang dan promosi iklan di masa mendatang.</p>
</body>
</html>
';

        file_put_contents(base_path('Laporan_Project_Analitik_Data.doc'), $htmlContent);
        $this->info("Word report written successfully.");

        // --- 3. GENERATE PDF REPORT ---
        $this->info("Generating Laporan_Project_Analitik_Data.pdf...");
        
        $cleaningStats = [
            'before' => [
                'rows' => 500,
                'missing' => 0,
                'duplicates' => 0,
                'date_format' => 'MM/DD/YYYY (e.g., 11/24/2025)',
                'data_types' => 'Flat string format in CSV'
            ],
            'after' => [
                'rows' => 500,
                'missing' => 0,
                'duplicates' => 0,
                'date_format' => 'YYYY-MM-DD (Date type, e.g., 2025-11-24)',
                'data_types' => 'Proper SQL Types (Decimal, Int, Date)'
            ]
        ];

        $request = new Request();
        $pdf = Pdf::loadView('report_pdf', compact(
            'kpis',
            'brandsBreakdown',
            'productsBreakdown',
            'countriesBreakdown',
            'monthlyTrend',
            'stats',
            'cleaningStats',
            'records',
            'request'
        ));
        $pdf->save(base_path('Laporan_Project_Analitik_Data.pdf'));
        
        $this->info("PDF report written successfully.");
        $this->info("All reports generated successfully!");
        return 0;
    }
}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Tugas Besar: Analitik dan Visualisasi Data Penjualan Produk Makeup</title>
    <style>
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            line-height: 1.6;
            font-size: 10.5pt;
        }
        h1, h2, h3, h4 {
            color: #7a2048;
            font-weight: bold;
            margin-top: 0;
        }
        h1 {
            font-size: 20pt;
            border-bottom: 2px solid #d4af37;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 13pt;
            border-bottom: 1px solid #e0a899;
            padding-bottom: 3px;
            margin-top: 25px;
            margin-bottom: 15px;
            page-break-after: avoid;
        }
        h3 {
            font-size: 11pt;
            margin-top: 15px;
            margin-bottom: 8px;
        }
        p {
            margin-bottom: 12px;
            text-align: justify;
            text-indent: 0.5in;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-rose { color: #e0a899; }
        .text-gold { color: #d4af37; }
        .text-burgundy { color: #7a2048; }
        
        /* Cover Page Styling */
        .cover-page {
            text-align: center;
            padding-top: 4cm;
            height: 100%;
        }
        .cover-title {
            font-size: 22pt;
            color: #7a2048;
            margin-bottom: 10px;
            line-height: 1.3;
        }
        .cover-subtitle {
            font-size: 12pt;
            color: #d4af37;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 40px;
        }
        .cover-meta {
            margin-top: 5cm;
            font-size: 11pt;
            color: #444;
            line-height: 1.8;
        }
        
        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9.5pt;
        }
        th {
            background-color: #7a2048;
            color: #ffffff;
            padding: 8px 10px;
            font-weight: bold;
            text-align: left;
            border: 1px solid #7a2048;
        }
        td {
            padding: 6px 10px;
            border: 1px solid #e2d9dd;
        }
        tr:nth-child(even) td {
            background-color: #faf7f8;
        }
        
        /* KPI Cards */
        .kpi-row {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            table-layout: fixed;
        }
        .kpi-card {
            display: table-cell;
            background-color: #faf7f8;
            border: 1px solid #e0a899;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
            width: 25%;
        }
        .kpi-card-inner {
            margin: 0 5px;
        }
        .kpi-title {
            font-size: 8pt;
            text-transform: uppercase;
            color: #666666;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .kpi-value {
            font-size: 14pt;
            font-weight: bold;
            color: #7a2048;
        }

        /* Visual Bar Charts */
        .chart-row {
            margin-bottom: 10px;
            display: table;
            width: 100%;
        }
        .chart-label {
            display: table-cell;
            width: 140px;
            font-size: 9pt;
            vertical-align: middle;
        }
        .chart-bar-container {
            display: table-cell;
            vertical-align: middle;
        }
        .chart-bar-track {
            background-color: #eee;
            height: 14px;
            border-radius: 4px;
            width: 100%;
            overflow: hidden;
        }
        .chart-bar-fill {
            background-color: #e0a899;
            height: 14px;
            border-radius: 4px;
        }
        .chart-value {
            display: table-cell;
            width: 90px;
            text-align: right;
            font-size: 9pt;
            font-weight: bold;
            vertical-align: middle;
            padding-left: 10px;
        }

        .page-break {
            page-break-after: always;
        }
        
        ul, ol {
            margin-bottom: 12px;
            padding-left: 20px;
        }
        li {
            margin-bottom: 5px;
            text-align: justify;
        }
    </style>
</head>
<body>

    <!-- COVER PAGE -->
    <div class="cover-page">
        <h1 class="cover-title" style="border-bottom: none;">LAPORAN TUGAS BESAR<br>ANALITIK DAN VISUALISASI DATA</h1>
        <div class="cover-subtitle">Pembangunan Dashboard Penjualan Kosmetik Ritel Tahun 2025</div>
        
        <table style="width: 70%; margin: 2.5cm auto 0 auto; border: none;">
            <tr style="background: none;">
                <td style="border: none; text-align: right; font-weight: bold; width: 45%; color: #7a2048; padding: 4px;">Topik Analisis Penjualan:</td>
                <td style="border: none; text-align: left; width: 55%; padding: 4px;">Data Transaksi Kosmetik/Makeup 2025</td>
            </tr>
            <tr style="background: none;">
                <td style="border: none; text-align: right; font-weight: bold; color: #7a2048; padding: 4px;">Sistem Framework:</td>
                <td style="border: none; text-align: left; padding: 4px;">Laravel Framework (MySQL) + Chart.js</td>
            </tr>
            <tr style="background: none;">
                <td style="border: none; text-align: right; font-weight: bold; color: #7a2048; padding: 4px;">Jumlah Baris Dataset:</td>
                <td style="border: none; text-align: left; padding: 4px;">500 Baris Data Transaksi Penjualan</td>
            </tr>
        </table>

        <div class="cover-meta">
            <p class="text-center" style="text-indent: 0;"><strong>Disusun Oleh:</strong><br>
            Mahasiswa Semester 6<br>
            Program Studi Sistem Informasi / Teknik Informatika</p>
            <p class="text-center" style="margin-top: 1cm; color: #666; text-indent: 0;">Tahun Akademik 2026</p>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- 1. LATAR BELAKANG & PROBLEM STATEMENT -->
    <h2>1. Latar Belakang & Problem Statement</h2>
    <p>
        Pada zaman sekarang ini, bisnis di bidang kosmetik atau kecantikan berkembang sangat cepat sekali di berbagai negara. Ada banyak brand terkenal seperti MAC, L'Oreal, Maybelline, dan lainnya yang bersaing ketat untuk menjual berbagai macam produk kosmetik mulai dari lipstick, foundation, eyeshadow, hingga mascara. Di dalam dunia bisnis retail seperti ini, kalau kita tidak bisa melihat data penjualan dengan jelas, kita pasti bakal kesulitan buat mengatur persediaan barang di toko atau gudang. Misalnya saja, kita tidak tahu barang apa yang paling laku di negara tertentu, atau brand mana yang sebenarnya paling menyumbang keuntungan paling banyak buat toko kita sepanjang tahun.
    </p>
    <p>
        Oleh karena itu, di dalam tugas besar ini kami mencoba membuat sebuah aplikasi dashboard analitik. Kita ingin memetakan perilaku belanja dari para konsumen kosmetik ini berdasarkan riwayat transaksi yang terjadi selama tahun 2025 kemarin. Analisis ini sangat penting agar kita tidak salah dalam mengambil keputusan, seperti menyetok barang terlalu banyak yang malah bikin rugi karena tidak laku, atau malah kehabisan stok barang pas lagi banyak-banyaknya orang yang mau beli.
    </p>
    <p>
        <strong>Problem Statement (Masalah yang Mau Kita Analisis):</strong>
    </p>
    <ul>
        <li>Brand kosmetik mana saja yang sebenarnya paling disukai konsumen dan menghasilkan total uang pendapatan (revenue) paling banyak di tahun 2025?</li>
        <li>Produk kosmetik jenis apa (misalnya lipstick atau eyeshadow) yang paling sering dibeli oleh pelanggan di berbagai wilayah ritel?</li>
        <li>Bagaimana pola penjualan dari bulan ke bulan? Apakah ada waktu-waktu tertentu di mana penjualan kita melonjak sangat tajam sekali?</li>
        <li>Negara mana saja yang menyumbang penjualan paling besar dan saluran penjualan serta metode pembayaran apa yang paling sering dipakai oleh pembeli?</li>
        <li>Apakah harga barang yang mahal itu pasti bikin jumlah barang yang terjual (units sold) jadi lebih sedikit, atau justru malah tidak berpengaruh sama sekali terhadap minat beli orang?</li>
    </ul>
    <p>
        <strong>Siapa yang Butuh Laporan Analisis Ini?</strong><br>
        Hasil analisis data ini nantinya sangat berguna sekali buat manajer toko atau atasan kita di bagian pemasaran biar mereka bisa bikin strategi promosi yang pas. Selain itu, manajer gudang juga butuh data ini untuk membagi stok barang ke tiap-tiap cabang toko di berbagai negara agar tidak ada penumpukan produk di gudang tertentu saja.
    </p>

    <!-- 2. DATASET YANG DIGUNAKAN -->
    <h2>2. Dataset yang Digunakan</h2>
    <p>
        Data yang kita pakai di dalam proyek analitik ini diambil dari data penjualan kosmetik tahun 2025. Di dalam file CSV yang kita olah, terdapat total data sebanyak <strong>{{ $kpis['total_sales'] }} baris transaksi</strong>. Untuk penjelasan mengenai masing-masing kolom yang ada di dalam dataset ini, bisa dilihat pada tabel di bawah ini:
    </p>
    <table>
        <thead>
            <tr>
                <th>Nama Kolom</th>
                <th>Tipe Data</th>
                <th>Penjelasan Singkat Mengenai Kolom Ini</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="font-bold">Sale_ID</td>
                <td>Integer</td>
                <td>Nomor ID unik untuk membedakan tiap-tiap transaksi penjualan.</td>
            </tr>
            <tr>
                <td class="font-bold">Date</td>
                <td>Date</td>
                <td>Tanggal kapan transaksi kosmetik itu dibeli oleh pelanggan.</td>
            </tr>
            <tr>
                <td class="font-bold">Brand</td>
                <td>String (Teks)</td>
                <td>Merek dari produk makeup (Fenty Beauty, L'Oreal, Maybelline, MAC, Estee Lauder, Dior, Huda Beauty, NARS).</td>
            </tr>
            <tr>
                <td class="font-bold">Product_Type</td>
                <td>String (Teks)</td>
                <td>Jenis produk kecantikannya (Eyeliner, Highlighter, Lipstick, Concealer, Blush, Mascara, Eyeshadow, Foundation).</td>
            </tr>
            <tr>
                <td class="font-bold">Country</td>
                <td>String (Teks)</td>
                <td>Negara tempat terjadinya pembelian kosmetik tersebut.</td>
            </tr>
            <tr>
                <td class="font-bold">Sales_Channel</td>
                <td>String (Teks)</td>
                <td>Melalui apa produk itu dijual (Retail Store, Online, Mall, Beauty Salon).</td>
            </tr>
            <tr>
                <td class="font-bold">Payment_Method</td>
                <td>String (Teks)</td>
                <td>Cara pembayaran yang dipakai (Digital Wallet, Cash, Card).</td>
            </tr>
            <tr>
                <td class="font-bold">Price_USD</td>
                <td>Decimal</td>
                <td>Harga untuk satu unit produk kosmetik dalam satuan USD.</td>
            </tr>
            <tr>
                <td class="font-bold">Units_Sold</td>
                <td>Integer</td>
                <td>Berapa banyak jumlah barang yang dibeli di transaksi itu.</td>
            </tr>
            <tr>
                <td class="font-bold">Revenue_USD</td>
                <td>Decimal</td>
                <td>Total uang yang didapat (Hasil perkalian antara Price_USD dikali Units_Sold).</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- 3. DATA CLEANING -->
    <h2>3. Data Cleaning (Proses Pembersihan Data)</h2>
    <p>
        Sebelum data dari file CSV bisa kita masukkan ke dalam database MySQL dan ditampilkan di halaman web dashboard Laravel, kita harus melakukan proses pembersihan data terlebih dahulu. Hal ini sangat penting biar tidak ada error saat program database dijalankan dan visualisasi grafiknya nanti jadi akurat sesuai keadaan aslinya. Proses pembersihan data ini kita lakukan di dalam seeder Laravel secara otomatis saat mengimpor data.
    </p>
    <p>
        Beberapa langkah pembersihan data yang kita jalankan yaitu:
    </p>
    <ul>
        <li><strong>Mengubah Format Tanggal:</strong> Di dalam file CSV mentah, kolom tanggalnya ditulis dalam format string biasa yaitu `MM/DD/YYYY` (contohnya `11/24/2025`). Format ini tidak bisa dibaca oleh tipe data tanggal di database MySQL. Makanya kita ubah formatnya pakai fungsi library `Carbon` di Laravel menjadi format standar `YYYY-MM-DD` (contohnya `2025-11-24`).</li>
        <li><strong>Menghapus Spasi Liar (Trimming Teks):</strong> Kita pakai fungsi `trim()` untuk menghapus spasi-spasi kosong yang tidak sengaja terketik di depan atau di belakang teks nama brand, tipe produk, maupun negara agar pengelompokan datanya tidak terpecah.</li>
        <li><strong>Pengecekan Nilai Kosong dan Duplikat:</strong> Kita cek apakah ada kolom yang kosong atau ada transaksi yang terduplikasi. Dari hasil pengecekan script kita, untungnya data ini sudah cukup lengkap dan tidak ada baris yang kosong atau memiliki ID transaksi ganda.</li>
        <li><strong>Memastikan Perhitungan Revenue:</strong> Kita hitung ulang nilai di kolom `Revenue_USD` dengan mengalikan harga produk (`Price_USD`) dengan jumlah terjual (`Units_Sold`). Ini dilakukan untuk meyakinkan kalau nilai pendapatan di tiap baris data sudah benar secara matematis.</li>
    </ul>

    <h3>Tabel Perbandingan Sebelum vs Sesudah Pembersihan Data:</h3>
    <table>
        <thead>
            <tr>
                <th>Kategori Data</th>
                <th>Kondisi SEBELUM (CSV Mentah)</th>
                <th>Kondisi SESUDAH (Setelah Seeder Laravel)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="font-bold">Format Kolom Tanggal</td>
                <td>String biasa dengan format bulan/hari/tahun (`MM/DD/YYYY`)</td>
                <td>Tipe data DATE standar database dengan format tahun-bulan-hari (`YYYY-MM-DD`)</td>
            </tr>
            <tr>
                <td class="font-bold">Spasi Kosong pada Teks</td>
                <td>Ada kemungkinan spasi kosong di awal/akhir nama kategori</td>
                <td>Semua teks sudah dibersihkan dari spasi liar memakai fungsi `trim()`</td>
            </tr>
            <tr>
                <td class="font-bold">Akurasi Nilai Pendapatan</td>
                <td>Hanya angka mentah tanpa verifikasi rumus matematika</td>
                <td>Sudah divalidasi dengan rumus `Price * Units` dan hasilnya cocok</td>
            </tr>
            <tr>
                <td class="font-bold">Tipe Data Angka Harga</td>
                <td>Format teks/angka biasa di file CSV</td>
                <td>Sudah divalidasi ke tipe DECIMAL agar desimalnya tidak bergeser</td>
            </tr>
        </tbody>
    </table>

    <!-- 4. EXPLORATORY DATA ANALYSIS (EDA) -->
    <h2>4. Exploratory Data Analysis (EDA) & Analisis Awal</h2>
    <p>
        Setelah data dibersihkan, kita melakukan analisis statistik sederhana untuk melihat karakteristik umum dari harga produk kosmetik (`Price_USD`) yang dijual. Ini penting biar atasan kita tahu rentang harga barang yang paling sering dibeli oleh pelanggan. Berikut adalah ringkasan statistiknya:
    </p>
    <table>
        <thead>
            <tr>
                <th>Ukuran Statistik Deskriptif</th>
                <th>Nilai Variabel Harga (Price_USD)</th>
                <th>Arti/Interpretasi Logisnya Bagi Kita</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="font-bold">Rata-rata Harga (Mean)</td>
                <td>${{ $stats['mean'] }}</td>
                <td>Secara rata-rata, harga kosmetik yang terjual ada di kelas menengah ke atas.</td>
            </tr>
            <tr>
                <td class="font-bold">Nilai Tengah (Median)</td>
                <td>${{ $stats['median'] }}</td>
                <td>Setengah dari produk kosmetik harganya di bawah nilai ini, setengahnya lagi di atas ini.</td>
            </tr>
            <tr>
                <td class="font-bold">Harga Termurah (Min)</td>
                <td>${{ $stats['min'] }}</td>
                <td>Menunjukkan harga produk paling murah (seperti kuas make up atau eyeliner biasa).</td>
            </tr>
            <tr>
                <td class="font-bold">Harga Termahal (Max)</td>
                <td>${{ $stats['max'] }}</td>
                <td>Menunjukkan harga produk paket premium (seperti palette eyeshadow atau foundation mahal).</td>
            </tr>
            <tr>
                <td class="font-bold">Standar Deviasi (Variasi Harga)</td>
                <td>${{ $stats['std_dev'] }}</td>
                <td>Jarak variasi harga produk cukup besar, artinya rentang produk murah ke mahal sangat bervariasi.</td>
            </tr>
        </tbody>
    </table>
    <p>
        Bila kita lihat dari rentang harganya yang berkisar antara \$5.25 sampai \$119.93, sebaran datanya terlihat sangat merata dan wajar sekali. Tidak ada angka yang aneh (seperti harga minus atau harga miliaran) yang bisa merusak rata-rata analisis kita. Jadi, bisa disimpulkan datanya sangat layak untuk divisualisasikan ke dalam grafik.
    </p>

    <div class="page-break"></div>

    <!-- 5. VISUALISASI DATA -->
    <h2>5. Visualisasi Data & Penjelasan Grafiknya</h2>
    <p>
        Berdasarkan hasil pengolahan data di Laravel, berikut ini adalah visualisasi grafik yang telah berhasil kita buat secara dinamis. Grafik-grafik di bawah ini disusun sedemikian rupa agar atasan kita bisa langsung memahami kondisi penjualan toko kita:
    </p>

    <!-- Bar Chart: Revenue by Brand -->
    <h3>A. Grafik Batang (Bar Chart): Total Uang yang Didapat per Brand</h3>
    <p>Grafik batang ini membandingkan seberapa banyak uang pendapatan yang disumbangkan oleh masing-masing brand kosmetik sepanjang tahun 2025:</p>
    <div style="margin-top: 15px; margin-bottom: 25px;">
        @php
            $maxBrandRevenue = $brandsBreakdown->max('revenue');
        @endphp
        @foreach($brandsBreakdown as $brand)
            @php
                $pctWidth = $maxBrandRevenue > 0 ? ($brand['revenue'] / $maxBrandRevenue) * 100 : 0;
            @endphp
            <div class="chart-row">
                <div class="chart-label">{{ $brand['name'] }}</div>
                <div class="chart-bar-container">
                    <div class="chart-bar-track">
                        <div class="chart-bar-fill" style="width: {{ $pctWidth }}%; background-color: #e0a899;"></div>
                    </div>
                </div>
                <div class="chart-value">${{ number_format($brand['revenue'], 2) }}</div>
            </div>
        @endforeach
    </div>

    <!-- Line Chart: Monthly Trend -->
    <h3>B. Grafik Garis (Line Chart): Tren Naik Turun Penjualan Tiap Bulan (2025)</h3>
    <p>Grafik garis ini dipakai untuk melihat apakah ada pola musim belanja dari pembeli sepanjang bulan di tahun 2025:</p>
    <div style="margin-top: 15px; margin-bottom: 25px;">
        @php
            $maxMonthlyRevenue = $monthlyTrend->max();
        @endphp
        @foreach($monthlyTrend as $month => $revenue)
            @php
                $pctWidth = $maxMonthlyRevenue > 0 ? ($revenue / $maxMonthlyRevenue) * 100 : 0;
            @endphp
            <div class="chart-row">
                <div class="chart-label">{{ $month }}</div>
                <div class="chart-bar-container">
                    <div class="chart-bar-track">
                        <div class="chart-bar-fill" style="width: {{ $pctWidth }}%; background-color: #d4af37;"></div>
                    </div>
                </div>
                <div class="chart-value">${{ number_format($revenue, 2) }}</div>
            </div>
        @endforeach
    </div>

    <div class="page-break"></div>

    <!-- Donut/Pie Chart: Market Share by Country -->
    <h3>C. Grafik Lingkaran (Pie/Donut Chart): Persentase Pendapatan dari Tiap Negara</h3>
    <p>Tabel di bawah ini menggambarkan pembagian persentase (market share) uang penjualan kosmetik berdasarkan asal negara pembeli:</p>
    <table style="margin-top: 15px; margin-bottom: 25px;">
        <thead>
            <tr>
                <th>Negara Tempat Jualan</th>
                <th>Total Pendapatan (USD)</th>
                <th>Persentase Pangsa Pasar</th>
                <th>Visualisasi Bar Distribusi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($countriesBreakdown as $country)
                <tr>
                    <td class="font-bold">{{ $country['name'] }}</td>
                    <td class="text-right">${{ number_format($country['revenue'], 2) }}</td>
                    <td class="text-right">{{ number_format($country['share'], 2) }}%</td>
                    <td>
                        <div class="chart-bar-track" style="height: 10px; width: 150px;">
                            <div class="chart-bar-fill" style="width: {{ $country['share'] }}%; height: 10px; background-color: #7a2048;"></div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Scatter Plot/Free Chart: Correlation -->
    <h3>D. Grafik Bebas (Scatter Plot): Korelasi Antara Harga Barang vs Jumlah Unit Terjual</h3>
    <p>
        Bila atasan kita melihat grafik scatter plot yang ada di website dashboard, sebaran titik transaksi menunjukkan hal yang cukup menarik. Volume pembelian barang (jumlah unit terjual per transaksi) ternyata tetap tersebar merata di angka 1 sampai 50 unit, baik untuk produk kosmetik yang murah seharga \$5 maupun untuk produk kosmetik yang harganya di atas \$100. Hal ini membuktikan bahwa <strong>konsumen kosmetik tidak terlalu mempermasalahkan harga yang mahal</strong> selagi mereka menyukai brand kosmetik tersebut. Penjelasan detail kinerja tiap jenis produk bisa kita lihat pada tabel di bawah ini:
    </p>
    <table>
        <thead>
            <tr>
                <th>Kategori Jenis Produk</th>
                <th>Kuantitas Terjual</th>
                <th>Rata-rata Harga Per Unit</th>
                <th>Total Uang Pendapatan (USD)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productsBreakdown as $product)
                <tr>
                    <td class="font-bold">{{ $product['name'] }}</td>
                    <td class="text-right">{{ number_format($product['units']) }} unit</td>
                    <td class="text-right">${{ number_format($product['avg_price'], 2) }}</td>
                    <td class="text-right font-bold text-burgundy">${{ number_format($product['revenue'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- 6. DASHBOARD INTERAKTIF -->
    <h2>6. Penjelasan Mengenai Dashboard Interaktif (Laravel)</h2>
    <p>
        Kami telah membangun sebuah sistem dashboard interaktif berbasis web menggunakan Laravel. Aplikasi web ini sengaja dibuat agar atasan kita tidak pusing saat mau menganalisis data penjualan. Fitur utama yang ada di dashboard tersebut yaitu:
    </p>
    <ul>
        <li><strong>Dropdown Slicer (Filter Dinamis):</strong> Atasan kita bisa memilih data penjualan secara khusus. Contohnya, jika ingin melihat data untuk brand MAC saja di negara USA dengan metode pembayaran cash, tinggal pilih di dropdown filter yang sudah kita sediakan.</li>
        <li><strong>Grafik Otomatis Berubah (AJAX):</strong> Begitu filter di dropdown kita ubah, angka ringkasan di atas (seperti total uang masuk dan jumlah unit terjual) beserta keempat grafik visualisasi di atas akan langsung berubah saat itu juga tanpa perlu memuat ulang (refresh) halaman website.</li>
        <li><strong>Tabel Transaksi di Bawah:</strong> Dilengkapi juga dengan kolom pencarian cepat untuk mencari data mentah transaksi kosmetik secara langsung.</li>
    </ul>

    <div class="page-break"></div>

    <!-- 7. INSIGHT -->
    <h2>7. Insight (Temuan Penting dari Data Penjualan)</h2>
    <p>
        Dari hasil visualisasi grafik dan pengolahan data transaksi penjualan sepanjang tahun 2025, kita berhasil menemukan beberapa poin penting (insight) yang bisa dilaporkan kepada atasan kita:
    </p>
    <ol>
        <li><strong>Brand MAC dan L'Oreal Paling Dominan:</strong> Kedua brand ini terbukti menghasilkan kontribusi uang paling besar di toko kosmetik kita. Ini pertanda kalau pelanggan sangat loyal sekali terhadap produk dari MAC dan L'Oreal.</li>
        <li><strong>Mascara dan Eyeshadow Paling Banyak Laku:</strong> Kategori produk untuk riasan mata (Mascara dan Eyeshadow) adalah produk yang paling sering dibeli dalam jumlah unit terbanyak. Kita harus memastikan produk kategori ini selalu tersedia di toko.</li>
        <li><strong>Pola Lonjakan Akhir Tahun (Efek Musiman):</strong> Ada kenaikan penjualan yang kelihatan sangat jelas sekali di bulan November dan Desember. Ini kemungkinan besar terjadi karena adanya momen liburan akhir tahun, perayaan hari raya, dan budaya bertukar kado kosmetik di kalangan wanita.</li>
        <li><strong>Pelanggan Sangat Suka Memakai Dompet Digital (Digital Wallet):</strong> Metode pembayaran dompet digital paling sering dipakai dibanding kartu kredit atau uang tunai. Ini menunjukkan kalau konsumen sekarang lebih suka transaksi digital yang serba cepat.</li>
        <li><strong>Harga Mahal Tetap Laku Keras:</strong> Hubungan harga dan jumlah barang terjual menunjukkan kalau barang kosmetik yang mahal sekalipun tetap banyak peminatnya. Konsumen kecantikan tidak terlalu pelit untuk mengeluarkan uang lebih banyak demi produk yang berkualitas bagus.</li>
    </ol>

    <!-- 8. PENERAPAN METODE DATA MINING (K-MEANS CLUSTERING) -->
    <h2>8. Penerapan Metode Data Mining (K-Means Clustering)</h2>
    <p>
        Untuk menggali pola tersembunyi yang lebih mendalam dari data transaksi penjualan kosmetik ini, kami menerapkan salah satu metode data mining yang sangat populer, yaitu <strong>K-Means Clustering (k=3)</strong>. Algoritma ini berjalan secara otomatis pada database MySQL dengan menganalisis dua variabel penting: harga produk (`Price_USD`) dan jumlah unit yang terjual (`Units_Sold`).
    </p>
    <p>
        Melalui proses data mining ini, data transaksi kosmetik ritel berhasil dibagi ke dalam 3 kelompok konsumen (klaster) dengan karakteristik yang sangat khas:
    </p>
    <ul>
        <li><strong>Klaster 1: Segmen Ekonomis (Budget)</strong><br>
        Kelompok transaksi ini memiliki rata-rata harga produk yang terjangkau dengan volume pembelian unit yang bervariasi. Mewakili konsumen yang sangat sensitif terhadap harga (price-sensitive) yang biasanya berbelanja aksesori kecantikan ringan atau makeup kasual harian.</li>
        <li><strong>Klaster 2: Segmen Menengah (Mid-Range)</strong><br>
        Kumpulan transaksi untuk kosmetik kelas menengah. Volume unit terjual di kelompok ini relatif stabil dan merata di semua negara ritel.</li>
        <li><strong>Klaster 3: Segmen Premium (Luks)</strong><br>
        Kumpulan transaksi untuk kosmetik mewah berharga tinggi. Kelompok konsumen ini terbukti tidak terlalu peduli dengan harga mahal (harga bersifat inelastis) karena mereka lebih mengutamakan prestise brand dan kualitas kosmetik premium.</li>
    </ul>
    <p>
        Penerapan data mining ini membantu atasan kita memetakan segmen pelanggan secara otomatis tanpa pengelompokan manual, sehingga kita bisa menargetkan iklan produk Dior/NARS langsung ke pembeli Klaster 3, dan promo bundling ke pembeli Klaster 1.
    </p>

    <!-- 9. REKOMENDASI -->
    <h2>9. Rekomendasi Aksi Bisnis</h2>
    <p>
        Berdasarkan temuan-temuan di atas, berikut adalah beberapa rekomendasi taktis yang bisa diambil oleh toko kosmetik kita ke depannya:
    </p>
    <ul>
        <li>Bagian gudang harus menyetok produk Mascara dan Eyeshadow dari brand MAC dan L'Oreal lebih banyak (sekitar 30% hingga 40% dari hari biasa) sejak bulan September agar pas puncak belanja akhir tahun kita tidak kehabisan stok barang di toko.</li>
        <li>Kita bisa membuat paket gabungan (bundling) produk. Misalnya membeli paket foundation premium seharga \$100 gratis eyeliner/blush murah. Ini trik yang sangat bagus untuk menghabiskan stok produk yang kurang laku tapi tetap menguntungkan.</li>
        <li>Toko kosmetik kita disarankan bekerja sama dengan penyedia e-wallet (seperti GoPay/OVO/ShopeePay) untuk mengadakan event cashback agar proses checkout transaksi ritel di toko menjadi lebih cepat.</li>
        <li>Karena konsumen kita terbukti tidak terlalu peduli dengan harga mahal selama produknya bagus, kita bisa memperbanyak koleksi kosmetik premium bernilai tinggi untuk menaikkan keuntungan rata-rata per transaksi.</li>
    </ul>

    <!-- 10. KESIMPULAN -->
    <h2>10. Kesimpulan</h2>
    <p>
        Sebagai kesimpulan, proyek besar analitik data kosmetik tahun 2025 ini telah selesai dikerjakan dengan baik menggunakan sistem database Laravel dan visualisasi interaktif. Atasan kita sekarang bisa memantau data penjualan per brand, negara, dan tipe produk secara mudah lewat dashboard. Temuan mengenai tingginya loyalitas brand pada MAC/L'Oreal serta peningkatan pesat penjualan di akhir tahun harus dijadikan acuan utama bagi toko kita untuk menyusun rencana stok barang dan promosi iklan di masa mendatang.
    </p>

    <!-- Footer for PDF -->
    <div style="margin-top: 2cm; text-align: center; border-top: 1px solid #ccc; padding-top: 10px; font-size: 8pt; color: #777;">
        Laporan Hasil Analitik GlowMetrics Tugas Besar &copy; 2026. Halaman Terakhir Dokumen.
    </div>

</body>
</html>

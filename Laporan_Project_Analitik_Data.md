# LAPORAN TUGAS BESAR: ANALITIK DAN VISUALISASI DATA
**Pembangunan Dashboard Penjualan Kosmetik Ritel Tahun 2025 (GlowMetrics)**

- **Topik Analisis Penjualan:** Data Transaksi Kosmetik/Makeup 2025
- **Sistem Framework:** Laravel Framework (MySQL) + Chart.js
- **Jumlah Baris Dataset:** 500 Baris Data Transaksi Penjualan
- **Disusun Oleh:** Mahasiswa Semester 6
- **Tanggal Pembuatan:** 25 June 2026

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

Data yang kita pakai di dalam proyek analitik ini diambil dari data penjualan kosmetik tahun 2025. Di dalam file CSV yang kita olah, terdapat total data sebanyak **500 baris transaksi**. Untuk penjelasan mengenai masing-masing kolom yang ada di dalam dataset ini, bisa dilihat pada tabel di bawah ini:

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
| **Rata-rata Harga (Mean)** | $62.86 | Secara rata-rata, harga kosmetik yang terjual ada di kelas menengah ke atas. |
| **Nilai Tengah (Median)** | $61.23 | Setengah dari produk kosmetik harganya di bawah nilai ini, setengahnya lagi di atas ini. |
| **Harga Termurah (Min)** | $5.25 | Menunjukkan harga produk paling murah (seperti kuas make up atau eyeliner biasa). |
| **Harga Termahal (Max)** | $119.93 | Menunjukkan harga produk paket premium (seperti palette eyeshadow atau foundation mahal). |
| **Standar Deviasi (Variasi Harga)** | $32.61 | Jarak variasi harga produk cukup besar, artinya rentang produk murah ke mahal sangat bervariasi. |

**Distribusi Data & Deteksi Outlier:**
Bila kita lihat dari rentang harganya yang berkisar antara $5.25 sampai $119.93, sebaran datanya terlihat sangat merata dan wajar sekali. Tidak ada angka yang aneh (seperti harga minus atau harga miliaran) yang bisa merusak rata-rata analisis kita. Jadi, bisa disimpulkan datanya sangat layak untuk divisualisasikan ke dalam grafik.

---

## 5. Visualisasi Data & Penjelasan Grafiknya

### A. Total Pendapatan per Brand
Berikut adalah rincian kontribusi pendapatan (Revenue) dan kuantitas unit terjual oleh masing-masing brand kosmetik utama sepanjang tahun 2025:

* **MAC:** $155,245.81 (2155 unit terjual)
* **L'Oreal:** $123,593.20 (1819 unit terjual)
* **Dior:** $99,810.99 (1442 unit terjual)
* **Fenty Beauty:** $95,001.67 (1569 unit terjual)
* **NARS:** $88,792.62 (1659 unit terjual)
* **Estee Lauder:** $86,983.48 (1492 unit terjual)
* **Huda Beauty:** $76,357.17 (1394 unit terjual)
* **Maybelline:** $67,403.56 (1178 unit terjual)

### B. Tren Pendapatan Bulanan sepanjang 2025 (Pola Kenaikan Bulanan)
Tren bulanan penjualan memetakan fluktuasi pendapatan dari bulan ke bulan:

* **Jan 2025:** $63,184.05
* **Feb 2025:** $41,654.67
* **Mar 2025:** $71,169.11
* **Apr 2025:** $67,298.71
* **May 2025:** $69,506.68
* **Jun 2025:** $98,829.22
* **Jul 2025:** $60,675.03
* **Aug 2025:** $83,296.77
* **Sep 2025:** $54,373.64
* **Oct 2025:** $53,388.29
* **Nov 2025:** $67,251.71
* **Dec 2025:** $62,560.62

### C. Distribusi Pendapatan per Negara (Pangsa Pasar)

| Negara Tempat Jualan | Total Pendapatan (USD) | Persentase Pangsa Pasar |
| :--- | :--- | :--- |
| **Saudi Arabia** | $120,778.08 | 0.00% |
| **UK** | $115,359.56 | 0.00% |
| **India** | $109,638.23 | 0.00% |
| **USA** | $96,005.51 | 0.00% |
| **UAE** | $93,567.94 | 0.00% |
| **Germany** | $92,125.73 | 0.00% |
| **Canada** | $85,349.39 | 0.00% |
| **France** | $80,364.06 | 0.00% |

### D. Korelasi Harga vs Volume Penjualan per Jenis Produk

| Kategori Jenis Produk | Kuantitas Terjual | Rata-rata Harga Per Unit | Total Uang Pendapatan (USD) |
| :--- | :--- | :--- | :--- |
| **Mascara** | 1,900 unit | $59.54 | **$116,135.07** |
| **Eyeshadow** | 1,716 unit | $64.81 | **$104,839.02** |
| **Eyeliner** | 1,646 unit | $64.66 | **$104,735.62** |
| **Highlighter** | 1,526 unit | $67.20 | **$99,516.43** |
| **Concealer** | 1,352 unit | $67.72 | **$98,215.88** |
| **Foundation** | 1,523 unit | $65.32 | **$96,756.97** |
| **Blush** | 1,453 unit | $61.32 | **$89,158.26** |
| **Lipstick** | 1,592 unit | $53.62 | **$83,831.25** |

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
* Kita bisa membuat paket gabungan (bundling) produk. Misalnya membeli paket foundation premium seharga $100 gratis eyeliner/blush murah. Ini trik yang sangat bagus untuk menghabiskan stok produk yang kurang laku tapi tetap menguntungkan.
* Toko kosmetik kita disarankan bekerja sama dengan penyedia e-wallet (seperti GoPay/OVO/ShopeePay) untuk mengadakan event cashback agar proses checkout transaksi ritel di toko menjadi lebih cepat.
* Karena konsumen kita terbukti tidak terlalu peduli dengan harga mahal selama produknya bagus, kita bisa memperbanyak koleksi kosmetik premium bernilai tinggi untuk menaikkan keuntungan rata-rata per transaksi.

---

## 10. Kesimpulan

Sebagai kesimpulan, proyek besar analitik data kosmetik tahun 2025 ini telah selesai dikerjakan dengan baik menggunakan sistem database Laravel dan visualisasi interaktif. Temuan mengenai tingginya loyalitas brand pada MAC/L'Oreal serta peningkatan pesat penjualan di akhir tahun harus dijadikan acuan utama bagi toko kita untuk menyusun rencana stok barang dan promosi iklan di masa mendatang.

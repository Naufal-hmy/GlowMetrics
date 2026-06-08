<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GlowMetrics - Dashboard Analitik Penjualan Kosmetik</title>
    <meta name="description" content="Dashboard analitik interaktif untuk visualisasi data penjualan kosmetik premium tahun 2025.">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom Style -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
    <div class="glass-bg"></div>

    <header class="app-header">
        <div class="logo-area">
            <div class="logo-text">
                <h1 id="app-title">GlowMetrics</h1>
                <p>Cosmetics Sales Intelligence</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ url('/export-pdf') }}" id="btn-export-pdf" class="btn btn-primary" download>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Unduh Laporan PDF
            </a>
        </div>
    </header>

    <main class="main-container">
        <!-- Tab Navigation Bar -->
        <div class="tab-navigation">
            <button type="button" class="tab-btn active" id="btn-tab-analytics" onclick="switchTab('analytics')">📊 Analisis Dashboard</button>
            <button type="button" class="tab-btn" id="btn-tab-mining" onclick="switchTab('mining')">🔍 Data Mining (K-Means)</button>
        </div>

        <!-- VIEW 1: STANDARD ANALYTICS -->
        <div id="view-analytics">
            <!-- Filter Panel -->
            <section class="filter-panel" aria-label="Slicers Penjualan" style="margin-top: 1rem;">
                <h2 class="section-title">Filter & Slicers</h2>
                <div class="filter-grid">
                    <!-- Brand Filter -->
                    <div class="filter-group">
                        <label>Brand</label>
                        <div class="custom-select-wrapper" id="wrapper-brand">
                            <div class="custom-select-trigger" onclick="toggleDropdown('dropdown-brand', event)">
                                <span class="trigger-text" id="text-brand">Semua Brand</span>
                                <span class="arrow">▼</span>
                            </div>
                            <div class="custom-select-options" id="dropdown-brand">
                                <div class="option-item">
                                    <input type="checkbox" id="brand-all" class="select-all-checkbox" value="all" checked onchange="toggleSelectAll('brand')">
                                    <label for="brand-all">Semua Brand</label>
                                </div>
                                <hr class="dropdown-divider">
                                @foreach($brands as $brand)
                                    <div class="option-item">
                                        <input type="checkbox" id="brand-{{ $loop->index }}" value="{{ $brand }}" class="filter-checkbox checkbox-brand" onchange="onCheckboxChange('brand')">
                                        <label for="brand-{{ $loop->index }}">{{ $brand }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Product Type Filter -->
                    <div class="filter-group">
                        <label>Jenis Produk</label>
                        <div class="custom-select-wrapper" id="wrapper-product-type">
                            <div class="custom-select-trigger" onclick="toggleDropdown('dropdown-product-type', event)">
                                <span class="trigger-text" id="text-product-type">Semua Produk</span>
                                <span class="arrow">▼</span>
                            </div>
                            <div class="custom-select-options" id="dropdown-product-type">
                                <div class="option-item">
                                    <input type="checkbox" id="product-type-all" class="select-all-checkbox" value="all" checked onchange="toggleSelectAll('product-type')">
                                    <label for="product-type-all">Semua Produk</label>
                                </div>
                                <hr class="dropdown-divider">
                                @foreach($productTypes as $type)
                                    <div class="option-item">
                                        <input type="checkbox" id="product-type-{{ $loop->index }}" value="{{ $type }}" class="filter-checkbox checkbox-product-type" onchange="onCheckboxChange('product-type')">
                                        <label for="product-type-{{ $loop->index }}">{{ $type }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Country Filter -->
                    <div class="filter-group">
                        <label>Negara</label>
                        <div class="custom-select-wrapper" id="wrapper-country">
                            <div class="custom-select-trigger" onclick="toggleDropdown('dropdown-country', event)">
                                <span class="trigger-text" id="text-country">Semua Negara</span>
                                <span class="arrow">▼</span>
                            </div>
                            <div class="custom-select-options" id="dropdown-country">
                                <div class="option-item">
                                    <input type="checkbox" id="country-all" class="select-all-checkbox" value="all" checked onchange="toggleSelectAll('country')">
                                    <label for="country-all">Semua Negara</label>
                                </div>
                                <hr class="dropdown-divider">
                                @foreach($countries as $country)
                                    <div class="option-item">
                                        <input type="checkbox" id="country-{{ $loop->index }}" value="{{ $country }}" class="filter-checkbox checkbox-country" onchange="onCheckboxChange('country')">
                                        <label for="country-{{ $loop->index }}">{{ $country }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Sales Channel Filter -->
                    <div class="filter-group">
                        <label>Saluran Penjualan</label>
                        <div class="custom-select-wrapper" id="wrapper-channel">
                            <div class="custom-select-trigger" onclick="toggleDropdown('dropdown-channel', event)">
                                <span class="trigger-text" id="text-channel">Semua Saluran</span>
                                <span class="arrow">▼</span>
                            </div>
                            <div class="custom-select-options" id="dropdown-channel">
                                <div class="option-item">
                                    <input type="checkbox" id="channel-all" class="select-all-checkbox" value="all" checked onchange="toggleSelectAll('channel')">
                                    <label for="channel-all">Semua Saluran</label>
                                </div>
                                <hr class="dropdown-divider">
                                @foreach($salesChannels as $channel)
                                    <div class="option-item">
                                        <input type="checkbox" id="channel-{{ $loop->index }}" value="{{ $channel }}" class="filter-checkbox checkbox-channel" onchange="onCheckboxChange('channel')">
                                        <label for="channel-{{ $loop->index }}">{{ $channel }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Filter -->
                    <div class="filter-group">
                        <label>Metode Pembayaran</label>
                        <div class="custom-select-wrapper" id="wrapper-payment">
                            <div class="custom-select-trigger" onclick="toggleDropdown('dropdown-payment', event)">
                                <span class="trigger-text" id="text-payment">Semua Metode</span>
                                <span class="arrow">▼</span>
                            </div>
                            <div class="custom-select-options" id="dropdown-payment">
                                <div class="option-item">
                                    <input type="checkbox" id="payment-all" class="select-all-checkbox" value="all" checked onchange="toggleSelectAll('payment')">
                                    <label for="payment-all">Semua Metode</label>
                                </div>
                                <hr class="dropdown-divider">
                                @foreach($paymentMethods as $payment)
                                    <div class="option-item">
                                        <input type="checkbox" id="payment-{{ $loop->index }}" value="{{ $payment }}" class="filter-checkbox checkbox-payment" onchange="onCheckboxChange('payment')">
                                        <label for="payment-{{ $loop->index }}">{{ $payment }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Date Range Filter -->
                    <div class="filter-group date-group">
                        <label for="filter-date-start">Mulai</label>
                        <input type="date" id="filter-date-start" name="date_start" class="filter-input" min="{{ $minDate }}" max="{{ $maxDate }}" value="{{ $minDate }}">
                    </div>

                    <div class="filter-group date-group">
                        <label for="filter-date-end">Selesai</label>
                        <input type="date" id="filter-date-end" name="date_end" class="filter-input" min="{{ $minDate }}" max="{{ $maxDate }}" value="{{ $maxDate }}">
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="button" id="btn-reset" class="btn btn-secondary">
                        Reset Filter
                    </button>
                </div>
            </section>

            <!-- KPI Metrics Summary -->
            <section class="metrics-grid" aria-label="KPI Utama" style="margin-top: 1.5rem;">
                <!-- KPI 1 -->
                <div class="metric-card" id="kpi-revenue-card">
                    <div class="metric-info">
                        <h3>Total Pendapatan</h3>
                        <p class="metric-value" id="kpi-revenue">$0.00</p>
                    </div>
                    <div class="metric-icon bg-gold">💰</div>
                </div>

                <!-- KPI 2 -->
                <div class="metric-card" id="kpi-units-card">
                    <div class="metric-info">
                        <h3>Unit Terjual</h3>
                        <p class="metric-value" id="kpi-units">0</p>
                    </div>
                    <div class="metric-icon bg-pink">🛍️</div>
                </div>

                <!-- KPI 3 -->
                <div class="metric-card" id="kpi-price-card">
                    <div class="metric-info">
                        <h3>Rata-rata Harga</h3>
                        <p class="metric-value" id="kpi-price">$0.00</p>
                    </div>
                    <div class="metric-icon bg-purple">🏷️</div>
                </div>

                <!-- KPI 4 -->
                <div class="metric-card" id="kpi-sales-card">
                    <div class="metric-info">
                        <h3>Jumlah Transaksi</h3>
                        <p class="metric-value" id="kpi-sales">0</p>
                    </div>
                    <div class="metric-icon bg-blue">📊</div>
                </div>
            </section>

            <!-- Charts Grid Section -->
            <section class="charts-grid" aria-label="Visualisasi Grafik" style="margin-top: 1.5rem;">
                <!-- Bar Chart Card -->
                <div class="chart-card">
                    <h3>Pendapatan per Brand ($)</h3>
                    <div class="chart-container">
                        <canvas id="chart-brand"></canvas>
                    </div>
                </div>

                <!-- Line Chart Card -->
                <div class="chart-card">
                    <h3>Tren Pendapatan Bulanan sepanjang 2025</h3>
                    <div class="chart-container">
                        <canvas id="chart-monthly"></canvas>
                    </div>
                </div>

                <!-- Donut Chart Card -->
                <div class="chart-card">
                    <h3>Distribusi Pendapatan per Negara</h3>
                    <div class="chart-container">
                        <canvas id="chart-country"></canvas>
                    </div>
                </div>

                <!-- Scatter Plot Card -->
                <div class="chart-card">
                    <h3>Korelasi Harga vs Volume Penjualan (Berdasarkan Brand)</h3>
                    <div class="chart-container">
                        <canvas id="chart-scatter"></canvas>
                    </div>
                </div>
            </section>

            <!-- Data Table Section -->
            <section class="data-table-section" aria-label="Detail Transaksi" style="margin-top: 1.5rem;">
                <div class="table-header">
                    <h3>Detail Transaksi Penjualan</h3>
                    <div class="table-search">
                        <input type="text" id="table-search-input" placeholder="Cari brand, negara, produk..." class="search-input">
                    </div>
                </div>
                <div class="table-wrapper">
                    <table id="sales-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tanggal</th>
                                <th>Brand</th>
                                <th>Produk</th>
                                <th>Negara</th>
                                <th>Saluran</th>
                                <th>Pembayaran</th>
                                <th class="text-right">Harga (USD)</th>
                                <th class="text-right">Unit</th>
                                <th class="text-right">Revenue (USD)</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <!-- Filled by JS -->
                        </tbody>
                    </table>
                </div>
                <div class="table-footer">
                    <div class="pagination-info" id="pagination-info">
                        Menampilkan 0 - 0 dari 0 baris
                    </div>
                    <div class="pagination-buttons">
                        <button type="button" id="btn-prev-page" class="btn btn-pagination">Sebelumnya</button>
                        <button type="button" id="btn-next-page" class="btn btn-pagination">Berikutnya</button>
                    </div>
                </div>
            </section>
        </div>

        <!-- VIEW 2: DATA MINING (K-MEANS) -->
        <div id="view-mining" style="display: none;">
            <section class="filter-panel" aria-label="Penjelasan Data Mining" style="margin-top: 1rem;">
                <h2 class="section-title">Segmentasi Pelanggan (K-Means Clustering)</h2>
                <p style="color: var(--text-secondary); margin-bottom: 0; text-indent: 0; line-height: 1.6;">
                    Di bagian ini, kita menjalankan algoritma data mining <strong>K-Means Clustering (k=3)</strong> secara langsung pada database MySQL untuk membagi 500 transaksi penjualan kosmetik ke dalam 3 kelompok konsumen (klaster) berdasarkan kombinasi <strong>Harga Produk (Price_USD)</strong> dan <strong>Jumlah Unit yang Dibeli (Units_Sold)</strong>. Metode ini berguna untuk memetakan kelompok pembeli kosmetik kita secara otomatis demi memudahkan atasan menyusun strategi pemasaran yang relevan.
                </p>
            </section>

            <!-- Centroid Summaries Grid -->
            <div class="metrics-grid" id="mining-summary-grid" style="margin-top: 1.5rem; margin-bottom: 1.5rem;">
                <!-- Filled dynamically by JS -->
            </div>

            <!-- Mining Chart Card -->
            <div class="chart-card" style="margin-bottom: 1.5rem;">
                <h3>Peta Klaster Hasil Data Mining (k=3)</h3>
                <p style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 1.25rem;">
                    Titik-titik di bawah ini dikelompokkan menjadi 3 warna mewakili klaster masing-masing. Simbol kotak berlian putih (◇) menandakan posisi tengah (Centroid) dari masing-masing klaster.
                </p>
                <div class="chart-container" style="height: 450px;">
                    <canvas id="chart-mining-scatter"></canvas>
                </div>
            </div>
        </div>
    </main>

    <footer class="app-footer">
        <p>&copy; 2026 GlowMetrics. Proyek Besar Analitik Data - Dashboard Laravel.</p>
    </footer>

    <!-- Frontend logic for charts and interaction -->
    <script>
        // Track Chart instances to destroy before rendering new ones
        let chartBrand = null;
        let chartMonthly = null;
        let chartCountry = null;
        let chartScatter = null;
        let chartMiningScatter = null;

        // Transaction table pagination state
        let allTransactions = [];
        let filteredTransactions = [];
        let currentPage = 1;
        const rowsPerPage = 10;

        // Custom select dropdown actions
        function toggleDropdown(id, event) {
            event.stopPropagation();
            const dropdown = document.getElementById(id);
            const trigger = dropdown.previousElementSibling;
            
            // Close other dropdowns
            document.querySelectorAll('.custom-select-options').forEach(el => {
                if (el.id !== id) {
                    el.classList.remove('show');
                    el.previousElementSibling.classList.remove('active');
                }
            });

            dropdown.classList.toggle('show');
            trigger.classList.toggle('active');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', (event) => {
            if (!event.target.closest('.custom-select-wrapper')) {
                document.querySelectorAll('.custom-select-options').forEach(el => {
                    el.classList.remove('show');
                    el.previousElementSibling.classList.remove('active');
                });
            }
        });

        // Toggle select-all state
        function toggleSelectAll(type) {
            const allCheckbox = document.getElementById(`${type}-all`);
            const checkboxes = document.querySelectorAll(`.checkbox-${type}`);
            
            if (allCheckbox.checked) {
                checkboxes.forEach(cb => cb.checked = false);
            } else {
                const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
                if (checkedCount === 0) {
                    allCheckbox.checked = true;
                }
            }
            updateTriggerText(type);
            currentPage = 1;
            fetchData();
        }

        // Handle specific checkbox change
        function onCheckboxChange(type) {
            const allCheckbox = document.getElementById(`${type}-all`);
            const checkboxes = document.querySelectorAll(`.checkbox-${type}`);
            const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;

            if (checkedCount > 0) {
                allCheckbox.checked = false;
            } else {
                allCheckbox.checked = true;
            }
            updateTriggerText(type);
            currentPage = 1;
            fetchData();
        }

        // Update trigger display text
        function updateTriggerText(type) {
            const allCheckbox = document.getElementById(`${type}-all`);
            const checkboxes = document.querySelectorAll(`.checkbox-${type}`);
            const checkedBoxes = Array.from(checkboxes).filter(cb => cb.checked);
            const textSpan = document.getElementById(`text-${type}`);
            
            const labelMap = {
                'brand': 'Brand',
                'product-type': 'Produk',
                'country': 'Negara',
                'channel': 'Saluran',
                'payment': 'Metode'
            };
            const labelSingular = labelMap[type] || '';

            if (allCheckbox.checked || checkedBoxes.length === 0) {
                textSpan.textContent = `Semua ${labelSingular}`;
            } else if (checkedBoxes.length === 1) {
                textSpan.textContent = checkedBoxes[0].value;
            } else if (checkedBoxes.length <= 2) {
                textSpan.textContent = checkedBoxes.map(cb => cb.value).join(', ');
            } else {
                textSpan.textContent = `${checkedBoxes.length} ${labelSingular} Terpilih`;
            }
        }

        // Document Ready
        document.addEventListener('DOMContentLoaded', () => {
            // Initial fetch
            fetchData();

            // Set up date filter change listeners
            ['filter-date-start', 'filter-date-end'].forEach(id => {
                document.getElementById(id).addEventListener('change', () => {
                    currentPage = 1; // reset page on date change
                    fetchData();
                });
            });

            // Reset Button
            document.getElementById('btn-reset').addEventListener('click', () => {
                const types = ['brand', 'product-type', 'country', 'channel', 'payment'];
                types.forEach(type => {
                    document.getElementById(`${type}-all`).checked = true;
                    document.querySelectorAll(`.checkbox-${type}`).forEach(cb => cb.checked = false);
                    updateTriggerText(type);
                });
                document.getElementById('filter-date-start').value = "{{ $minDate }}";
                document.getElementById('filter-date-end').value = "{{ $maxDate }}";
                currentPage = 1;
                fetchData();
            });

            // Search Table Input
            document.getElementById('table-search-input').addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase();
                filteredTransactions = allTransactions.filter(item => {
                    return item.brand.toLowerCase().includes(query) ||
                           item.product_type.toLowerCase().includes(query) ||
                           item.country.toLowerCase().includes(query) ||
                           item.sales_channel.toLowerCase().includes(query) ||
                           item.payment_method.toLowerCase().includes(query) ||
                           String(item.sale_id).includes(query);
                });
                currentPage = 1;
                renderTable();
            });

            // Pagination Buttons
            document.getElementById('btn-prev-page').addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    renderTable();
                }
            });

            document.getElementById('btn-next-page').addEventListener('click', () => {
                const maxPage = Math.ceil(filteredTransactions.length / rowsPerPage);
                if (currentPage < maxPage) {
                    currentPage++;
                    renderTable();
                }
            });
        });

        // Fetch analytical data from Laravel backend API
        function fetchData() {
            const getCheckedValues = (type) => {
                const allCheckbox = document.getElementById(`${type}-all`);
                if (allCheckbox.checked) return [];
                return Array.from(document.querySelectorAll(`.checkbox-${type}:checked`)).map(cb => cb.value);
            };

            const selectedBrands = getCheckedValues('brand');
            const selectedProductTypes = getCheckedValues('product-type');
            const selectedCountries = getCheckedValues('country');
            const selectedChannels = getCheckedValues('channel');
            const selectedPayments = getCheckedValues('payment');
            
            const date_start = document.getElementById('filter-date-start').value;
            const date_end = document.getElementById('filter-date-end').value;

            // Build query params
            const params = new URLSearchParams();
            selectedBrands.forEach(val => params.append('brand[]', val));
            selectedProductTypes.forEach(val => params.append('product_type[]', val));
            selectedCountries.forEach(val => params.append('country[]', val));
            selectedChannels.forEach(val => params.append('sales_channel[]', val));
            selectedPayments.forEach(val => params.append('payment_method[]', val));
            
            if (date_start) params.append('date_start', date_start);
            if (date_end) params.append('date_end', date_end);

            const queryString = params.toString();

            // Dynamic Update PDF Export Button link with filter params
            const exportButton = document.getElementById('btn-export-pdf');
            exportButton.href = "{{ url('/export-pdf') }}" + (queryString ? '?' + queryString : '');

            // Fetch
            fetch(`/api/sales-data?${queryString}`)
                .then(response => response.json())
                .then(data => {
                    updateKPIs(data.kpis);
                    renderCharts(data.charts);
                    
                    // Setup data for table
                    allTransactions = data.transactions;
                    
                    // Apply existing search filter if present
                    const searchQuery = document.getElementById('table-search-input').value.toLowerCase();
                    if (searchQuery) {
                        filteredTransactions = allTransactions.filter(item => {
                            return item.brand.toLowerCase().includes(searchQuery) ||
                                   item.product_type.toLowerCase().includes(searchQuery) ||
                                   item.country.toLowerCase().includes(searchQuery) ||
                                   item.sales_channel.toLowerCase().includes(searchQuery) ||
                                   item.payment_method.toLowerCase().includes(searchQuery);
                        });
                    } else {
                        filteredTransactions = allTransactions;
                    }

                    renderTable();
                })
                .catch(err => console.error('Error fetching data:', err));
        }

        // Update KPI summary cards
        function updateKPIs(kpis) {
            document.getElementById('kpi-revenue').textContent = formatCurrency(kpis.total_revenue);
            document.getElementById('kpi-units').textContent = formatNumber(kpis.total_units);
            document.getElementById('kpi-price').textContent = formatCurrency(kpis.avg_price);
            document.getElementById('kpi-sales').textContent = formatNumber(kpis.total_sales);
        }

        // Render Charts using Chart.js
        function renderCharts(chartData) {
            // Theme colors matching premium rose gold/dark design
            const goldColor = '#D4AF37';
            const roseColor = '#E0A899';
            const burgundyColor = '#7A2048';

            // 1. Bar Chart: Revenue by Brand (or Revenue by Product Type)
            if (chartBrand) chartBrand.destroy();
            
            // Dynamically update the chart title based on selected brand filter
            const selectedBrands = Array.from(document.querySelectorAll('.checkbox-brand:checked')).map(cb => cb.value);
            const isSingleBrand = (selectedBrands.length === 1);
            let barChartTitle = 'Pendapatan per Brand ($)';
            if (isSingleBrand) {
                barChartTitle = `Pendapatan per Jenis Produk dari Brand "${selectedBrands[0]}" ($)`;
            } else if (selectedBrands.length > 1) {
                barChartTitle = `Perbandingan Pendapatan Antar Brand Terpilih ($)`;
            }
            document.getElementById('chart-brand').closest('.chart-card').querySelector('h3').textContent = barChartTitle;

            const brandLabels = chartData.brand.map(item => item.brand);
            const brandRevenues = chartData.brand.map(item => parseFloat(item.revenue));

            // Color mappings for dynamic visual sync
            const brandColors = {
                'MAC': '#6e4a84',
                'L\'Oreal': '#d4af37',
                'Fenty Beauty': '#e0a899',
                'NARS': '#7a2048',
                'Dior': '#c15c3d',
                'Estee Lauder': '#4a6984',
                'Huda Beauty': '#4a846e',
                'Maybelline': '#84734a'
            };

            const productColors = {
                'Eyeliner': '#4a6984',
                'Highlighter': '#d4af37',
                'Lipstick': '#e0a899',
                'Concealer': '#6e4a84',
                'Blush': '#7a2048',
                'Mascara': '#c15c3d',
                'Eyeshadow': '#4a846e',
                'Foundation': '#84734a'
            };

            const barBackgroundColors = brandLabels.map(label => {
                if (isSingleBrand) {
                    return (productColors[label] || '#e0a899') + 'cc';
                } else {
                    return (brandColors[label] || '#e0a899') + 'cc';
                }
            });

            const barBorderColors = brandLabels.map(label => {
                if (isSingleBrand) {
                    return productColors[label] || '#e0a899';
                } else {
                    return brandColors[label] || '#e0a899';
                }
            });

            const ctxBrand = document.getElementById('chart-brand').getContext('2d');
            chartBrand = new Chart(ctxBrand, {
                type: 'bar',
                data: {
                    labels: brandLabels,
                    datasets: [
                        {
                            label: 'Pendapatan ($)',
                            data: brandRevenues,
                            backgroundColor: barBackgroundColors,
                            borderColor: barBorderColors,
                            borderWidth: 1.5,
                            borderRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Pendapatan: ' + formatCurrency(context.raw);
                                }
                            }
                        }
                    },
                    scales: {
                        x: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#E0E0E0' } },
                        y: { 
                            grid: { color: 'rgba(255, 255, 255, 0.05)' }, 
                            ticks: { 
                                color: '#E0E0E0',
                                callback: function(value) { return '$' + value; }
                            } 
                        }
                    }
                }
            });

            // 2. Line Chart: Monthly Revenue
            if (chartMonthly) chartMonthly.destroy();
            const monthlyLabels = chartData.monthly.map(item => item.month_name);
            const monthlyRevenues = chartData.monthly.map(item => parseFloat(item.revenue));

            const ctxMonthly = document.getElementById('chart-monthly').getContext('2d');
            chartMonthly = new Chart(ctxMonthly, {
                type: 'line',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: 'Pendapatan',
                        data: monthlyRevenues,
                        borderColor: '#D4AF37',
                        backgroundColor: 'rgba(212, 175, 55, 0.15)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 3,
                        pointBackgroundColor: '#D4AF37',
                        pointBorderColor: '#fff',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#E0E0E0' } },
                        y: { 
                            grid: { color: 'rgba(255, 255, 255, 0.05)' }, 
                            ticks: { 
                                color: '#E0E0E0',
                                callback: function(value) { return '$' + value; }
                            } 
                        }
                    }
                }
            });

            // 3. Donut Chart: Revenue by Country
            if (chartCountry) chartCountry.destroy();
            const countryLabels = chartData.country.map(item => item.country);
            const countryRevenues = chartData.country.map(item => parseFloat(item.revenue));

            const ctxCountry = document.getElementById('chart-country').getContext('2d');
            chartCountry = new Chart(ctxCountry, {
                type: 'doughnut',
                data: {
                    labels: countryLabels,
                    datasets: [{
                        data: countryRevenues,
                        backgroundColor: [
                            '#E0A899', '#D4AF37', '#7A2048', '#C15C3D', 
                            '#4A6984', '#6E4A84', '#4A846E', '#84734A'
                        ],
                        borderWidth: 2,
                        borderColor: '#181528'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                color: '#E0E0E0',
                                font: { family: 'Outfit', size: 11 }
                            }
                        }
                    }
                }
            });

            // 4. Scatter Plot: Price vs Units Sold
            if (chartScatter) chartScatter.destroy();
            
            // Dynamically update the scatter plot title based on selected brand filter
            let scatterChartTitle = 'Korelasi Harga vs Volume Penjualan (Berdasarkan Brand)';
            let scatterDatasets = [];

            if (isSingleBrand) {
                scatterChartTitle = `Korelasi Harga vs Volume Penjualan dari Brand "${selectedBrands[0]}" (Berdasarkan Jenis Produk)`;
                const scatterProducts = [...new Set(chartData.scatter.map(item => item.product_type))];
                
                scatterDatasets = scatterProducts.map(productName => {
                    const productPoints = chartData.scatter
                        .filter(item => item.product_type === productName)
                        .map(item => ({
                            x: parseFloat(item.x),
                            y: parseInt(item.y),
                            brand: item.brand
                        }));

                    const color = productColors[productName] || '#fff';
                    return {
                        label: productName,
                        data: productPoints,
                        backgroundColor: color + 'bf', // opacity
                        borderColor: color,
                        pointRadius: 6,
                        pointHoverRadius: 9,
                        borderWidth: 1
                    };
                });
            } else {
                const scatterBrands = [...new Set(chartData.scatter.map(item => item.brand))];
                scatterDatasets = scatterBrands.map(brandName => {
                    const brandPoints = chartData.scatter
                        .filter(item => item.brand === brandName)
                        .map(item => ({
                            x: parseFloat(item.x),
                            y: parseInt(item.y),
                            product_type: item.product_type
                        }));

                    const color = brandColors[brandName] || '#fff';
                    return {
                        label: brandName,
                        data: brandPoints,
                        backgroundColor: color + 'bf', // opacity
                        borderColor: color,
                        pointRadius: 6,
                        pointHoverRadius: 9,
                        borderWidth: 1
                    };
                });
            }

            document.getElementById('chart-scatter').closest('.chart-card').querySelector('h3').textContent = scatterChartTitle;

            const ctxScatter = document.getElementById('chart-scatter').getContext('2d');
            chartScatter = new Chart(ctxScatter, {
                type: 'scatter',
                data: {
                    datasets: scatterDatasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                color: '#E0E0E0',
                                font: { family: 'Outfit', size: 10 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const point = context.raw;
                                    if (isSingleBrand) {
                                        return `${point.brand} (${context.dataset.label}): Harga $${point.x}, Terjual ${point.y} unit`;
                                    } else {
                                        return `${context.dataset.label} (${point.product_type}): Harga $${point.x}, Terjual ${point.y} unit`;
                                    }
                                }
                            }
                        }
                    },
                    scales: {
                        x: { 
                            title: { display: true, text: 'Harga Produk (USD)', color: '#E0E0E0' },
                            grid: { color: 'rgba(255, 255, 255, 0.05)' }, 
                            ticks: { color: '#E0E0E0' } 
                        },
                        y: { 
                            title: { display: true, text: 'Unit Terjual', color: '#E0E0E0' },
                            grid: { color: 'rgba(255, 255, 255, 0.05)' }, 
                            ticks: { color: '#E0E0E0' } 
                        }
                    }
                }
            });
        }

        // Render paginated transaction table
        function renderTable() {
            const tableBody = document.getElementById('table-body');
            tableBody.innerHTML = '';

            const startIdx = (currentPage - 1) * rowsPerPage;
            const endIdx = startIdx + rowsPerPage;
            const pageItems = filteredTransactions.slice(startIdx, endIdx);

            if (pageItems.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="10" class="text-center">Tidak ada transaksi ditemukan</td></tr>`;
                document.getElementById('pagination-info').textContent = 'Menampilkan 0 dari 0 baris';
                document.getElementById('btn-prev-page').disabled = true;
                document.getElementById('btn-next-page').disabled = true;
                return;
            }

            pageItems.forEach(item => {
                const tr = document.createElement('tr');
                const dateParts = item.date.split('-');
                const formattedDate = `${dateParts[1]}/${dateParts[2]}/${dateParts[0]}`;

                tr.innerHTML = `
                    <td>${item.sale_id}</td>
                    <td>${formattedDate}</td>
                    <td class="font-medium text-pink">${item.brand}</td>
                    <td>${item.product_type}</td>
                    <td>${item.country}</td>
                    <td><span class="badge badge-channel">${item.sales_channel}</span></td>
                    <td><span class="badge badge-payment">${item.payment_method}</span></td>
                    <td class="text-right">$${parseFloat(item.price_usd).toFixed(2)}</td>
                    <td class="text-right font-medium">${item.units_sold}</td>
                    <td class="text-right font-semibold text-gold">$${parseFloat(item.revenue_usd).toFixed(2)}</td>
                `;
                tableBody.appendChild(tr);
            });

            // Update pagination text
            const displayStart = startIdx + 1;
            const displayEnd = Math.min(endIdx, filteredTransactions.length);
            document.getElementById('pagination-info').textContent = 
                `Menampilkan ${displayStart} - ${displayEnd} dari ${filteredTransactions.length} baris`;

            // Enable/disable buttons
            document.getElementById('btn-prev-page').disabled = (currentPage === 1);
            const maxPage = Math.ceil(filteredTransactions.length / rowsPerPage);
            document.getElementById('btn-next-page').disabled = (currentPage >= maxPage || maxPage === 0);
        }

        // --- DATA MINING FUNCTIONS ---
        function switchTab(tab) {
            const btnAnalytics = document.getElementById('btn-tab-analytics');
            const btnMining = document.getElementById('btn-tab-mining');
            const viewAnalytics = document.getElementById('view-analytics');
            const viewMining = document.getElementById('view-mining');

            if (tab === 'analytics') {
                btnAnalytics.classList.add('active');
                btnMining.classList.remove('active');
                viewAnalytics.style.display = 'block';
                viewMining.style.display = 'none';
            } else {
                btnAnalytics.classList.remove('active');
                btnMining.classList.add('active');
                viewAnalytics.style.display = 'none';
                viewMining.style.display = 'block';
                fetchMiningData();
            }
        }

        function fetchMiningData() {
            const summaryGrid = document.getElementById('mining-summary-grid');
            summaryGrid.innerHTML = '<div style="color: var(--text-secondary); text-align: center; grid-column: 1/-1; padding: 2rem;">🔄 Menjalankan Algoritma K-Means di database MySQL... Mohon tunggu...</div>';

            fetch('/api/data-mining/k-means')
                .then(response => response.json())
                .then(data => {
                    summaryGrid.innerHTML = '';
                    
                    const clusterColors = ['#e0a899', '#d4af37', '#7a2048'];
                    const clusterClasses = ['bg-pink', 'bg-gold', 'bg-purple'];
                    
                    // 1. Render Centroid Summary Cards
                    data.summaries.forEach((summary, idx) => {
                        const card = document.createElement('div');
                        card.className = 'metric-card';
                        card.style.flexDirection = 'column';
                        card.style.alignItems = 'flex-start';
                        card.style.gap = '0.75rem';
                        card.style.padding = '1.5rem';
                        
                        card.innerHTML = `
                            <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
                                <span class="badge ${clusterClasses[idx]}" style="font-weight: bold; font-size: 0.8rem; padding: 0.3rem 0.7rem;">${summary.name}</span>
                                <span style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 600;">${summary.count} Transaksi</span>
                            </div>
                            <p style="font-size: 0.85rem; color: var(--text-secondary); margin: 0.1rem 0; line-height: 1.5; text-indent: 0; text-align: left;">${summary.desc}</p>
                            <div style="margin-top: 0.5rem; font-size: 0.85rem; width: 100%; border-top: 1px solid rgba(224, 168, 153, 0.1); padding-top: 0.6rem; line-height: 1.6;">
                                <div>Rerata Harga: <strong class="text-gold">$${summary.avg_price}</strong></div>
                                <div>Rerata Unit Terbeli: <strong class="text-pink">${summary.avg_units} unit</strong></div>
                                <div style="margin-top: 0.4rem; font-size: 0.75rem; color: var(--text-secondary); border-top: 1px dashed rgba(255,255,255,0.05); padding-top: 0.3rem;">
                                    Brand Utama: <strong>${summary.top_brand}</strong><br>
                                    Produk Terlaris: <strong>${summary.top_product}</strong>
                                </div>
                            </div>
                        `;
                        summaryGrid.appendChild(card);
                    });

                    // 2. Render K-Means Scatter Plot (Splitting into 3 Cluster datasets)
                    if (chartMiningScatter) chartMiningScatter.destroy();

                    const datasets = [];
                    const clusterNames = data.summaries.map(s => s.name);
                    
                    for (let c = 0; c < 3; c++) {
                        const points = data.data
                            .filter(item => item.cluster === c)
                            .map(item => ({
                                x: item.x,
                                y: item.y,
                                brand: item.brand,
                                product_type: item.product_type
                            }));
                        
                        datasets.push({
                            label: clusterNames[c],
                            data: points,
                            backgroundColor: clusterColors[c] + 'a0', // transparency
                            borderColor: clusterColors[c],
                            pointRadius: 5,
                            pointHoverRadius: 8,
                            borderWidth: 1
                        });
                    }

                    // Add Centroids as a special dataset
                    const centroidPoints = data.summaries.map((s, idx) => ({
                        x: s.avg_price,
                        y: s.avg_units,
                        label: s.name
                    }));

                    datasets.push({
                        label: 'Centroid (◇)',
                        data: centroidPoints,
                        backgroundColor: '#ffffff',
                        borderColor: '#ffffff',
                        pointRadius: 10,
                        pointHoverRadius: 12,
                        pointStyle: 'rectRot', // diamond shape
                        borderWidth: 3,
                        showLine: false
                    });

                    const ctxMining = document.getElementById('chart-mining-scatter').getContext('2d');
                    chartMiningScatter = new Chart(ctxMining, {
                        type: 'scatter',
                        data: {
                            datasets: datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: '#E0E0E0',
                                        font: { family: 'Outfit', size: 10 }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const point = context.raw;
                                            if (context.datasetIndex === 3) {
                                                return `Pusat Centroid - ${point.label}: Harga $${point.x}, Terjual ${point.y} unit`;
                                            }
                                            return `${point.brand} (${point.product_type}): Harga $${point.x}, Terjual ${point.y} unit`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: { 
                                    title: { display: true, text: 'Harga Produk (USD)', color: '#E0E0E0' },
                                    grid: { color: 'rgba(255, 255, 255, 0.05)' }, 
                                    ticks: { color: '#E0E0E0' } 
                                },
                                y: { 
                                    title: { display: true, text: 'Unit Terjual', color: '#E0E0E0' },
                                    grid: { color: 'rgba(255, 255, 255, 0.05)' }, 
                                    ticks: { color: '#E0E0E0' } 
                                }
                            }
                        }
                    });
                })
                .catch(err => {
                    console.error('Error fetching mining data:', err);
                    summaryGrid.innerHTML = '<div style="color: #ff6b6b; text-align: center; grid-column: 1/-1; padding: 2rem;">⚠️ Gagal memproses algoritma data mining. Pastikan database MySQL aktif.</div>';
                });
        }

        // Formatting Helpers
        function formatCurrency(value) {
            return '$' + parseFloat(value).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function formatNumber(value) {
            return parseInt(value).toLocaleString('id-ID');
        }
    </script>
</body>
</html>

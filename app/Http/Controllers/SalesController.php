<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesController extends Controller
{
    /**
     * Display the main dashboard page with initial filter listings.
     */
    public function index()
    {
        // Get unique values for filter dropdowns
        $brands = DB::table('makeup_sales')->distinct()->pluck('brand')->toArray();
        $productTypes = DB::table('makeup_sales')->distinct()->pluck('product_type')->toArray();
        $countries = DB::table('makeup_sales')->distinct()->pluck('country')->toArray();
        $salesChannels = DB::table('makeup_sales')->distinct()->pluck('sales_channel')->toArray();
        $paymentMethods = DB::table('makeup_sales')->distinct()->pluck('payment_method')->toArray();

        // Get min and max date in the dataset
        $minDate = DB::table('makeup_sales')->min('date');
        $maxDate = DB::table('makeup_sales')->max('date');

        return view('dashboard', compact(
            'brands', 
            'productTypes', 
            'countries', 
            'salesChannels', 
            'paymentMethods',
            'minDate',
            'maxDate'
        ));
    }

    /**
     * Get analytics data based on filter parameters (API Endpoint).
     */
    public function getData(Request $request)
    {
        $query = DB::table('makeup_sales');

        // Apply filters
        $this->applyFilters($query, $request);

        // 1. KPI Summary Cards
        $totalRevenue = $query->sum('revenue_usd');
        $totalUnits = $query->sum('units_sold');
        $avgPrice = $query->avg('price_usd') ?? 0;
        $totalSales = $query->count();

        // 2. Bar Chart: Revenue by Brand (or Revenue by Product Type if a single brand is filtered)
        $selectedBrand = $request->input('brand');
        $isSingleBrand = false;
        $singleBrandName = '';
        if ($selectedBrand) {
            if (is_array($selectedBrand)) {
                if (count($selectedBrand) === 1) {
                    $isSingleBrand = true;
                    $singleBrandName = $selectedBrand[0];
                }
            } else {
                $isSingleBrand = true;
                $singleBrandName = $selectedBrand;
            }
        }

        if ($isSingleBrand) {
            $brandData = $query->clone()
                ->select('product_type as brand', DB::raw('SUM(revenue_usd) as revenue'), DB::raw('SUM(units_sold) as units'))
                ->groupBy('product_type')
                ->orderBy('revenue', 'desc')
                ->get();
        } else {
            $brandData = $query->clone()
                ->select('brand', DB::raw('SUM(revenue_usd) as revenue'), DB::raw('SUM(units_sold) as units'))
                ->groupBy('brand')
                ->orderBy('revenue', 'desc')
                ->get();
        }

        // 3. Line Chart: Monthly Revenue Trend
        $monthlyData = $query->clone()
            ->select(DB::raw("DATE_FORMAT(date, '%Y-%m') as month"), DB::raw('SUM(revenue_usd) as revenue'))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Convert YYYY-MM to readable month names (e.g., Jan, Feb)
        $monthlyData = $monthlyData->map(function ($item) {
            $date = Carbon::createFromFormat('Y-m', $item->month);
            $item->month_name = $date->format('M Y');
            return $item;
        });

        // 4. Donut Chart: Revenue by Country
        $countryData = $query->clone()
            ->select('country', DB::raw('SUM(revenue_usd) as revenue'))
            ->groupBy('country')
            ->orderBy('revenue', 'desc')
            ->get();

        // 5. Scatter Plot (Grafik Bebas): Price vs Units Sold
        $scatterData = $query->clone()
            ->select('price_usd as x', 'units_sold as y', 'brand', 'product_type')
            ->get();

        // 6. Raw Data Table (All matching records)
        $transactions = $query->clone()
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'kpis' => [
                'total_revenue' => round($totalRevenue, 2),
                'total_units' => $totalUnits,
                'avg_price' => round($avgPrice, 2),
                'total_sales' => $totalSales
            ],
            'charts' => [
                'brand' => $brandData,
                'monthly' => $monthlyData,
                'country' => $countryData,
                'scatter' => $scatterData
            ],
            'transactions' => $transactions
        ]);
    }

    /**
     * Run K-Means clustering on the sales dataset for data mining.
     */
    public function kMeans(Request $request)
    {
        $records = DB::table('makeup_sales')
            ->select('sale_id', 'brand', 'product_type', 'price_usd', 'units_sold')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->sale_id,
                    'brand' => $item->brand,
                    'product_type' => $item->product_type,
                    'x' => (float)$item->price_usd,
                    'y' => (int)$item->units_sold,
                ];
            })->toArray();

        if (empty($records)) {
            return response()->json(['data' => [], 'summaries' => []]);
        }

        // Normalize data for better clustering (scale X and Y between 0 and 1)
        $minX = min(array_column($records, 'x'));
        $maxX = max(array_column($records, 'x'));
        $minY = min(array_column($records, 'y'));
        $maxY = max(array_column($records, 'y'));

        $rangeX = ($maxX - $minX) ?: 1;
        $rangeY = ($maxY - $minY) ?: 1;

        $normalized = [];
        foreach ($records as $index => $r) {
            $normalized[$index] = [
                'x' => ($r['x'] - $minX) / $rangeX,
                'y' => ($r['y'] - $minY) / $rangeY
            ];
        }

        $k = 3; // 3 clusters: Low, Medium, High Price
        
        // Determinstic spaced-out centroids
        $centroids = [
            ['x' => 0.1, 'y' => 0.2],
            ['x' => 0.5, 'y' => 0.5],
            ['x' => 0.9, 'y' => 0.8]
        ];

        $maxIterations = 50;
        $assignments = [];

        for ($iter = 0; $iter < $maxIterations; $iter++) {
            $newAssignments = [];
            // Assign points to nearest centroid
            foreach ($normalized as $index => $point) {
                $minDist = INF;
                $bestCentroid = 0;
                foreach ($centroids as $cIdx => $c) {
                    $dist = sqrt(pow($point['x'] - $c['x'], 2) + pow($point['y'] - $c['y'], 2));
                    if ($dist < $minDist) {
                        $minDist = $dist;
                        $bestCentroid = $cIdx;
                    }
                }
                $newAssignments[$index] = $bestCentroid;
            }

            if ($assignments === $newAssignments) {
                break;
            }
            $assignments = $newAssignments;

            // Recalculate centroids
            $sums = array_fill(0, $k, ['x' => 0, 'y' => 0, 'count' => 0]);
            foreach ($normalized as $index => $point) {
                $cIdx = $assignments[$index];
                $sums[$cIdx]['x'] += $point['x'];
                $sums[$cIdx]['y'] += $point['y'];
                $sums[$cIdx]['count']++;
            }

            foreach ($centroids as $cIdx => &$c) {
                if ($sums[$cIdx]['count'] > 0) {
                    $c['x'] = $sums[$cIdx]['x'] / $sums[$cIdx]['count'];
                    $c['y'] = $sums[$cIdx]['y'] / $sums[$cIdx]['count'];
                }
            }
        }

        // Group data and calculate cluster properties
        $clusteredData = [];
        $clusterSummaries = array_fill(0, $k, [
            'count' => 0,
            'avg_price' => 0,
            'avg_units' => 0,
            'brands' => [],
            'products' => []
        ]);

        foreach ($records as $index => $r) {
            $cIdx = $assignments[$index];
            $r['cluster'] = $cIdx;
            $clusteredData[] = $r;

            $clusterSummaries[$cIdx]['count']++;
            $clusterSummaries[$cIdx]['avg_price'] += $r['x'];
            $clusterSummaries[$cIdx]['avg_units'] += $r['y'];
            
            $clusterSummaries[$cIdx]['brands'][$r['brand']] = ($clusterSummaries[$cIdx]['brands'][$r['brand']] ?? 0) + 1;
            $clusterSummaries[$cIdx]['products'][$r['product_type']] = ($clusterSummaries[$cIdx]['products'][$r['product_type']] ?? 0) + 1;
        }

        // Finalize summaries
        foreach ($clusterSummaries as $cIdx => &$summary) {
            if ($summary['count'] > 0) {
                $summary['avg_price'] = round($summary['avg_price'] / $summary['count'], 2);
                $summary['avg_units'] = round($summary['avg_units'] / $summary['count'], 1);
                
                arsort($summary['brands']);
                arsort($summary['products']);
                
                $summary['top_brand'] = key($summary['brands']);
                $summary['top_product'] = key($summary['products']);
            }
            
            // Name the clusters based on average price
            if ($summary['avg_price'] < 40) {
                $summary['name'] = 'Cluster 1: Segmen Ekonomis (Budget)';
                $summary['desc'] = 'Merupakan kumpulan transaksi untuk produk-produk kecantikan dengan harga terjangkau (rata-rata $' . $summary['avg_price'] . '). Menunjukkan kelompok pelanggan yang sangat sensitif terhadap harga.';
            } elseif ($summary['avg_price'] > 80) {
                $summary['name'] = 'Cluster 3: Segmen Premium (Luks)';
                $summary['desc'] = 'Merupakan kumpulan transaksi untuk kosmetik kelas atas dengan harga mahal (rata-rata $' . $summary['avg_price'] . '). Kelompok konsumen di segmen ini cenderung mengutamakan brand prestige dibanding harga.';
            } else {
                $summary['name'] = 'Cluster 2: Segmen Menengah (Mid-Range)';
                $summary['desc'] = 'Merupakan kumpulan transaksi untuk produk kosmetik dengan harga rata-rata menengah (rata-rata $' . $summary['avg_price'] . '). Memiliki volume pembelian unit yang relatif stabil.';
            }
        }

        return response()->json([
            'data' => $clusteredData,
            'summaries' => $clusterSummaries
        ]);
    }

    /**
     * Export the analytical report to PDF.
     */
    public function exportPdf(Request $request)
    {
        $query = DB::table('makeup_sales');
        
        // Apply filters
        $this->applyFilters($query, $request);

        // Fetch all filtered records for calculations
        $records = $query->clone()->orderBy('date', 'asc')->get();

        if ($records->isEmpty()) {
            return response()->json(['error' => 'No data available for the selected filters.'], 404);
        }

        // 1. KPI Summary
        $kpis = [
            'total_revenue' => $records->sum('revenue_usd'),
            'total_units' => $records->sum('units_sold'),
            'avg_price' => $records->avg('price_usd'),
            'total_sales' => $records->count(),
        ];

        // 2. Brand Breakdown
        $brandsBreakdown = $records->groupBy('brand')->map(function ($items, $brand) {
            return [
                'name' => $brand,
                'revenue' => $items->sum('revenue_usd'),
                'units' => $items->sum('units_sold'),
                'avg_price' => $items->avg('price_usd'),
                'count' => $items->count()
            ];
        })->sortByDesc('revenue');

        // 3. Product Type Breakdown
        $productsBreakdown = $records->groupBy('product_type')->map(function ($items, $product) {
            return [
                'name' => $product,
                'revenue' => $items->sum('revenue_usd'),
                'units' => $items->sum('units_sold'),
                'avg_price' => $items->avg('price_usd')
            ];
        })->sortByDesc('revenue');

        // 4. Country Breakdown
        $countriesBreakdown = $records->groupBy('country')->map(function ($items, $country) {
            return [
                'name' => $country,
                'revenue' => $items->sum('revenue_usd'),
                'share' => 0 // will calculate below
            ];
        })->sortByDesc('revenue');

        $totalRevenue = $kpis['total_revenue'];
        foreach ($countriesBreakdown as &$c) {
            $c['share'] = $totalRevenue > 0 ? ($c['revenue'] / $totalRevenue) * 100 : 0;
        }

        // 5. Monthly Trend
        $monthlyTrend = $records->groupBy(function ($item) {
            return Carbon::parse($item->date)->format('M Y');
        })->map(function ($items) {
            return $items->sum('revenue_usd');
        });

        // 6. Descriptive Statistics for Price
        $prices = $records->pluck('price_usd')->toArray();
        sort($prices);
        $count = count($prices);
        
        $minPrice = $prices[0];
        $maxPrice = $prices[$count - 1];
        $meanPrice = array_sum($prices) / $count;
        
        // Median
        $middle = floor($count / 2);
        $medianPrice = ($count % 2 === 0) ? ($prices[$middle - 1] + $prices[$middle]) / 2 : $prices[$middle];
        
        // Standard Deviation
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

        // 7. Data Cleaning Before/After stats
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

        // Build PDF Laporan
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

        return $pdf->download('Laporan_Project_Analitik_Data.pdf');
    }

    /**
     * Helper to apply requests filters.
     */
    private function applyFilters($query, Request $request)
    {
        if ($request->filled('brand')) {
            $query->whereIn('brand', is_array($request->brand) ? $request->brand : [$request->brand]);
        }
        if ($request->filled('product_type')) {
            $query->whereIn('product_type', is_array($request->product_type) ? $request->product_type : [$request->product_type]);
        }
        if ($request->filled('country')) {
            $query->whereIn('country', is_array($request->country) ? $request->country : [$request->country]);
        }
        if ($request->filled('sales_channel')) {
            $query->whereIn('sales_channel', is_array($request->sales_channel) ? $request->sales_channel : [$request->sales_channel]);
        }
        if ($request->filled('payment_method')) {
            $query->whereIn('payment_method', is_array($request->payment_method) ? $request->payment_method : [$request->payment_method]);
        }
        if ($request->filled('date_start')) {
            $query->where('date', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->where('date', '<=', $request->date_end);
        }
    }
}

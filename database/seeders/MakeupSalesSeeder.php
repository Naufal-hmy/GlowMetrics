<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MakeupSalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = base_path('makeup_sales_dataset_2025.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error("CSV file not found at: {$csvFile}");
            return;
        }

        $handle = fopen($csvFile, 'r');
        $header = fgetcsv($handle, 1000, ',');

        $count = 0;
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $row = array_combine($header, $data);
            
            // --- DATA CLEANING PIPELINE ---
            
            // 1. Clean and Standardize Date
            // Raw date format: MM/DD/YYYY (e.g., "11/24/2025")
            // Target date format: YYYY-MM-DD
            $rawDate = trim($row['Date']);
            try {
                $formattedDate = Carbon::createFromFormat('m/d/Y', $rawDate)->format('Y-m-d');
            } catch (\Exception $e) {
                $formattedDate = Carbon::parse($rawDate)->format('Y-m-d');
            }

            // 2. Trim string inputs and sanitize
            $brand = trim($row['Brand']);
            $productType = trim($row['Product_Type']);
            $country = trim($row['Country']);
            $salesChannel = trim($row['Sales_Channel']);
            $paymentMethod = trim($row['Payment_Method']);

            // 3. Numeric Casting & Integrity Validation
            $saleId = (int)$row['Sale_ID'];
            $priceUsd = (float)$row['Price_USD'];
            $unitsSold = (int)$row['Units_Sold'];
            
            // Recalculate and round revenue to ensure absolute data consistency
            $revenueUsd = round($priceUsd * $unitsSold, 2);

            // 4. Insert clean data into DB
            DB::table('makeup_sales')->insert([
                'sale_id' => $saleId,
                'date' => $formattedDate,
                'brand' => $brand,
                'product_type' => $productType,
                'country' => $country,
                'sales_channel' => $salesChannel,
                'payment_method' => $paymentMethod,
                'price_usd' => $priceUsd,
                'units_sold' => $unitsSold,
                'revenue_usd' => $revenueUsd,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $count++;
        }

        fclose($handle);
        $this->command->info("Successfully cleaned and seeded {$count} records into 'makeup_sales' table.");
    }
}

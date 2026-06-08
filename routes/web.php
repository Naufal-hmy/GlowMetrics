<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SalesController;

Route::get('/', [SalesController::class, 'index']);
Route::get('/api/sales-data', [SalesController::class, 'getData']);
Route::get('/api/data-mining/k-means', [SalesController::class, 'kMeans']);
Route::get('/export-pdf', [SalesController::class, 'exportPdf']);

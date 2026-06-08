<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('makeup_sales', function (Blueprint $table) {
            $table->id();
            $table->integer('sale_id')->unique();
            $table->date('date');
            $table->string('brand');
            $table->string('product_type');
            $table->string('country');
            $table->string('sales_channel');
            $table->string('payment_method');
            $table->decimal('price_usd', 8, 2);
            $table->integer('units_sold');
            $table->decimal('revenue_usd', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('makeup_sales');
    }
};

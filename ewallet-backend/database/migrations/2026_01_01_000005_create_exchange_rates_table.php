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
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('from_currency', 10);
            $table->string('to_currency', 10);
            $table->decimal('rate', 15, 6);
            $table->decimal('buy_rate', 15, 6)->nullable();
            $table->decimal('sell_rate', 15, 6)->nullable();
            $table->decimal('custom_fee_percent', 5, 2)->nullable();
            $table->decimal('min_exchange_amount', 15, 2)->nullable();
            $table->decimal('max_exchange_amount', 15, 2)->nullable();
            $table->string('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['from_currency', 'to_currency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};

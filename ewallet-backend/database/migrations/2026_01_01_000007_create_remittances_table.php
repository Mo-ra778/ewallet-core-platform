<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('remittances', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Unique Remittance Identifier & Security PIN Code
            $table->string('remittance_code', 20)->unique();
            $table->string('pin_code', 6);

            // Sender Information (Can be a registered user or agent)
            $table->uuid('sender_id')->nullable()->index();
            $table->string('sender_type', 20)->default('user'); // 'user', 'agent', 'admin'
            $table->string('sender_name', 150);
            $table->string('sender_phone', 30);

            // Unregistered Recipient Information
            $table->string('recipient_name', 150);
            $table->string('recipient_phone', 30)->index();
            $table->string('recipient_id_type', 50)->nullable(); // National ID, Passport, Driving License
            $table->string('recipient_id_number', 50)->nullable();

            // Financials
            $table->decimal('amount', 15, 2);
            $table->decimal('fee', 15, 2)->default(0.00);
            $table->decimal('agent_commission', 15, 2)->default(0.00);
            $table->string('currency', 10)->default('YER');

            // Status & Lifecycle: 'pending', 'paid', 'cancelled', 'refunded'
            $table->enum('status', ['pending', 'paid', 'cancelled', 'refunded'])->default('pending')->index();

            // Agent Payout Tracking
            $table->uuid('paid_by_agent_id')->nullable()->index();
            $table->timestamp('paid_at')->nullable();

            // Administrative & Notes
            $table->text('notes')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('paid_by_agent_id')->references('id')->on('agents')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remittances');
    }
};

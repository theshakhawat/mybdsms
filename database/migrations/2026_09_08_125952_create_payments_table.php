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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->string('payment_ref_id')->nullable()->index();
            $table->string('trx_id')->nullable()->index();
            $table->string('gateway')->default('nagad');
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('charge', 12, 2)->default(0);
            $table->string('currency')->default('BDT');
            $table->string('status')->default('pending'); // pending, success, failed, cancelled
            $table->json('response_data')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

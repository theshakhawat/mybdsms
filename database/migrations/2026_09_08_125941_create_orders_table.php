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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('package_id')->nullable()->constrained('pricing_plans')->onDelete('set null');
            $table->string('order_number')->unique();
            $table->string('package_name');
            $table->string('plan_type')->nullable(); // masking, non-masking etc.
            $table->decimal('price_per_sms', 10, 4)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->bigInteger('sms_count')->default(0);
            $table->string('payment_method')->default('nagad');
            $table->string('payment_status')->default('pending'); // pending, completed, failed, cancelled
            $table->string('status')->default('pending'); // pending, processing, completed, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
